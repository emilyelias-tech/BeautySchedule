<?php
session_start();
require_once __DIR__ . "/../init.php";

if (!isset($_SESSION['agendamento_atual'])) {
    header('Location: agendar.php');
    exit;
}




$profissionais = [];
try {
    $profissionais = readAll($pdo, 'funcionarios');
} catch (Exception $e) {
}

if (empty($profissionais)) {
    $profissionais = [
        ['id_funcionario' => 1, 'nome' => 'Mariana Lopes'],
        ['id_funcionario' => 2, 'nome' => 'Camila Ferreira'],
        ['id_funcionario' => 3, 'nome' => 'Juliana Santos']
    ];
}

$horariosDisponiveis = [
    '10:30',
    '11:30',
    '13:00',
    '14:00',
    '14:30',
    '15:00',
    '15:30',
    '16:30',
    '17:00',
    

];

$erro = '';

if (isset($_POST['continuar'])) {
    $profissional_id = $_POST['profissional'] ?? '';
    $data = $_POST['data'] ?? '';
    $horario = $_POST['horario'] ?? '';

    if (empty($profissional_id) || empty($data) || empty($horario)) {
        $erro = "Por favor, selecione o profissional, a data e o horário.";
    } else {
        $nome_profissional = '';
        foreach ($profissionais as $prof) {
            $id = $prof['id_funcionario'] ?? $prof['id_profissional'] ?? $prof['id'] ?? '';
            if ((string)$id === (string)$profissional_id) {
                $nome_profissional = $prof['nome'];
                break;
            }
        }

        $_SESSION['agendamento_atual']['id_funcionario'] = $profissional_id;
        $_SESSION['agendamento_atual']['id_profissional'] = $profissional_id;
        $_SESSION['agendamento_atual']['nome_profissional'] = $nome_profissional;
        $_SESSION['agendamento_atual']['data'] = $data;
        $_SESSION['agendamento_atual']['horario'] = $horario;

        header('Location: agendar-horario.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/agendar-profissional.css">
</head>
<body>

    <?php require_once __DIR__ . '/../nav.php'; ?>

    <main>
        <div class="container">
            

    


            <div class="card-header">
                <div class="progresso-wrapper">
                    <div class="progresso-barras">
                        <span class="barra ativa"></span>
                        <span class="barra ativa"></span>
                        <span class="barra"></span>
                    </div>
                    <span class="subtitulo">Agendamento</span>
                </div>

                <a href="index.php" class="btn-fechar" title="Fechar">x</a>
            </div>

            <h2>Com quem e quando?</h2>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form method="POST" class="form-etapa2">

              
                <div class="bloco-agendamento2">
                   
                
                <div class="bloco-agendamento2">
                    
                    
                    <div class="campo-grupo">
                        <label for="profissional" class="label-etapa">Profissional</label>
                        <div class="select-wrapper">
                            <select name="profissional" id="profissional" class="select-custom" required>
                                <?php foreach ($profissionais as $prof): 
                                    $idProf = $prof['id_funcionario'] ?? $prof['id_profissional'] ?? $prof['id'];
                                ?>
                                    <option value="<?= htmlspecialchars($idProf) ?>" <?= (isset($_POST['profissional']) && (string)$_POST['profissional'] === (string)$idProf) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prof['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

    

                    <div class="campo-grupo">
                        <label for="data" class="label-etapa">Data</label>
                        <div class="select-wrapper">
                            <input
                                type="date"
                                name="data"
                                id="data"
                                class="select-custom"
                                min="<?= date('Y-m-d') ?>"
                                value="<?= htmlspecialchars($_POST['data'] ?? '') ?>"
                                required
                            >
                        </div>
                    </div>


                    <div class="campo-grupo campo-horarios">
                        <span class="label-etapa">Horário</span>
                        <div class="grid-opcoes">
                            <?php 
                            $horarioSelecionado = $_POST['horario'] ?? '09:00';
                            foreach ($horariosDisponiveis as $hora): 
                            ?>
                                <label class="opcao-pill">
                                    <input 
                                        type="radio" 
                                        name="horario" 
                                        value="<?= htmlspecialchars($hora) ?>"
                                        <?= $hora === $horarioSelecionado ? 'checked' : '' ?>
                                        required
                                    >
                                    <span class="pill-texto"><?= htmlspecialchars($hora) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>


                 <div class="acoes-rodape">

                <div class="acoes-rodape">
                    <a href="/app//app-cliente/agendar-procedimento.php" class="btn-voltar">Voltar</a>
                    <button type="submit" name="continuar" class="btn-continuar">
                        Continuar
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>