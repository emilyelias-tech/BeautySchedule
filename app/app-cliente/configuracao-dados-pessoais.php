
<?php
session_start();

require_once __DIR__ . '/../init.php';
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['usuario_id'];

$sql = "SELECT * FROM clientes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

function mostrar($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="configuracao-dados-pessoais.css">

    <title>Beauty Schedule</title>
</head>

<body>

<header>
    <div class="logo">
        <h1>BS</h1>

        <div>
            <p>Beauty</p>
            <p>Schedule</p>
        </div>
    </div>
</header>

<main>

    <div class="coluna-esquerda">

        <section class="perfil-section">

            <div class="avatar">
                <?= strtoupper(substr($cliente['nome'], 0, 2)) ?>
            </div>

            <h4><?= mostrar($cliente['nome']) ?></h4>

            <h6><?= mostrar($cliente['email']) ?></h6>

            <div class="stats-box">

                <div class="stat-item">
                    <span class="stat-num">12</span>
                    <span class="stat-desc">Cuidados realizados</span>
                </div>

                <div class="stat-divider"></div>

                <div class="stat-item">
                    <span class="stat-num">4.9</span>
                    <span class="stat-desc">Sua avaliação média</span>
                </div>

            </div>

        </section>

        <section class="nav-perfil">

            <a href="dados-pessoais.php" class="ativo">
                <span>Dados pessoais</span>
                <span>&gt;</span>
            </a>

            <a href="enderecos.php">
                <span>Endereços</span>
                <span>&gt;</span>
            </a>

            <a href="cartoes.php">
                <span>Meus cartões</span>
                <span>&gt;</span>
            </a>

            <a href="configuracao.php">
                <span>Configurações</span>
                <span>&gt;</span>
            </a>

            <a href="historico.php">
                <span>Histórico de pontos</span>
                <span>&gt;</span>
            </a>

        </section>

        <section class="club-card">

            <h4 class="club-title">Beauty club</h4>

            <div class="club-points-box">
                <span class="club-num">680</span>
                <span class="club-label">Pontos</span>
            </div>

            <p class="club-desc">
                Você pode trocar seus pontos por até R$40 de desconto
            </p>

            <a href="resgatar.php" class="club-link">
                Resgatar benefício &rarr;
            </a>

        </section>

    </div>

    <section class="dados-section">

        <p class="subtitulo">Dados pessoais</p>

        <h2>Seu perfil</h2>

        <form class="campo-formulario">

            <label for="nome">Nome completo:</label>
            <input
                type="text"
                id="nome"
                name="nome"
                value="<?= mostrar($cliente['nome']) ?>"
                readonly
            >

            <label for="email">Email:</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= mostrar($cliente['email']) ?>"
                readonly
            >

            <label for="data-nasc">Data de nascimento:</label>
            <input
                type="date"
                id="data-nasc"
                name="data-nascimento"
                value="<?= mostrar($cliente['data_nascimento']) ?>"
                readonly
            >

            <label for="telefone">Telefone:</label>
            <input
                type="text"
                id="telefone"
                name="telefone"
                value="<?= mostrar($cliente['telefone']) ?>"
                readonly
            >

            <label for="cpf">CPF:</label>
            <input
                type="text"
                id="cpf"
                name="cpf"
                value="<?= mostrar($cliente['cpf']) ?>"
                readonly
            >

        </form>

    </section>

</main>

</body>
</html>