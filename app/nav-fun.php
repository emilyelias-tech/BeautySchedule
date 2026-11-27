<?php
declare(strict_types=1);

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$isInFuncionario = str_ends_with($scriptDir, '/app-funcionario');
$baseApp = $isInFuncionario ? '..' : '.';
$baseFuncionario = $isInFuncionario ? '.' : 'app-funcionario';
?>

<div class="navbar">

    <div class="brand">
        <a href="<?= $baseFuncionario ?>/index.php">
            <img class="img" src="<?= $baseApp ?>/imagens/Logotipo Botânico BS Beauty Schedule.png" alt="Beauty Schedule">
        </a>
    </div>

        <nav class="nav-links">
            <a href="<?= $baseFuncionario ?>/index.php" class="ativo1">
                <i class="fa-solid fa-house"></i>
                Início
            </a>

            <a href="<?= $baseFuncionario ?>/agenda.php" class="ativo2">
                <i class="fa-regular fa-calendar"></i>
                Agenda
            </a>

            <a href="<?= $baseFuncionario ?>/clientes.php" class="ativo3">>
                <i class="fa-solid fa-user-plus"></i>
                Clientes
            </a>

            <a href="<?= $baseFuncionario ?>/servicos.php" class="ativo4">>
                <i class="fa-solid fa-scissors"></i>
                Serviços
            </a>

            <a href="<?= $baseFuncionario ?>/perfil.php" class="ativo5">>
                <i class="fa-regular fa-user"></i>
                Perfil
            </a>
        </nav>

</div>