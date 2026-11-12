<?php
    declare(strict_types=1);

    // o php para de traduzir os itens e leva ao pe da letra, mostrando os erros que podem ser invisiveis no php normal


    // caso o usuario esteja logado, botoes login e cadastro desaparecerao e aparecera o meu perfil e carrinho
    $usuarioLogado = isset($_SESSION['usuario_id']);
    ?>

    <!-- fonte provisoria para os icones do carrinho e perfil -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/nav.css">

    <div class="navbar">
        <div class="brand">
         <img class ="img"src="img/logo.png" alt="Beauty Schedule">
        </div>

        <nav class="nav-links">
            <a href="index.php">Início</a>
            <a href="produtos.php">Produtos</a>
            <a href="agendar.php">Agendar</a>
            <a href="consulta.php">Consulta</a>
            <a href="procedimentos.php">Procedimentos</a>

        </nav>

        <div class="nav-actions">
            <?php if ($usuarioLogado): ?>
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
            </div>
    <!-- Falta os ícones que serão realmente utilizados ao final, falta o Titulo que está mal feito e a correção na logo !-->