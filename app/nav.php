<?php
declare(strict_types=1);

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$isInCliente = str_ends_with($scriptDir, '/app-cliente');
$baseApp = $isInCliente ? '..' : '.';
$baseCliente = $isInCliente ? '.' : 'app-cliente';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="navbar">

    <div class="brand">
        <a href="<?= $baseCliente ?>/index.php">
            <img class="img" src="<?= $baseApp ?>/imagens/logo.png" alt="Beauty Schedule">
        </a>
    </div>

    <nav class="nav-links">
        <a href="<?= $baseCliente ?>/index.php">Início</a>
        <a href="<?= $baseCliente ?>/produtos.php">Produtos</a>
        <a href="<?= $baseCliente ?>/agendar-procedimento.php">Agendar</a>
        <a href="<?= $baseCliente ?>/consulta.php">Consulta</a>
        <a href="<?= $baseCliente ?>/procedimentos.php">Procedimentos</a>
    </nav>

    <div class="nav-actions">

        <a href="<?= $baseCliente ?>/carrinho.php" class="nav-icon">
            <img class="nav-mage" src="<?= $baseApp ?>/imagens/carrinho.png" alt="Carrinho">
        </a>

        <a href="<?= $baseCliente ?>/configuracao-dados-pessoais.php" class="nav-icon">
            <img class="nav-mage" src="<?= $baseApp ?>/imagens/perfil.png" alt="Perfil">
        </a>

    </div>

</div>