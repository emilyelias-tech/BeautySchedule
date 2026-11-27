
<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = database();

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {

    $idExcluir = (int) $_POST['id_procedimento'];

    try {

        $stmtExcluir = $pdo->prepare("
            DELETE FROM procedimentos
            WHERE id_procedimento = :id
        ");

        $stmtExcluir->execute([
            'id' => $idExcluir
        ]);

        header('Location: servicos.php');
        exit;

    } catch (PDOException $e) {

        $mensagemErro = 'Não foi possível excluir este serviço. Ele pode estar vinculado a agendamentos.';

    }
}

$stmt = $pdo->query("
    SELECT
        id_procedimento,
        nome,
        categoria,
        valor,
        duracao_minutos,
        imagem
    FROM procedimentos
    ORDER BY nome ASC
");

$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Serviços | Beauty Schedule</title>

    <link rel="stylesheet" href="/app/css/css-func/servicos.css">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

    <?php require_once __DIR__ . "/../nav-fun.php" ?>

    <main class="conteudo">

        <section class="cabecalho">

            <div>

                <p class="identificacao">SEU CATÁLOGO</p>

                <h1>Serviços</h1>

                <p class="subtitulo">
                    Gerencie procedimentos, valores e disponibilidade.
                </p>

            </div>

            <a href="novo-servico.php" class="botao-novo">
                <i class="fa-solid fa-plus"></i>
                Novo serviço
            </a>

        </section>

        <?php if (!empty($mensagemErro)): ?>

            <p class="mensagem-erro">
                <?= htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <section class="lista-servicos">

            <?php if (count($servicos) > 0): ?>

                <?php foreach ($servicos as $servico): ?>

                    <?php

                    $imagem = !empty($servico['imagem'])
                        ? '../' . $servico['imagem']
                        : '../img/procedimento-padrao.jpg';

                    ?>

                    <article class="card-servico">

                        <img
                            src="<?= htmlspecialchars($imagem, ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($servico['nome'], ENT_QUOTES, 'UTF-8') ?>"
                            class="imagem-servico"
                        >

                        <div class="informacoes-servico">

                            <span class="categoria-servico">
                                <?= htmlspecialchars($servico['categoria'], ENT_QUOTES, 'UTF-8') ?>
                            </span>

                            <h2>
                                <?= htmlspecialchars($servico['nome'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <p class="duracao-servico">
                                <?= (int) $servico['duracao_minutos'] ?> min
                            </p>

                            <p class="valor-servico">
                                R$ <?= number_format((float) $servico['valor'], 2, ',', '.') ?>
                            </p>

                        </div>

                        <form method="POST" class="form-excluir">

                            <input
                                type="hidden"
                                name="id_procedimento"
                                value="<?= (int) $servico['id_procedimento'] ?>"
                            >

                            <button
                                type="submit"
                                name="excluir"
                                class="botao-excluir"
                                aria-label="Excluir serviço"
                            >
                                <i class="fa-regular fa-trash-can"></i>
                            </button>

                        </form>

                    </article>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="nenhum-servico">

                    <i class="fa-solid fa-scissors"></i>

                    <p>Nenhum serviço cadastrado.</p>

                </div>

            <?php endif; ?>

        </section>

    </main>

</body>

</html>
