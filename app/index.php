<?php

declare(strict_types=1);


// 1. SIMULAÇÃO DOS DADOS (Estes valores na prática viriam da tua tabela de utilizadores no Banco de Dados)
$pontosAtuais = 680;  // Quantidade de pontos que o utilizador possui atualmente
$metaPontos   = 1000; // Quantidade total de pontos necessária para atingir a meta

// 2. CÁLCULO DA PORCENTAGEM DA BARRA
// Fazemos uma regra de 3 simples: (Pontos Atuais / Meta) * 100
// Exemplo: (680 / 1000) * 100 = 68
$porcentagem = ($pontosAtuais / $metaPontos) * 100;

// 3. VALIDAÇÃO DE SEGURANÇA
// Se o utilizador tiver mais de 1000 pontos (ex: 1200), a porcentagem daria 120%.
// Limitamos no máximo a 100% para a barra de CSS não quebrar o layout e "sair" do container.
if ($porcentagem > 100) {
    $porcentagem = 100;
}

// 4. VERIFICAÇÃO DE CONEXÃO AO BANCO DE DADOS
$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beauty Schedule - Início</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <main class="container-principal">

        <!-- BANNER DE BENEFÍCIOS DO BEAUTY CLUB -->
        <section class="banner-club">
            <div class="club-info">
                <img class="club-logo" src="imagens/beauty.png" alt="Logo">
                <div>
                    <span class="club-tag">Beauty club</span>
                    <h2>Seu cuidado vale pontos</h2>
                    
                    <!-- Exibição dinâmica dos pontos no texto do parágrafo -->
                    <p>Você tem <strong><?= $pontosAtuais ?> pontos</strong> e já pode resgatar <strong>R$ 40 de desconto</strong> no próximo serviço</p>
                </div>
            </div>

            <div class="club-progresso">
                <!-- ESTRUTURA DA BARRA DE PROGRESSO -->
                <div class="barra-progresso">
                    <!--    
                        Usamos o <?= $porcentagem ?> dentro do atributo style="width: ...%".
                        O navegador vai ler isso como style="width: 68%;" por exemplo.
                    -->
                    <div class="progresso-preenchido" style="width: <?= $porcentagem ?>%;"></div>
                </div>
                
                <!-- TEXTO INFORMATIVO DINÂMICO -->
                <!-- number_format ajuda a formatar o número 1000 como 1.000 se desejares -->
                <span class="texto-progresso">
                    <?= $pontosAtuais ?> de <?= number_format($metaPontos, 0, ',', '.') ?> pontos para o próximo benefício
                </span>

                <button class="btn-beneficios">Ver meus benefícios</button>
            </div>
        </section>

    </main>

</body>
</html> 