<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dados.css">
    <title>Beauty Schedule </title>
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<main>
<p class="subtitulo">Dados pessoais</p>

<section class ="perfil-section">
<h4> Emily Elias </h4>
<h6>emilyelias@gmail.com</h6>
<hr>
<h4>12 <br> Cuidados Realizados <br><br>4.9 <br>Sua Avaliação Média</h4>
</section>

<section class ="nav-perfil">
<a href="configuracao-dados-pessoais.php">Dados pessoais  ></a><br>
<a>Meu endereço</a><br>
<a href="cartoes.php">Meus cartões  ></a><br>
<a href="configuracao.php">Configurações  ></a><br>
<a href="historico.php">Histórico de pontos  ></a><br>


</section>
    <section class="dados-section">
        <h2>Meu endereço</h2> 
        
        <form class="campo-formulario">
            <label for="cep">CEP:</label>
            <input type="number" id="cep" name="cep">

            <label for="estado">Estado:</label>
              <input type="text" id="estado" name="estado">

            <label for="endereco">Endereço:</label>
              <input type="text" id="endereco" name="endereco">

               <label for="numero">Número:</label>
              <input type="number" id="numero" name="numero">
               
              <label for="bairro">Bairro:</label>
              <input type="text" id="bairro" name="bairro">

               <label for="CPF">Complemento:</label>
              <input type="text" id="complemento" name="complemento">

</form>
</section>  
</main>
    
</body>
</html>


