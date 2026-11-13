<?php
session_start();
require_once __DIR__ . "/init.php";

if (!isset($_SESSION['agendamento_atual'])) {
    header('Location: agendar1.php');
    exit;
}

$profissionais = [];
try {
    $profissionais = readAll($pdo, 'profissionais');
} catch (Exception $e) {
}
if (empty($profissionais)) {$profissionais = [
        ['id_profissional' => 1, 'nome' => 'Mariana Lopes'],
        ['id_profissional' => 2, 'nome' => 'Camila Ferreira'],
        ['id_profissional' => 3, 'nome' => 'Juliana Santos']
    ];
}

$datasDisponiveis = [
    '2026-06-18' => '18 Jun',
    '2026-06-19' => '19 Jun',
    '2026-06-20' => '20 Jun',
    '2026-06-21' => '21 Jun'
];

$horariosDisponiveis = [
    '09:00',
    '10:30',
    '13:00',
    '15:30'
];

$erro = '';

if (isset($_POST['continuar'])) {
    $profissional_id =$_POST['profissional'] ?? '';
    $data =$_POST['data'] ?? '';
    $horario =$_POST['horario'] ?? '';

    if (empty($profissional_id) || empty($data) || empty($horario)) {
    $erro = "Por favor, selecione o profissional, a data e o horário.";
} else {

        $nome_profissional = '';
        foreach ($profissionais as $prof) {$id = $prof['id_profissional'] ?? $prof['id'] ?? '';
            if ((string)$id === (string)$profissional_id) {
                $nome_profissional =$prof['nome'];
                break;
            }
        }

        $_SESSION['agendamento_atual']['id_profissional'] =$profissional_id;
        $_SESSION['agendamento_atual']['nome_profissional'] =$nome_profissional;
        $_SESSION['agendamento_atual']['data'] =$data;
        $_SESSION['agendamento_atual']['horario'] =$horario;

        header('Location: agendar3.php');
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
    <link rel="stylesheet" href="css/agendamentos.css">
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

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
                

                <div class="campo-grupo">
                    <label for="profissional" class="label-etapa">Profissional</label>
                    <div class="select-wrapper">
                        <select name="profissional" id="profissional" class="select-custom" required>
                            <?php foreach ($profissionais as $prof):$idProf = $prof['id_profissional'] ?? $prof['id'];
                            ?>
                                <option value="<?= htmlspecialchars($idProf) ?>">
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
                </div>

          
                <div class="campo-grupo">
                    <span class="label-etapa">Horario</span>
                    <div class="grid-opcoes">
                        <?php foreach ($horariosDisponiveis as$hora): ?>
                            <label class="opcao-pill">
                                <input 
                                    type="radio" 
                                    name="horario" 
                                    value="<?= htmlspecialchars($hora) ?>"
                                    <?= $hora == '09:00' ? 'checked' : '' ?>
                                    required
                                >
                                <span class="pill-texto"><?= htmlspecialchars($hora) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            
                <div class="acoes-rodape">
                    <a href="agendar1.php" class="btn-voltar">Voltar</a>
                    <button type="submit" name="continuar" class="btn-continuar">
                        Continuar <span>&rarr;</span>
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>