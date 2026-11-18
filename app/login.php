<?php

declare(strict_types=1);
$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/login.css">
</head>

<body>
    <div class="fundo">

        <div class="texto">
            <h3>Bem-vindo(a) de volta</h3>
            <h1>Entre na sua conta</h1>
        </div>

        <img src="/app/imagens/Logotipo Botânico BS Beauty Schedule.png" alt="logo">

        <div class="campo">
            <label for="index.php">
                <label><h4>E-mail</h4></label>
                <input type="email" name="email" id="email" placeholder="seu@email.com" required>

                <label><h4>Senha</h4></label>
                <input type="password" name="senha" id="senha" placeholder="• • • • • • ">
            </label>

            <div class="esqueci">

                <a href="esqueci-senha.php">Esqueci minha senha</a>

            </div>
            <button type="submit">Entrar  →</button>

         <h5>Ainda não tem conta? <a class="a"href="criar-conta.php">Cadastre-se</Cadastre-se></a></h5>        

        </div>
    </div>
</body>
</html>