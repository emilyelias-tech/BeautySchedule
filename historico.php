<?php
require_once 'estado_app.php';

$mensagem_resgate = '';
$erro_resgate = '';

// Recompensas disponíveis para troca
$recompensas = [
    ['id' => 1, 'titulo' => 'Desconto de R$ 20,00 em Serviços', 'custo' => 300],
    ['id' => 2, 'titulo' => 'Desconto de R$ 40,00 em Qualquer Procedimento', 'custo' => 600],
    ['id' => 3, 'titulo' => 'Hidratação Facial Express Grátis', 'custo' => 450]
];

// Processa o resgate de pontos
if (isset($_GET['resgatar_id'])) {
    $id_rec = (int)$_GET['resgatar_id'];
    foreach ($recompensas as $rec) {
        if ($rec['id'] === $id_rec) {
            if ($_SESSION['pontos'] >= $rec['custo']) {
                $_SESSION['pontos'] -= $rec['custo'];
                array_unshift($_SESSION['historico_pontos'], [
                    'data' => date('d/m/Y'),
                    'descricao' => 'Resgate: ' . $rec['titulo'],
                    'pontos' => '-' . $rec['custo'],
                    'tipo' => 'resgate'
                ]);
                header("Location: " . $_SERVER['PHP_SELF'] . "?resgatado=1");
                exit;
            } else {
                $erro_resgate = 'Pontos insuficientes para este benefício.';
            }
        }
    }
}

if (isset($_GET['resgatado'])) {
    $mensagem_resgate = 'Benefício resgatado com sucesso! Seu saldo foi atualizado.';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="historico.css">
    <title>Beauty Schedule - Histórico de Pontos</title>
</head>
<body class="<?= htmlspecialchars($attr_body_class) ?>">

<header> 
    <div class="logo">
        <h1>BS</h1>
        <div>
            <p>Beauty</p>
            <p>Schedule</p>
        </div>
    </div>

    <nav class="menu-topo">
        <a href="index.php">Inicio</a>
        <a href="produtos.php">Produtos</a>
        <a href="agendar.php">Agendar</a>
        <a href="consulta.php">Consulta</a>
        <a href="procedimentos.php">Procedimentos</a>
    </nav>

    <div class="icones-topo">
        <a href="carrinho.php" class="icone-circulo" title="Sacola">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4D5637" stroke-width="1.8">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
        </a>
        <a href="configuracao-dados-pessoais.php" class="icone-perfil-topo" title="Perfil">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#D8D0B4">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
        </a>
    </div>
</header>

<main>
    <div class="coluna-esquerda">
        <section class="perfil-section">
            <div class="avatar"><?= htmlspecialchars($iniciais) ?></div>
            <h4><?= htmlspecialchars($nome_cartao) ?></h4>
            <h6><?= htmlspecialchars($email_cartao) ?></h6>

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

        <section class="nav-perfil">
            <a href="configuracao-dados-pessoais.php"><span>Dados pessoais</span> <span>&gt;</span></a>
            <a href="configuracao-enderecos.php"><span>Endereços</span> <span>&gt;</span></a>
            <a href="cartoes.php"><span>Meus cartões</span> <span>&gt;</span></a>
            <a href="configuracao.php"><span>Configurações</span> <span>&gt;</span></a>
            <a href="historico.php" class="ativo"><span>Histórico de pontos</span> <span>&gt;</span></a>
        </section>

        <section class="club-card">
            <h4 class="club-title">Beauty club</h4>
            <div class="club-points-box">
                <span class="club-num"><?= $_SESSION['pontos'] ?></span>
                <span class="club-label">Pontos</span>
            </div>
            <p class="club-desc">Voce pode trocar seus pontos por até R$40 de desconto</p>
            <a href="#recompensas" class="club-link">Resgatar beneficio &rarr;</a>
        </section>
    </div>

    <section class="dados-section">
        <p class="subtitulo">Beauty Club</p> 
        <h2>Histórico de pontos</h2> 

        <?php if ($mensagem_resgate !== ''): ?>
            <p class="msg-sucesso"><?= htmlspecialchars($mensagem_resgate) ?></p>
        <?php endif; ?>
        <?php if ($erro_resgate !== ''): ?>
            <p class="msg-erro"><?= htmlspecialchars($erro_resgate) ?></p>
        <?php endif; ?>

        <!-- Resumo de Saldo -->
        <div class="card-saldo-destaque">
            <div>
                <span>Saldo disponível</span>
                <h3><?= $_SESSION['pontos'] ?> Pontos</h3>
            </div>
            <p>A cada R$ 1,00 em cuidados, você acumula 2 pontos no Beauty Club.</p>
        </div>

        <!-- Recompensas para Resgate -->
        <h5 class="categoria-titulo" id="recompensas">Trocar pontos por benefícios</h5>
        <div class="lista-recompensas">
            <?php foreach ($recompensas as $rec): ?>
                <div class="card-recompensa">
                    <div>
                        <h4><?= htmlspecialchars($rec['titulo']) ?></h4>
                        <span><?= $rec['custo'] ?> pontos</span>
                    </div>
                    <?php if ($_SESSION['pontos'] >= $rec['custo']): ?>
                        <a href="?resgatar_id=<?= $rec['id'] ?>" class="btn-resgatar">Resgatar</a>
                    <?php else: ?>
                        <span class="btn-indisponivel">Pontos insuficientes</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Extrato de Movimentações -->
        <h5 class="categoria-titulo">Extrato de movimentações</h5>
        <div class="grupo-cards">
            <?php foreach ($_SESSION['historico_pontos'] as $mov): ?>
                <div class="item-movimento">
                    <div>
                        <h4><?= htmlspecialchars($mov['descricao']) ?></h4>
                        <p><?= htmlspecialchars($mov['data']) ?></p>
                    </div>
                    <span class="pontos-badge <?= $mov['tipo'] === 'ganho' ? 'positivo' : 'negativo' ?>">
                        <?= htmlspecialchars($mov['pontos']) ?> pts
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>  
</main>
    
</body>
</html>