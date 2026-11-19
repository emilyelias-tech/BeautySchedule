<?php
declare(strict_types=1);
?>

<!-- Ícones do carrinho e perfil -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="css/nav.css">

<div class="navbar">

    <div class="brand">
        <img class="img" src="/app/imagens/logo.png" alt="Beauty Schedule">
    </div>

    <nav class="nav-links">
        <a href="index.php">Início</a>
        <a href="produtos.php">Produtos</a>
        <a href="/app/index.php#agendamento">Agendar</a>
        <a href="consulta.php">Consulta</a>
        <a href="procedimentos.php">Procedimentos</a>
    </nav>

    <div class="nav-actions">

        <a href="carrinho.php" class="nav-icon" title="Meu Carrinho">
            <img class="nav-mage" src="imagens/carrinho.png" alt="Carrinho">
        </a>

        <a href="perfil.php" class="nav-icon" title="Meu Perfil">
            <img class="nav-mage" src="imagens/perfil.png" alt="Perfil">
        </a>

    </div>

</div>