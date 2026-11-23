<?php
require_once __DIR__ . "/../init.php";

try {
    $pdo = database();
    
    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
    
    if (!empty($busca)) {
        $stmt = $pdo->prepare("SELECT DISTINCT * FROM produtos WHERE nome LIKE :busca OR materiais LIKE :busca ORDER BY id DESC");
        $stmt->execute(['busca' => '%' . $busca . '%']);
    } else {
        $stmt = $pdo->query("SELECT DISTINCT * FROM produtos ORDER BY id DESC");
    }
    
    $produtosFiltrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $produtosFiltrados = [];
    $busca = '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Schedule - Produtos</title>
    <link rel="stylesheet" href="/app/css/produtos.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <main class="secao-produtos">
        <h1 class="titulo">Nossos <span class="verde-claro">produtos</span><br>para a sua beleza.</h1>

        <!-- Barra de Pesquisa -->
        <form method="GET" action="produtos.php" class="barra-pesquisa-container">
            <input type="text" name="busca" id="inputBusca" placeholder="Pesquisar produto..." value="<?php echo htmlspecialchars($busca ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn-buscar">Buscar</button>
            <?php if (!empty($busca)): ?>
                <a href="produtos.php" class="btn-limpar">Limpar</a>
            <?php endif; ?>
        </form>

        <!-- Lista de Cards -->
        <div class="lista-cards">
            <?php if (count($produtosFiltrados) > 0): ?>
                <?php foreach ($produtosFiltrados as $item): ?>
                    <?php 
                        $imagemProduto = !empty($item['imagem']) ? $item['imagem'] : 'https://placehold.co/300x180/d5ceb8/485136?text=Beauty+Schedule';
                    ?>
                    <a href="detalhes-produto.php?id=<?php echo $item['id']; ?>" class="card-link">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($imagemProduto); ?>" alt="<?php echo htmlspecialchars($item['nome'] ?? 'Produto', ENT_QUOTES, 'UTF-8'); ?>" class="card-imagem">
                            <div class="faixa-verde"></div>
                            <div class="card-info">
                                <p class="nome-produto"><?php echo htmlspecialchars($item['nome'] ?? 'Sem nome', ENT_QUOTES, 'UTF-8'); ?></p>
                                
                                <p class="descricao-card"><?php echo htmlspecialchars(substr($item['descricao'] ?? '', 0, 65)) . '...'; ?></p>

                                <div class="card-rodape">
                                    <span class="preco">R$ <?php echo number_format((float)($item['preco'] ?? 0), 2, ',', '.'); ?></span>
                                    <span class="botao-seta">➔</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="sem-resultados">Nenhum produto encontrado para "<?php echo htmlspecialchars($busca, ENT_QUOTES, 'UTF-8'); ?>".</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>