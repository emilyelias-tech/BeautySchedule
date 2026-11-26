<?php
require_once __DIR__ . "/../init.php";

$pdo = database();
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

if ($busca !== '') {
    $sql = "SELECT * FROM procedimentos WHERE ativo = 1 AND nome LIKE :busca ORDER BY id_procedimento DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', '%' . $busca . '%');
    $stmt->execute();
    $procedimentosFiltrados = $stmt->fetchAll();
} else {
    $sql = "SELECT * FROM procedimentos WHERE ativo = 1 ORDER BY id_procedimento DESC";
    $stmt = $pdo->query($sql);
    $procedimentosFiltrados = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Schedule - Procedimentos</title>
    <link rel="stylesheet" href="/app/css/produtos.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>

    <section class="secao-produtos">
        <h1 class="titulo">
            <span class="verde-claro">Nossos</span> procedimentos<br>
            <span class="verde-claro">para a</span> sua beleza.
        </h1>

        <form method="GET" action="" class="barra-pesquisa-container">
            <input type="text" name="busca" id="inputBusca" placeholder="Pesquisar procedimento..." value="<?php echo htmlspecialchars($busca); ?>">
            <button type="submit" class="btn-buscar">Buscar</button>
            <?php if ($busca !== ''): ?>
                <a href="procedimentos.php" class="btn-limpar">Limpar</a>
            <?php endif; ?>
        </form>

        <div class="lista-cards">
            <?php if (count($procedimentosFiltrados) > 0): ?>
                <?php foreach ($procedimentosFiltrados as $item): ?>
                    <?php 
                        $imagemProc = !empty($item['imagem']) ? $item['imagem'] : 'https://placehold.co/300x300/e8dfd5/495437?text=Procedimento';
                    ?>
                    <a href="agendar-procedimento.php" class="card-link">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($imagemProc); ?>" alt="<?php echo htmlspecialchars($item['nome'] ?? 'Procedimento'); ?>" class="card-imagem">
                            <div class="faixa-verde"></div>
                            <div class="card-info">
                                <p class="nome-produto"><?php echo htmlspecialchars($item['nome'] ?? 'Sem nome'); ?></p>
                                <div class="card-rodape">
                                    <span class="preco">R$ <?php echo number_format((float)($item['valor'] ?? 0), 2, ',', '.'); ?></span>
                                    <span class="botao-seta">➔</span>
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