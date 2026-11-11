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
    <title>Nossos Procedimentos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>
        Nossos procedimentos <br> para a sua beleza
    </h1>
    <?php if ($databaseStatus === 'Indisponível'): ?> <!-- essa funcao mostra indisponivel caso o status nao exista!(o === verifica se o que está escrito é igual e tem a msm identidade, str, int etc)-->
            <p>O banco de dados está indisponível no momento.</p>
        <?php elseif (empty($procedimentos)): ?>  <!-- essa funcao mostra a msg a seguir caso nao exista procedimentos!-->
            <p>Nenhum procedimento cadastrado.</p>
        <?php else: ?>
            <?php foreach ($procedimentos as $procedimento): ?>  <!-- a partir daqui os cards sao criados de acordo com o que há no Banco!-->
                <div class="card-procedimento">
                    
                    <!-- Imagem do Procedimento -->
                    <div class="card-imagem">
                        <img src="<?= htmlspecialchars($procedimento['imagem'] ?? 'assets/sem-foto.jpg') ?>" 
                             alt="<?= htmlspecialchars($procedimento['nome']) ?>">
                    </div>
                    
                    <!-- Conteúdo do Card -->
                    <div class="card-corpo">
                        <h3 class="nome-procedimento"><?= htmlspecialchars($procedimento['nome']) ?></h3>
                        
                        <div class="card-rodape">
                            <span class="preco">R$ <?= number_format((float)$procedimento['preco'], 2, ',', '.') ?></span>
                            <a href="procedimento.php?id=<?= $procedimento['id'] ?>" 
                               class="btn-detalhes" 
                               aria-label="Ver detalhes de <?= htmlspecialchars($procedimento['nome']) ?>">
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