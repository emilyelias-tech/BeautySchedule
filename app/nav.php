<?php
    declare(strict_types=1);

    // o php para de traduzir os itens e leva ao pe da letra, mostrando os erros que podem ser invisiveis no php normal

    require_once __DIR__ . '/init.php';

    // caso o usuario esteja logado, botoes login e cadastro desaparecerao e aparecera o meu perfil e carrinho
    $usuarioLogado = isset($_SESSION['usuario_id']);
    ?>

    <!-- fonte provisoria para os icones do carrinho e perfil -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <nav class="navbar">
        <div class="brand">
         <img src="logo.png" alt="Beauty Schedule">
         <h1 >Beauty Schedule</h1>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="produtos.php">Produtos</a></li>
            <li><a href="agendar.php">Agendar</a></li>
            <li><a href="consulta.php">Consulta</a></li>
            <li><a href="procedimentos.php">Procedimentos</a></li>

        </ul>

        <div class="nav-actions">
            <?php if ($usuarioLogado): ?>
                <!-- Usuário LOGADO: Exibe Ícones de Carrinho, Perfil e botão Sair -->
                <a href="carrinho.php" class="nav-icon" title="Meu Carrinho">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>

                <a href="perfil.php" class="nav-icon" title="Meu Perfil">
                    <i class="fa-solid fa-user"></i>
                </a>

                <a href="logout.php" class="btn-logout">Sair</a>
            <?php else: ?>
                <!-- Se o usuario nao tiver logado aparece: Login/Cadastro !-->
                <a href="login.php" class="btn-login">Login</a>
                <a href="cadastro.php" class="btn-cadastro">Cadastrar</a>
            <?php endif; ?>
        </div>
    </nav>