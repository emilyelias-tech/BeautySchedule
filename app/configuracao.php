<?php

require_once __DIR__ . "/init.php"

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautySchedule|Inicio</title>
    <link rel="stylesheet" href="/app/css/configuracao.css">
</head>
<body>
    <div class="identificacao">
        <h4>Dados pessoais</h4>
        <h1>Seu perfil</h1>
    </div>
    <div class="opcao">
        <label>Nome completo: </label>
        <input type="text" name="nome" id="nome">

        <label>E-mail:</label>
        <input type="email" name="email" id="email">

        <label>Data de nascimento: </label>
        <input type="date" name="data" id="data">

        <label>Telefone:</label>
        <input type="tel" name="tel" id="tel">

        <label>CPF: </label>
        <input type="text" name="cpf" id="cpf">
    </div>

    <div class="identidade">
        <h1>Emily Elias</h1>
        <h5>emilyelias@gmail.com</h5>
       

    </div>
</body>
</html>