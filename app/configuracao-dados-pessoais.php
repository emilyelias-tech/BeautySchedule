<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="configuracao-dados-pessoais.css">
    <title>Beauty Schedule </title>
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
    <!-- 1. caixa da esquerda q agrupa oscartões -->
     <div class="coluna-esquerda">

<section class ="perfil-section">
    <div class="avatar">EE</div>
<h4> Emily Elias </h4>
<h6>emilyelias@gmail.com</h6>

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

<section class ="nav-perfil">
<a href="#" class="ativo"><span>Dados pessoais</span> <span>&gt;</span></a>
<a href="enderecos.php"><span>Endereços</span> <span>&gt;</span></a>
<a href="cartoes.php"><span>Meus cartões</span> <span>&gt;</span></a>
<a href="configuracao.php"><span>Configurações</span> <span>&gt;</span></a>
<a href="historico.php"><span>Histórico de pontos</span> <span>&gt;</span></a>


</section>
<section class="club-card">
    <h4 class="club-title">Beauty club</h4>

    <div class="club-points-box">
        <span class="club-num">680</span>
        <span class="club-label">Ponto</span>
    </div>

    <p class="club-desc">Voce pode trocar seus pontos por até R$40 de desconto</p>

    <a href="resgatar.php" class="club-link">Resgatar beneficio &rarr;</a>
</section>

</div>
    <section class="dados-section">
        <p class="subtitulo">Dados pessoais</p> 
        <h2>Seu perfil</h2> 
        
        <form class="campo-formulario">
            <label for="nome">Nome completo:</label>
              <input type="text" id="nome" name="nome">

               <label for="email">Email:</label>
              <input type="text" id="email" name="email">
               
              <label for="data-nasc">Data de nascimento:</label>
              <input type="text" id="data-nasc" name="data-nasc">

               <label for="Telefone">Telefone:</label>
              <input type="text" id="Telefone" name="Telefone">

               <label for="CPF">CPF:</label>
              <input type="text" id="CPF" name="CPF">

</form>
</section>  
</main>
    
</body>
</html>