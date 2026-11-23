
<?php

$databaseStatus = 'Conectado';
$produtos = [];

try {
    require_once __DIR__ . '/../init.php';

    $pdo->query('SELECT 1');

} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}

if ($databaseStatus === 'Conectado') {
    try {
        $sql = "SELECT id, nome, preco, materiais, quantidade, imagem
                FROM produtos
                WHERE ativo = 1
                ORDER BY id ASC";

        $stmt = $pdo->query($sql);
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Throwable $exception) {
        $databaseStatus = 'Indisponível';
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nossos Produtos | BeautySchedule</title>

    <link rel="stylesheet" href="/app/css/produtos.css">
</head>

<body>

    <?php require_once __DIR__ . '/../nav.php'; ?>

    <main class="produtos">

        <section class="titulo">
            <h1>Nossos produtos</h1>
            <p>Cuide de você com nossos produtos especiais.</p>
        </section>

        <section class="lista-produtos">

            <?php if ($databaseStatus === 'Indisponível'): ?>

                <p class="mensagem">
                    O banco de dados está indisponível no momento.
                </p>

            <?php elseif (empty($produtos)): ?>

                <p class="mensagem">
                    Nenhum produto cadastrado.
                </p>

            <?php else: ?>

                <?php foreach ($produtos as $produto): ?>

                    <div class="card-produto">

                        <div class="imagem-produto">

                            <img
                                src="<?= htmlspecialchars($produto['imagem'] ?? 'assets/sem-foto.jpg') ?>"
                                alt="<?= htmlspecialchars($produto['nome']) ?>"
                            >

                        </div>

                        <div class="linha-verde"></div>

                        <div class="informacoes-produto">

                            <span class="id-produto">
                                Produto #<?= (int) $produto['id'] ?>
                            </span>

                            <h2 class="nome-produto">
                                <?= htmlspecialchars($produto['nome']) ?>
                            </h2>

                            <p class="materiais">
                                <?= htmlspecialchars($produto['materiais'] ?? '') ?>
                            </p>

                            <div class="rodape-produto">

                                <span class="preco-produto">
                                    R$ <?= number_format(
                                        (float) $produto['preco'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </span>

                                <a
                                    href="produto.php?id=<?= (int) $produto['id'] ?>"
                                    class="botao-produto"
                                    aria-label="Ver detalhes de <?= htmlspecialchars($produto['nome']) ?>"
                                >
                                    &#x27F6;
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </main>

</body>
</html>
