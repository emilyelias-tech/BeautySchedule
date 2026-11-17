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
<p class="subtitulo">Dados pessoais</p>

<section class ="perfil-section">
<h4> Emily Elias </h4>
<h6>emilyelias@gmail.com</h6>
<hr>
<h4>12 <br> Cuidados Realizados <br><br>4.9 <br>Sua Avaliação Média</h4>
</section>

<section class ="nav-perfil">
<a href="#">Dados pessoais  ></a>
<a href="enderecos.php">Endereços  ></a>
<a href="cartoes.php">Meus cartões  ></a>
<a href="configuracao.php">Configurações  ></a>
<a href="historico.php">Histórico de pontos  ></a>


</section>
    <section class="dados-section">
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