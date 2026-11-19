<?php

?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href=controleDeAgenda.css>
    <title> Beauty Schedule </title>

</head>
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
    <section class="agenda">
        <h2>Escolha seu cuidado</h2>        

<!-- BOTÃO FECHAR -->
<a href="produtos.php"><button class="fechar">X</button></a>
<!-- ETAPAS DO AGENDAMENTO -->
<div class="etapas">
    <span class="etapa ativa"></span>
    <span class="etapa"></span>
    <span class="etapa"></span>
</div>

<p class="subtitulo">Agendamento</p>

<!-- PROCEDIMENTOS -->
<div class="servicos">

    <div class="servico selecionado">
        <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=150">

        <div class="info">
            <h3>Limpeza de pele</h3>
            <p>60 min · R$ 150</p>
        </div>

        <button class="selecao"></button>
    </div>

    <div class="servico">
        <img src="https://images.unsplash.com/photo-1519823551278-64ac92734fb1?w=150">

        <div class="info">
            <h3>Drenagem linfática</h3>
            <p>50 min · R$ 130</p>
        </div>

        <button class="selecao">✓</button>
    </div>

    <div class="servico">
        <img src="https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=150">

        <div class="info">
            <h3>Design de sobrancelhas</h3>
            <p>40 min · R$ 80</p>
        </div>

        <button class="selecao">✓</button>
    </div>

    <div class="servico">
        <img src="https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=150">

        <div class="info">
            <h3>Hidratação capilar</h3>
            <p>45 min · R$ 110</p>
        </div>

        <button class="selecao">✓</button>
    </div>

</div>

<!-- BOTÃO CONTINUAR -->
<div class="rodape">
    <button class="continuar">Continuar →</button>
</div>

</section>
</main>
<body>
   
</body>
</html>