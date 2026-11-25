<?php
require_once __DIR__ . "/../init.php";

$pdo = database();

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if ($busca !== '') {
    $sql = "SELECT * FROM produtos WHERE nome LIKE :busca ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', '%' . $busca . '%');
    $stmt->execute();
    $produtosFiltrados = $stmt->fetchAll();
} else {
    $sql = "SELECT * FROM produtos ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $produtosFiltrados = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Schedule - Produtos</title>
    <link rel="stylesheet" href="../css/produtos.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <section class="secao-produtos">
        
        <h1 class="titulo">
            <span class="verde-claro">Nossos</span> procedimento<br>
            <span class="verde-claro">para a</span> sua beleza.
        </h1>

        <form method="GET" action="" class="barra-pesquisa-container">
            <input type="text" name="busca" id="inputBusca" placeholder="Pesquisar procedimento..." value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn-buscar">Buscar</button>
            <?php if ($busca !== ''): ?>
                <a href="produtos.php" class="btn-limpar">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="lista-cards">
    <?php if (count($produtosFiltrados) > 0): ?>
        <?php foreach ($produtosFiltrados as $item): ?>
            <?php 
                $imagemProduto = !empty($item['imagem']) ? $item['imagem'] : 'https://placehold.co/300x300/e8dfd5/495437?text=Sem+Foto';
            ?>
            <a href="detalhes-produto.php?id=<?php echo $item['id']; ?>" class="card-link">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($imagemProduto); ?>" alt="<?php echo htmlspecialchars($item['nome'] ?? 'Produto'); ?>" class="card-imagem">
                    <div class="faixa-verde"></div>
                    <div class="card-info">
                        <p class="nome-produto"><?php echo htmlspecialchars($item['nome'] ?? 'Sem nome'); ?></p>
                        <div class="card-rodape">
                            <span class="preco">R$ <?php echo number_format((float)($item['preco'] ?? 0), 2, ',', '.'); ?></span>
                            <span class="botao-seta"></span>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="sem-resultados">Nenhum procedimento encontrado para "<?php echo htmlspecialchars($busca); ?>".</p>
    <?php endif; ?>
</div>

    </section>

</body>
</html>