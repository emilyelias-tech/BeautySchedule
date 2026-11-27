<?php

require_once __DIR__ . "/../init.php";

$procedimentos = readAll($pdo, 'procedimentos');

$procedimentosPorCategoria = [];
foreach ($procedimentos as $proc) {
    $categoria = $proc['categoria'] ?? 'Outros';
    $procedimentosPorCategoria[$categoria][] = $proc;
}

$categoriasDisponiveis = array_keys($procedimentosPorCategoria);
$categoriaAtiva = $_GET['cat'] ?? ($categoriasDisponiveis[0] ?? '');

$erro = '';

if (isset($_POST['cadastrar'])) {
    $procedimento_id = $_POST['procedimento'] ?? '';

    if (empty($procedimento_id)) {
        $erro = "Por favor, selecione um cuidado antes de continuar.";

    } else {
        $dados = read(
            $pdo,
            'procedimentos',
            'id_procedimento = ?',
            [$procedimento_id]
        );

        if (!$dados) {
            $erro = "Procedimento não encontrado.";
        } else {

            $_SESSION['agendamento_atual'] = [

                'id_procedimento' => $dados['id_procedimento'],
                'nome_procedimento' => $dados['nome'],
                'duracao_minutos' => $dados['duracao_minutos'],
                'valor' => $dados['valor'] ?? $dados['preco'] ?? 0

            ];

            header('Location: agendar-profissional.php');

            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - BeautySchedule</title>
    <link rel="stylesheet" href="../css/agendar-procedimento.css">
    <style>
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../nav.php'; ?>

    <main>
        <div class="container">
         
            <div class="card-header">
                <div class="progresso-wrapper">
                    <div class="progresso-barras">
                        <span class="barra ativa"></span>
                        <span class="barra"></span>
                        <span class="barra"></span>
                    </div>
                    <span class="subtitulo">Agendamento</span>
                </div>
                <a href="index.php" class="btn-fechar" title="Fechar">x</a>
            </div>

            <h2>Escolha seu cuidado</h2>
            <br><br>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <div class="categorias-tabs">
                <?php foreach ($categoriasDisponiveis as $cat): ?>
                    <a href="?cat=<?= urlencode($cat) ?>" class="tab-categoria <?= $cat === $categoriaAtiva ? 'ativa' : '' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="POST">
                <div class="lista-cuidados">
                    <?php 
                    if (isset($procedimentosPorCategoria[$categoriaAtiva])):
                        foreach ($procedimentosPorCategoria[$categoriaAtiva] as $index => $proc):
                            $imagem = !empty($proc['imagem']) ? $proc['imagem'] : 'img/procedimento-padrao.jpg';
                            $duracao = $proc['duracao_minutos'] ?? '50';
                            $preco = isset($proc['valor']) ? number_format($proc['valor'], 0, ',', '.') : (isset($proc['preco']) ? number_format($proc['preco'], 0, ',', '.') : '150');
                    ?>
                        <label class="card-cuidado">
                            <input
                                type="radio"
                                name="procedimento"
                                value="<?= htmlspecialchars($proc['id_procedimento']) ?>"
                                <?= $index === 0 ? 'checked' : '' ?>
                                required
                            >         
                            <div class="cuidado-conteudo">
                                <img src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($proc['nome']) ?>" class="cuidado-img">
                                <div class="cuidado-info">
                                    <span class="cuidado-titulo"><?= htmlspecialchars($proc['nome']) ?></span>
                                    <span class="cuidado-detalhes"><?= htmlspecialchars($duracao) ?> min R$ <?= htmlspecialchars($preco) ?></span>
                                </div>
                                <div class="cuidado-check">
                                    <span class="check-icon">✓</span>
                                </div>
                            </div>
                        </label>
                    <?php 
                        endforeach;
                    else:
                    ?>
                        <p>Nenhum procedimento encontrado nesta categoria.</p>
                    <?php endif; ?>
                </div>

                <button type="submit" name="cadastrar" class="btn-continuar">Continuar</button>
            </form>
        </div>
    </main>
</body>
</html>