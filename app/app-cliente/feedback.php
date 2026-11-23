<?php
session_start();
require_once __DIR__ . "/../init.php";

$id_cliente = $_SESSION['id_cliente'] ?? $_SESSION['usuario']['id'] ?? 1;
$id_agendamento = $_GET['id'] ?? null;

$mensagem = '';
$erro = '';

$agendamento = null;

if ($id_agendamento) {
    $stmt = $pdo->prepare("
        SELECT a.*, p.nome AS nome_procedimento, f.nome AS nome_profissional
        FROM agendamentos a
        INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
        INNER JOIN funcionarios f ON a.id_funcionario = f.id
        WHERE a.id_agendamento = :id
    ");
    $stmt->execute([':id' => $id_agendamento]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("
        SELECT a.*, p.nome AS nome_procedimento, f.nome AS nome_profissional
        FROM agendamentos a
        INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
        INNER JOIN funcionarios f ON a.id_funcionario = f.id
        WHERE a.id_cliente = :id_cliente
        ORDER BY a.id_agendamento DESC
        LIMIT 1
    ");
    $stmt->execute([':id_cliente' => $id_cliente]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$agendamento) {
    $agendamento = [
        'id_agendamento'    => 1,
        'nome_procedimento' => 'Drenagem linfática',
        'nome_profissional' => 'Laura Martins'
    ];
}

$feedbackExistente = null;
try {
    $stmtFb = $pdo->prepare("SELECT * FROM feedback WHERE id_agendamento = :id");
    $stmtFb->execute([':id' => $agendamento['id_agendamento']]);
    $feedbackExistente = $stmtFb->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

if (isset($_POST['enviar_avaliacao'])) {
    $notaServico = (int)($_POST['nota_servico'] ?? 0);
    $notaProfissional = (int)($_POST['nota_profissional'] ?? 0);
    $comentario = trim($_POST['descricao'] ?? '');

    if ($notaServico < 1 || $notaProfissional < 1) {
        $erro = "Por favor, selecione as estrelas para qualidade do serviço e atendimento.";
    } else {
        $notaMedia = (int)round(($notaServico + $notaProfissional) / 2);

        try {
            if ($feedbackExistente) {
                $stmtUp = $pdo->prepare("
                    UPDATE feedback 
                    SET nota = :nota, descricao = :descricao 
                    WHERE id_agendamento = :id_agendamento
                ");
                $stmtUp->execute([
                    ':nota'           => $notaMedia,
                    ':descricao'      => $comentario,
                    ':id_agendamento' => $agendamento['id_agendamento']
                ]);
            } else {
                $stmtIn = $pdo->prepare("
                    INSERT INTO feedback (id_agendamento, nota, descricao) 
                    VALUES (:id_agendamento, :nota, :descricao)
                ");
                $stmtIn->execute([
                    ':id_agendamento' => $agendamento['id_agendamento'],
                    ':nota'           => $notaMedia,
                    ':descricao'      => $comentario
                ]);
            }

            $mensagem = "Avaliação enviada com sucesso! Obrigado pelo seu feedback.";
            $feedbackExistente = [
                'nota'      => $notaMedia,
                'descricao' => $comentario
            ];
        } catch (Exception $e) {
            $erro = "Não foi possível salvar a avaliação: " . $e->getMessage();
        }
    }
}

$notaAtualServico = $_POST['nota_servico'] ?? ($feedbackExistente['nota'] ?? 0);
$notaAtualProf = $_POST['nota_profissional'] ?? ($feedbackExistente['nota'] ?? 0);
$descricaoAtual = $_POST['descricao'] ?? ($feedbackExistente['descricao'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Atendimento - BeautySchedule</title>
    <link rel="stylesheet" href="css/consulta.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php" ?>

    <main>
        <div class="container container-avaliacao">

            <div class="card-header">
                <div class="titulo-grupo">
                    <span class="subtitulo">Sua experiencia</span>
                    <h2>Como foi seu momento?</h2>
                    <p class="subtitulo-servico">
                        <?= htmlspecialchars($agendamento['nome_procedimento']) ?> com <?= htmlspecialchars($agendamento['nome_profissional']) ?>
                    </p>
                </div>

                <a href="meus-agendamentos.php" class="btn-fechar" title="Fechar">x</a>
            </div>

            <?php if (!empty($mensagem)): ?>
                <p class="mensagem-sucesso"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form method="POST" class="form-avaliacao">

                <div class="bloco-estrelas">
                    <div class="estrelas-texto">
                        <h3>Qualidade do serviço</h3>
                        <p>o resultado atendeu às suas espectativas?</p>
                    </div>

                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input 
                                type="radio" 
                                id="servico-<?= $i ?>" 
                                name="nota_servico" 
                                value="<?= $i ?>"
                                <?= ((int)$notaAtualServico === $i) ? 'checked' : '' ?>
                            >
                            <label for="servico-<?= $i ?>" title="<?= $i ?> estrela(s)">
                                <svg width="24" height="24" viewBox="0 0 24 24" stroke="#485136" stroke-width="1.3" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="bloco-estrelas bloco-estrelas-ultimo">
                    <div class="estrelas-texto">
                        <h3>Atendimento profissional</h3>
                        <p>Como voce se sentiu durante o cuidado?</p>
                    </div>

                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input 
                                type="radio" 
                                id="prof-<?= $i ?>" 
                                name="nota_profissional" 
                                value="<?= $i ?>"
                                <?= ((int)$notaAtualProf === $i) ? 'checked' : '' ?>
                            >
                            <label for="prof-<?= $i ?>" title="<?= $i ?> estrela(s)">
                                <svg width="24" height="24" viewBox="0 0 24 24" stroke="#485136" stroke-width="1.3" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="campo-observacao">
                    <label for="descricao" class="label-observacao">Conte um pouco mais</label>
                    <textarea 
                        name="descricao" 
                        id="descricao" 
                        class="textarea-observacao textarea-avaliacao" 
                        placeholder="Seu comentario ajuda a melhorar cada experiencia..."
                    ><?= htmlspecialchars($descricaoAtual) ?></textarea>
                </div>

                <div class="acoes-avaliacao">
                    <button type="submit" name="enviar_avaliacao" class="btn-enviar-avaliacao">
                        Enviar avaliação
                    </button>
                </div>

            </form>

        </div>
    </main>

</body>
</html>