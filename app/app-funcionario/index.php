
<?php

require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = database();

// ID do funcionário que está logado
$funcionarioId = $_SESSION['funcionario_id'] ?? 0;
$funcionarioId = (int) $funcionarioId;

// Buscar funcionário
$stmt = $pdo->prepare("
    SELECT nome
    FROM funcionarios
    WHERE id = :id
");

$stmt->execute(['id' => $funcionarioId]);
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

$nomeFuncionario = $funcionario['nome'] ?? 'Funcionário';

// Data atual
$dias = [
    'Sunday' => 'DOMINGO',
    'Monday' => 'SEGUNDA-FEIRA',
    'Tuesday' => 'TERÇA-FEIRA',
    'Wednesday' => 'QUARTA-FEIRA',
    'Thursday' => 'QUINTA-FEIRA',
    'Friday' => 'SEXTA-FEIRA',
    'Saturday' => 'SÁBADO'
];

$meses = [
    1 => 'JANEIRO',
    2 => 'FEVEREIRO',
    3 => 'MARÇO',
    4 => 'ABRIL',
    5 => 'MAIO',
    6 => 'JUNHO',
    7 => 'JULHO',
    8 => 'AGOSTO',
    9 => 'SETEMBRO',
    10 => 'OUTUBRO',
    11 => 'NOVEMBRO',
    12 => 'DEZEMBRO'
];

$dataHoje = $dias[date('l')] . ', ' . date('d') . ' DE ' . $meses[(int)date('m')];

$horaAtual = date('H');

// Saudação
if ($horaAtual < 12) {
    $saudacao = 'Bom dia';
} elseif ($horaAtual < 18) {
    $saudacao = 'Boa tarde';
} else {
    $saudacao = 'Boa noite';
}

// =====================================
// AGENDAMENTOS DE HOJE
// =====================================

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM agendamentos
    WHERE id_funcionario = :id
    AND data = CURDATE()
    AND status <> 'Cancelado'
");

$stmt->execute(['id' => $funcionarioId]);

$agendamentosHoje = (int) $stmt->fetchColumn();

// =====================================
// PRÓXIMO ATENDIMENTO
// =====================================

$stmt = $pdo->prepare("
    SELECT
        a.horario,
        c.nome AS cliente

    FROM agendamentos a

    INNER JOIN clientes c
        ON a.id_cliente = c.id

    WHERE a.id_funcionario = :id
    AND CONCAT(a.data, ' ', a.horario) >= NOW()
    AND a.status <> 'Cancelado'

    ORDER BY a.data ASC, a.horario ASC

    LIMIT 1
");

$stmt->execute(['id' => $funcionarioId]);

$proximoAtendimento = $stmt->fetch(PDO::FETCH_ASSOC);

// =====================================
// CLIENTES CADASTRADOS NO MÊS
// =====================================

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM clientes
    WHERE MONTH(data_cadastro) = MONTH(CURDATE())
    AND YEAR(data_cadastro) = YEAR(CURDATE())
");

$clientesMes = (int) $stmt->fetchColumn();

// =====================================
// TOTAL DE PROCEDIMENTOS
// =====================================

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM procedimentos
");

$totalProcedimentos = (int) $stmt->fetchColumn();

// =====================================
// PRÓXIMOS ATENDIMENTOS
// =====================================

$stmt = $pdo->prepare("
    SELECT
        a.data,
        a.horario,
        a.status,
        c.nome AS cliente,
        p.nome AS procedimento

    FROM agendamentos a

    INNER JOIN clientes c
        ON a.id_cliente = c.id

    INNER JOIN procedimentos p
        ON a.id_procedimento = p.id_procedimento

    WHERE a.id_funcionario = :id
    AND CONCAT(a.data, ' ', a.horario) >= NOW()
    AND a.status <> 'Cancelado'

    ORDER BY a.data ASC, a.horario ASC

    LIMIT 4
");

$stmt->execute(['id' => $funcionarioId]);

$listaAgendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =====================================
// GRÁFICO DA SEMANA
// =====================================

// Primeiro dia da semana
$inicioSemana = date('Y-m-d', strtotime('monday this week'));

// Último dia da semana (sábado)
$fimSemana = date('Y-m-d', strtotime('saturday this week'));

// Buscar quantidade de atendimentos por dia
$stmt = $pdo->prepare("
    SELECT
        data,
        COUNT(*) AS total

    FROM agendamentos

    WHERE id_funcionario = :id
    AND data BETWEEN :inicio AND :fim
    AND status <> 'Cancelado'

    GROUP BY data
");

$stmt->execute([
    'id' => $funcionarioId,
    'inicio' => $inicioSemana,
    'fim' => $fimSemana
]);

$resultadoSemana = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar os resultados
$atendimentosSemana = [];

foreach ($resultadoSemana as $resultado) {
    $atendimentosSemana[$resultado['data']] = (int) $resultado['total'];
}

$diasGrafico = ['SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB'];

$dadosGrafico = [];
$totalSemana = 0;

for ($i = 0; $i < 6; $i++) {

    $dataDia = date('Y-m-d', strtotime($inicioSemana . " +$i days"));

    $quantidade = $atendimentosSemana[$dataDia] ?? 0;

    $dadosGrafico[] = [
        'dia' => $diasGrafico[$i],
        'quantidade' => $quantidade
    ];

    $totalSemana += $quantidade;
}

// Maior quantidade para calcular as barras
$maiorQuantidade = 1;

foreach ($dadosGrafico as $dado) {
    if ($dado['quantidade'] > $maiorQuantidade) {
        $maiorQuantidade = $dado['quantidade'];
    }
}

// =====================================
// PREVISÃO DE VALOR DA SEMANA
// =====================================

$stmt = $pdo->prepare("
    SELECT SUM(p.valor)

    FROM agendamentos a

    INNER JOIN procedimentos p
        ON a.id_procedimento = p.id_procedimento

    WHERE a.id_funcionario = :id
    AND a.data BETWEEN :inicio AND :fim
    AND a.status <> 'Cancelado'
");

$stmt->execute([
    'id' => $funcionarioId,
    'inicio' => $inicioSemana,
    'fim' => $fimSemana
]);

$valorSemana = (float) ($stmt->fetchColumn() ?? 0);

// Formatar dinheiro
$valorFormatado = number_format($valorSemana, 2, ',', '.');

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Início | Beauty Schedule</title>

    <link rel="stylesheet" href="/app/css/css-func/index.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php require_once __DIR__ . "/../nav-fun.php" ?>

    <main class="conteudo">

        <!-- CABEÇALHO -->

        <section class="cabecalho">

            <div>
                <p class="data-atual">
                    <?= $dataHoje ?>
                </p>

                <h1>
                    <?= $saudacao ?>, <?= htmlspecialchars($nomeFuncionario) ?>.
                </h1>

                <p class="subtitulo">
                    Veja o que está acontecendo no seu espaço hoje.
                </p>
            </div>

            <a href="agenda.php" class="botao-agendamento">
                <i class="fa-solid fa-plus"></i>
                Novo agendamento
            </a>

        </section>

        <!-- CARTÕES DE INFORMAÇÕES -->

        <section class="cartoes">

            <div class="cartao">

                <div class="icone">
                    <i class="fa-regular fa-calendar"></i>
                </div>

                <div class="informacoes">
                    <p>Agendamentos hoje</p>

                    <h2><?= $agendamentosHoje ?></h2>

                    <span>Atendimentos do dia</span>
                </div>

            </div>

            <div class="cartao">

                <div class="icone">
                    <i class="fa-regular fa-clock"></i>
                </div>

                <div class="informacoes">
                    <p>Próximo atendimento</p>

                    <h2>
                        <?php if ($proximoAtendimento): ?>
                            <?= date('H:i', strtotime($proximoAtendimento['horario'])) ?>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </h2>

                    <span>
                        <?= $proximoAtendimento
                            ? htmlspecialchars($proximoAtendimento['cliente'])
                            : 'Nenhum agendamento' ?>
                    </span>
                </div>

            </div>

            <div class="cartao">

                <div class="icone">
                    <i class="fa-solid fa-user-plus"></i>
                </div>

                <div class="informacoes">
                    <p>Clientes no mês</p>

                    <h2><?= $clientesMes ?></h2>

                    <span>Novos cadastros</span>
                </div>

            </div>

            <div class="cartao">

                <div class="icone">
                    <i class="fa-solid fa-sparkles"></i>
                </div>

                <div class="informacoes">
                    <p>Procedimentos cadastrados</p>

                    <h2><?= $totalProcedimentos ?></h2>

                    <span>Total disponível no sistema</span>
                </div>

            </div>

        </section>

        <!-- ÁREA INFERIOR -->

        <section class="area-inferior">

            <!-- PRÓXIMOS ATENDIMENTOS -->

            <div class="painel agenda-painel">

                <div class="titulo-painel">

                    <div>
                        <p class="identificacao">AGENDA DE HOJE</p>

                        <h2>Próximos atendimentos</h2>
                    </div>

                    <a href="agenda.php">
                        Ver agenda
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>

                </div>

                <div class="lista-agendamentos">

                    <?php if (count($listaAgendamentos) > 0): ?>

                        <?php foreach ($listaAgendamentos as $agendamento): ?>

                            <?php
                            $status = $agendamento['status'];

                            $classeStatus = 'pendente';

                            if ($status === 'Confirmado') {
                                $classeStatus = 'confirmado';
                            } elseif ($status === 'Concluído') {
                                $classeStatus = 'concluido';
                            }
                            ?>

                            <div class="agendamento">

                                <div class="horario">
                                    <?= date('H:i', strtotime($agendamento['horario'])) ?>
                                </div>

                                <div class="ponto"></div>

                                <div class="detalhes">

                                    <strong>
                                        <?= htmlspecialchars($agendamento['cliente']) ?>
                                    </strong>

                                    <span>
                                        <?= htmlspecialchars($agendamento['procedimento']) ?>
                                    </span>

                                    <small>
                                        <?= date('d/m', strtotime($agendamento['data'])) ?>
                                    </small>

                                </div>

                                <span class="status <?= $classeStatus ?>">
                                    <?= htmlspecialchars($status) ?>
                                </span>

                                <i class="fa-solid fa-chevron-right seta"></i>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <p class="sem-agendamentos">
                            Você não possui próximos atendimentos.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </section>

    </main>

</body>
</html>
