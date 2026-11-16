<?php

require_once __DIR__ . '/banco/database.php';

$pdo = database();

$sql = "SELECT * FROM produtos ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/produtos.css">
</head>

<body>

<section class="produtos">

    <div class="lista-produtos">

        <?php foreach ($produtos as $produto): ?>

            <div class="card-produto">

                <div class="imagem-produto">

                    <img
                        src="<?php echo htmlspecialchars($produto['imagem']); ?>"
                        alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                    >

                </div>

                <div class="linha-verde"></div>

                <div class="informacoes-produto">

                    <p class="nome-produto">
                        <?php echo htmlspecialchars($produto['nome']); ?>
                    </p>

                    <div class="rodape-produto">

                        <p class="preco-produto">
                            R$ <?php echo number_format(
                                (float) $produto['preco'],
                                2,
                                ',',
                                '.'
                            ); ?>
                        </p>

                        <a href="produto.php?id=<?php echo (int) $produto['id']; ?>"
                           class="botao-produto">
                            →
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</section>

</body>
</html>
