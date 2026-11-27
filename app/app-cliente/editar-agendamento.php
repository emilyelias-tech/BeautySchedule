<?php
ob_start();
session_start();
require_once __DIR__ . "/../init.php";

$id_agendamento = (int)($_GET['id'] ?? 0);

if ($id_agendamento <= 0) {
    header('Location: meus-agendamento.php');
    exit;
}

$erro = '';

$stmt = $pdo->prepare("SELECT * FROM agendamentos WHERE id_agendamento = :id");
$stmt->execute([':id' => $id_agendamento]);
$agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agendamento) {
    header('Location: meus-agendamento.php');
    exit;
}

$procedimentos = $pdo->query("SELECT id_procedimento, nome, valor, duracao_minutos FROM procedimentos ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
$funcionarios = $pdo->query("SELECT id, nome, cargo FROM funcionarios ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

$horariosDisponiveis = [
    '09:00', '09:30', '10:00', '10:30',
    '11:00', '11:30', '13:30', '14:00',
    '15:00', '16:30', '18:00', '19:30'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_edicao'])) {
    $id_procedimento = (int)($_POST['id_procedimento'] ?? 0);
    $id_funcionario  = (int)($_POST['id_funcionario'] ?? 0);
    $data            = $_POST['data'] ?? '';
    $horario         = $_POST['horario'] ?? '';
    $status          = $_POST['status'] ?? 'Confirmado';
    $observacao      = trim($_POST['observacao'] ?? '');

    if ($id_procedimento <= 0 || $id_funcionario <= 0 || empty($data) || empty($horario)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        $stmtDur = $pdo->prepare("SELECT duracao_minutos FROM procedimentos WHERE id_procedimento = :id");
        $stmtDur->execute([':id' => $id_procedimento]);
        $duracao = (int)($stmtDur->fetchColumn() ?: 60);

        try {
            $temColunaObs = array_key_exists('observacao', $agendamento);

            if ($temColunaObs) {
                $stmtUp = $pdo->prepare("
                    UPDATE agendamentos 
                    SET id_procedimento = :id_procedimento,
                        id_funcionario  = :id_funcionario,
                        data            = :data,
                        horario         = :horario,
                        duracao_minutos = :duracao,
                        status          = :status,
                        observacao      = :observacao
                    WHERE id_agendamento = :id
                ");

                $stmtUp->execute([
                    ':id_procedimento' => $id_procedimento,
                    ':id_funcionario'  => $id_funcionario,
                    ':data'            => $data,
                    ':horario'         => $horario,
                    ':duracao'         => $duracao,
                    ':status'          => $status,
                    ':observacao'      => $observacao,
                    ':id'              => $id_agendamento
                ]);
            } else {
                $stmtUp = $pdo->prepare("
                    UPDATE agendamentos 
                    SET id_procedimento = :id_procedimento,
                        id_funcionario  = :id_funcionario,
                        data            = :data,
                        horario         = :horario,
                        duracao_minutos = :duracao,
                        status          = :status
                    WHERE id_agendamento = :id
                ");

                $stmtUp->execute([
                    ':id_procedimento' => $id_procedimento,
                    ':id_funcionario'  => $id_funcionario,
                    ':data'            => $data,
                    ':horario'         => $horario,
                    ':duracao'         => $duracao,
                    ':status'          => $status,
                    ':id'              => $id_agendamento
                ]);
            }

            if (!headers_sent()) {
                header('Location: meus-agendamento.php?editado=1');
                exit;
            } else {
                echo "<script>window.location.href = 'meus-agendamento.php?editado=1';</script>";
                exit;
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erro = "Conflito de agenda: esta profissional já possui um agendamento nesta data e horário.";
            } else {
                $erro = "Erro ao atualizar agendamento: " . $e->getMessage();
            }
        }
    }

    $agendamento['id_procedimento'] = $id_procedimento;
    $agendamento['id_funcionario']  = $id_funcionario;
    $agendamento['data']            = $data;
    $agendamento['horario']         = $horario;
    $agendamento['status']          = $status;
    $agendamento['observacao']      = $observacao;
}

$horarioAtual = substr($agendamento['horario'], 0, 5);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Agendamento - BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/informacao.css">
</head>
<body>

    <?php require_once __DIR__ . '/../nav-cli.php'; ?>

    <main>
        <div class="container">

            <div class="card-header">
                <div class="titulo-grupo">
                    <span class="subtitulo">Alterar reserva #<?= $id_agendamento ?></span>
                    <h2>Editar agendamento</h2>
                </div>

                <a href="meus-agendamento.php" class="btn-fechar" title="Voltar">x</a>
            </div>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form method="POST" action="editar-agendamento.php?id=<?= $id_agendamento ?>" class="form-editar">

                <div class="campo-form">
                    <label for="id_procedimento">Procedimento</label>
                    <select name="id_procedimento" id="id_procedimento" class="input-editorial" required>
                        <?php foreach ($procedimentos as $proc): ?>
                            <option value="<?= (int)$proc['id_procedimento'] ?>" <?= ((int)$agendamento['id_procedimento'] === (int)$proc['id_procedimento']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($proc['nome']) ?> — R$ <?= number_format((float)$proc['valor'], 2, ',', '.') ?> (<?= (int)$proc['duracao_minutos'] ?> min)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo-form">
                    <label for="id_funcionario">Profissional</label>
                    <select name="id_funcionario" id="id_funcionario" class="input-editorial" required>
                        <?php foreach ($funcionarios as $func): ?>
                            <option value="<?= (int)$func['id'] ?>" <?= ((int)$agendamento['id_funcionario'] === (int)$func['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($func['nome']) ?> (<?= htmlspecialchars($func['cargo']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="linha-dupla-form">
                    <div class="campo-form">
                        <label for="data">Data</label>
                        <input 
                            type="date" 
                            name="data" 
                            id="data" 
                            class="input-editorial" 
                            value="<?= htmlspecialchars($agendamento['data']) ?>" 
                            required
                        >
                    </div>

                    <div class="campo-form">
                        <label for="horario">Horário</label>
                        <select name="horario" id="horario" class="input-editorial" required>
                            <?php if (!in_array($horarioAtual, $horariosDisponiveis)): ?>
                                <option value="<?= htmlspecialchars($horarioAtual) ?>" selected><?= htmlspecialchars($horarioAtual) ?></option>
                            <?php endif; ?>
                            <?php foreach ($horariosDisponiveis as $hora): ?>
                                <option value="<?= $hora ?>" <?= ($horarioAtual === $hora) ? 'selected' : '' ?>>
                                    <?= $hora ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    </select>
                </div>

                <div class="campo-observacao">
                    <label for="observacao" class="label-observacao">Observação</label>
                    <textarea 
                        name="observacao" 
                        id="observacao" 
                        class="textarea-observacao"
                        placeholder="Ex: Preferência de atendimento ou alergias"
                    ><?= htmlspecialchars($agendamento['observacao'] ?? '') ?></textarea>
                </div>

                <div class="acoes-rodape">
                    <a href="meus-agendamento.php" class="btn-cancelar">Voltar sem salvar</a>
                    <button type="submit" name="salvar_edicao" class="btn-salvar">Salvar alterações</button>
                </div>

            </form>

        </div>
    </main>

</body>
</html>