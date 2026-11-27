
<?php
require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = database();

$busca = trim($_GET['busca'] ?? '');

$sql = "
    SELECT
        c.id,
        c.nome,
        c.telefone,
        COUNT(a.id_agendamento) AS total_atendimentos,
        MAX(a.data) AS ultima_visita

    FROM clientes c

    LEFT JOIN agendamentos a
        ON a.id_cliente = c.id
        AND a.status <> 'Cancelado'

    WHERE (
        c.nome LIKE :buscaNome
        OR c.telefone LIKE :buscaTelefone
    )

    GROUP BY c.id, c.nome, c.telefone

    ORDER BY c.nome ASC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    'buscaNome' => '%' . $busca . '%',
    'buscaTelefone' => '%' . $busca . '%'
]);

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [
    1 => 'Jan',
    2 => 'Fev',
    3 => 'Mar',
    4 => 'Abr',
    5 => 'Mai',
    6 => 'Jun',
    7 => 'Jul',
    8 => 'Ago',
    9 => 'Set',
    10 => 'Out',
    11 => 'Nov',
    12 => 'Dez'
];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Clientes | Beauty Schedule</title>

    <link rel="stylesheet" href="/app/css/css-func/clientes.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <?php require_once __DIR__ . "/../nav-fun.php" ?>

    <main class="conteudo">

        <section class="cabecalho">

            <div>

                <p class="identificacao">
                    RELACIONAMENTO
                </p>

                <h1>Clientes</h1>

                <p class="subtitulo">
                    Histórico e informações de quem confia no seu trabalho.
                </p>

            </div>

            <a href="novo-cliente.php" class="botao-novo">
                <i class="fa-solid fa-plus"></i>
                Novo cliente
            </a>

        </section>

        <form action="clientes.php" method="GET" class="form-pesquisa">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                name="busca"
                placeholder="Buscar por nome ou telefone..."
                value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>"
            >

        </form>

        <section class="tabela-clientes">

            <div class="tabela-topo">

                <span>CLIENTE</span>

                <span>ATENDIMENTOS</span>

                <span>ÚLTIMA VISITA</span>

                <span>STATUS</span>

            </div>

            <?php if (count($clientes) > 0): ?>

                <?php foreach ($clientes as $cliente): ?>

                    <?php

                    $nome = trim($cliente['nome'] ?? '');

                    $partesNome = preg_split('/\s+/', $nome);

                    $iniciais = '';

                    if (!empty($partesNome[0])) {
                        $iniciais = mb_strtoupper(mb_substr($partesNome[0], 0, 1));
                    }

                    if (count($partesNome) > 1) {
                        $ultimoNome = end($partesNome);
                        $iniciais .= mb_strtoupper(mb_substr($ultimoNome, 0, 1));
                    }

                    $ultimaVisita = '--';

                    if (!empty($cliente['ultima_visita'])) {

                        $data = strtotime($cliente['ultima_visita']);

                        if ($data !== false) {

                            $numeroMes = (int) date('m', $data);

                            $ultimaVisita = date('d', $data) . ' ' . $meses[$numeroMes];
                        }
                    }

                    $totalAtendimentos = (int) $cliente['total_atendimentos'];

                    $status = $totalAtendimentos > 0
                        ? 'Ativa'
                        : 'Sem atendimentos';

                    $classeStatus = $totalAtendimentos > 0
                        ? 'ativa'
                        : 'sem-atendimentos';

                    ?>

                    <div class="linha-cliente">

                        <div class="dados-cliente">

                            <div class="iniciais">
                                <?= htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8') ?>
                            </div>

                            <div class="nome-telefone">

                                <strong>
                                    <?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
                                </strong>

                                <span>
                                    <?= htmlspecialchars($cliente['telefone'] ?? 'Telefone não informado', ENT_QUOTES, 'UTF-8') ?>
                                </span>

                            </div>

                        </div>

                        <div class="total-atendimentos">

                            <?= $totalAtendimentos ?>

                        </div>

                        <div class="ultima-visita">

                            <?= $ultimaVisita ?>

                        </div>

                        <div class="status-cliente">

                            <span class="<?= $classeStatus ?>">
                                <?= $status ?>
                            </span>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="nenhum-cliente">

                    <i class="fa-regular fa-user"></i>

                    <p>
                        Nenhum cliente encontrado.
                    </p>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>
