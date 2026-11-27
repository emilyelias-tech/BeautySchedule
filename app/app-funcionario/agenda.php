
<?php
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = database();

date_default_timezone_set('America/Sao_Paulo');

// =====================================
// NAVEGAÇÃO ENTRE OS DIAS
// =====================================

$semana = isset($_GET['semana']) ? (int) $_GET['semana'] : 0;

// Cada navegação avança ou volta cinco dias
$inicio = new DateTime('today');

if ($semana !== 0) {
    $inicio->modify(($semana * 5) . ' days');
}

$fim = clone $inicio;
$fim->modify('+4 days');

$dataInicio = $inicio->format('Y-m-d');
$dataFim = $fim->format('Y-m-d');

// =====================================
// NOMES DOS DIAS E MESES
// =====================================

$dias = [
    'Monday' => 'SEG',
    'Tuesday' => 'TER',
    'Wednesday' => 'QUA',
    'Thursday' => 'QUI',
    'Friday' => 'SEX',
    'Saturday' => 'SÁB',
    'Sunday' => 'DOM'
];

$meses = [
    1 => 'janeiro',
    2 => 'fevereiro',
    3 => 'março',
    4 => 'abril',
    5 => 'maio',
    6 => 'junho',
    7 => 'julho',
    8 => 'agosto',
    9 => 'setembro',
    10 => 'outubro',
    11 => 'novembro',
    12 => 'dezembro'
];

// Título do período
if ($inicio->format('m') === $fim->format('m')) {
    $tituloPeriodo = $inicio->format('d') . ' — ' .
        $fim->format('d') . ' de ' .
        $meses[(int) $fim->format('m')];
} else {
    $tituloPeriodo = $inicio->format('d') . ' de ' .
        $meses[(int) $inicio->format('m')] . ' — ' .
        $fim->format('d') . ' de ' .
        $meses[(int) $fim->format('m')];
}

// =====================================
// BUSCAR AGENDAMENTOS
// =====================================

$stmt = $pdo->prepare("
    SELECT
        a.id_agendamento,
        a.data,
        a.horario,
        a.status,
        c.nome AS nome_cliente,
        p.nome AS nome_procedimento

    FROM agendamentos a

    INNER JOIN clientes c
        ON a.id_cliente = c.id

    INNER JOIN procedimentos p
        ON a.id_procedimento = p.id_procedimento

    WHERE a.data BETWEEN :inicio AND :fim
    AND a.status <> 'Cancelado'

    ORDER BY a.data ASC, a.horario ASC
");

$stmt->execute([
    'inicio' => $dataInicio,
    'fim' => $dataFim
]);

$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================
// ORGANIZAR AGENDAMENTOS POR DIA
// =====================================

$agenda = [];

foreach ($agendamentos as $agendamento) {

    $data = $agendamento['data'];

    if (!isset($agenda[$data])) {
        $agenda[$data] = [];
    }

    $agenda[$data][] = $agendamento;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agenda | Beauty Schedule</title>

    <link rel="stylesheet" href="/app/css/css-func/agenda.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<?php require_once __DIR__ . "/../nav-fun.php" ?>

    <main class="conteudo">

        <!-- CABEÇALHO -->

        <section class="cabecalho">

            <div>

                <p class="identificacao">
                    ORGANIZE SEU DIA
                </p>

                <h1>Agenda</h1>

                <p class="subtitulo">
                    Visualize todos os horários da sua equipe.
                </p>

            </div>

            <a href="novo-horario.php" class="botao-novo">
                <i class="fa-solid fa-plus"></i>
                Novo horário
            </a>

        </section>

        <!-- CALENDÁRIO -->

        <section class="calendario">

            <!-- CABEÇALHO DO CALENDÁRIO -->

            <div class="calendario-topo">

                <a href="agenda.php" class="botao-hoje">
                    Hoje
                </a>

                <h2>
                    <?= htmlspecialchars($tituloPeriodo) ?>
                </h2>

                <div class="navegacao">

                    <a href="agenda.php?semana=<?= $semana - 1 ?>">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    <a href="agenda.php?semana=<?= $semana + 1 ?>">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                </div>

            </div>

            <!-- DIAS DA AGENDA -->

            <div class="dias-agenda">

                <?php for ($i = 0; $i < 5; $i++): ?>

                    <?php

                    $dataDia = clone $inicio;
                    $dataDia->modify("+$i days");

                    $dataFormatada = $dataDia->format('Y-m-d');

                    $nomeDia = $dias[$dataDia->format('l')];

                    $numeroDia = $dataDia->format('d');

                    ?>

                    <div class="dia">

                        <!-- NOME DO DIA -->

                        <div class="dia-titulo">

                            <?= $nomeDia ?> <?= $numeroDia ?>

                        </div>

                        <!-- HORÁRIOS DO DIA -->

                        <div class="horarios">

                            <?php if (!empty($agenda[$dataFormatada])): ?>

                                <?php foreach ($agenda[$dataFormatada] as $item): ?>

                                    <div class="horario-card">

                                        <span class="horario-hora">
                                            <?= date('H:i', strtotime($item['horario'])) ?>
                                        </span>

                                        <strong>
                                            <?= htmlspecialchars($item['nome_cliente']) ?>
                                        </strong>

                                        <p>
                                            <?= htmlspecialchars($item['nome_procedimento']) ?>
                                        </p>

                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <p class="dia-vazio">
                                    Nenhum horário
                                </p>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endfor; ?>

            </div>

        </section>

    </main>

</body>

</html>
