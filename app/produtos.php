<?php

declare(strict_types=1);
require_once __DIR__ . '/css/nav.php';

$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}
$produtos = [];
if ($databaseStatus === 'Conectado') {
    try {
        $stmt = $pdo->query('SELECT id, nome, preco, materiais, quantidade, imagem FROM produtos');
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nossos Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="titulo-secao">Nossos produtos<br>pensando em você.</h1>
    
    <div class="card_produtos">
        <?php if ($databaseStatus === 'Indisponível'): ?> <!-- essa funcao mostra indisponivel caso o status nao exista!-->
            <p>O banco de dados está indisponível no momento.</p>
        <?php elseif (empty($produtos)): ?>  <!-- essa funcao mostra a msg caso nao exista produtos!-->
            <p>Nenhum produto cadastrado.</p>
        <?php else: ?>
            <?php foreach ($produtos as $produto): ?>  <!-- a partir daqui os cards sao criados de acordo com o que há no Banco!-->
                <div class="card-produto">
                    
                    <!-- Imagem do Produto -->
                    <div class="card-imagem">
                        <img src="<?= htmlspecialchars($produto['imagem'] ?? 'assets/sem-foto.jpg') ?>" 
                             alt="<?= htmlspecialchars($produto['nome']) ?>">
                    </div>
                    
                    <!-- Conteúdo do Card -->
                    <div class="card-corpo">
                        <h3 class="nome-produto"><?= htmlspecialchars($produto['nome']) ?></h3>
                        
                        <div class="card-rodape">
                            <span class="preco">R$ <?= number_format((float)$produto['preco'], 2, ',', '.') ?></span>
                            <a href="produto.php?id=<?= $produto['id'] ?>" 
                               class="btn-detalhes" 
                               aria-label="Ver detalhes de <?= htmlspecialchars($produto['nome']) ?>">
                                &#x27F6;
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>