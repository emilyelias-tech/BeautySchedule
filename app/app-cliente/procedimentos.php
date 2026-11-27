<?php
declare(strict_types=1);
require_once __DIR__ . "/../init.php";

try {
    $pdo = database();
    
    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    
    if (!empty($busca)) {
        $stmt = $pdo->prepare("SELECT DISTINCT * FROM procedimentos WHERE nome LIKE :busca OR materiais LIKE :busca ORDER BY id DESC");
        $stmt->execute(['busca' => '%' . $busca . '%']);
    } else {
        $stmt = $pdo->query("SELECT DISTINCT * FROM procedimentos ORDER BY id DESC");
    }
    
    $procedimentosFiltrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $procedimentosFiltrados = [];
    $busca = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Schedule - Produtos</title>
    <link rel="stylesheet" href="../css/procedimentos.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <main class="secao-procedimentos">
        <h1 class="titulo">Nossos <span class="verde-claro">produtos</span><br>para a sua beleza.</h1>

        <form method="GET" action="produtos.php" class="barra-pesquisa-container">
            <input type="text" name="busca" id="inputBusca" placeholder="Pesquisar procedimentos..." value="<?php echo htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn-buscar">Buscar</button>
            <?php if (!empty($busca)): ?>
                <a href="procedimentos.php" class="btn-limpar">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="lista-cards">
            <?php if (count($procedimentosFiltrados) > 0): ?>
                <?php foreach ($procedimentosFiltrados as $item): ?>
                    <?php 
                        $imagemProduto = !empty($item['imagem']) ? $item['imagem'] : 'https://placehold.co/300x180/d5ceb8/485136?text=Beauty+Schedule';
                    ?>
                    <div class="card">
                        <a href="detalhes-procedimentos.php?id=<?php echo (int)$item['id']; ?>" class="card-link">
                            <img src="<?php echo htmlspecialchars($imagemProduto, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8'); ?>" class="card-imagem">
                            <div class="faixa-verde"></div>
                            <div class="card-info">
                                <p class="nome-procedimentos"><?php echo htmlspecialchars($item['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8'); ?></p>
                                
                                <div class="card-rodape">
                                    <span class="preco">R$ <?php echo number_format((float)($item['preco'] ?? 0), 2, ',', '.'); ?></span>
                                    <span class="botao-seta">➔</span>
                                </div>
                            </div>
                        </a>
                        
                        <div style="padding: 0 15px 15px 15px;">
                            <a href="adicionar-carrinho.php?id=<?php echo (int)$item['id']; ?>" style="display: block; text-align: center; background-color: #4f5838; color: #D5CEB8; padding: 8px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">
                                + Adicionar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-resultados">Nenhum produto encontrado para "<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>".</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>