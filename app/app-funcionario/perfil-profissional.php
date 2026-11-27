
<?php
session_start();

$nome = $_POST['nome'] ?? 'Marina Lopes';
$especialidade = $_POST['especialidade'] ?? 'Estética facial e corporal';
$biografia = $_POST['biografia'] ?? 'Especialista em cuidados personalizados e bem-estar.';
$horario = $_POST['horario'] ?? 'Terça a sábado: 09h às 18h';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensagem = 'Alterações recebidas com sucesso!';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perfil profissional | BeautySchedule</title>

    <link rel="stylesheet" href="/app/css/css-func/perfil-profissional.css">
</head>

<body>

    <?php require_once __DIR__ . "/../nav-fun.php" ?>
    
    <main class="conteudo">

        <form method="POST" enctype="multipart/form-data">

            <header class="cabecalho">

                <div>
                    <span class="subtitulo">MINHA CONTA</span>

                    <h1>Perfil profissional</h1>

                    <p>Atualize suas informações e preferências.</p>
                </div>

                <button type="submit" class="botao-salvar">
                    Salvar alterações
                </button>

            </header>

            <?php if ($mensagem): ?>
                <p class="mensagem">
                    <?= htmlspecialchars($mensagem) ?>
                </p>
            <?php endif; ?>

            <section class="perfil">

                <div class="foto-container">

                    <img
                        src="../imagens/perfil-profissional.jpg"
                        alt="Foto do profissional"
                        class="foto-perfil"
                    >

                </div>

                <div class="informacoes-perfil">

                    <h2><?= htmlspecialchars($nome) ?></h2>

                    <span>Esteticista - CRBM 00000</span>

                    <label for="foto" class="botao-foto">
                        Alterar foto
                    </label>

                    <input
                        type="file"
                        name="foto"
                        id="foto"
                        accept="image/*"
                        hidden
                    >

                </div>

            </section>

            <section class="formulario">

                <div class="campo">
                    <label for="nome">Nome profissional</label>

                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        value="<?= htmlspecialchars($nome) ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="especialidade">Especialidade</label>

                    <input
                        type="text"
                        name="especialidade"
                        id="especialidade"
                        value="<?= htmlspecialchars($especialidade) ?>"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="biografia">Biografia</label>

                    <textarea
                        name="biografia"
                        id="biografia"
                        rows="4"
                    ><?= htmlspecialchars($biografia) ?></textarea>
                </div>

                <div class="campo">
                    <label for="horario">Horário de atendimento</label>

                    <input
                        type="text"
                        name="horario"
                        id="horario"
                        value="<?= htmlspecialchars($horario) ?>"
                    >
                </div>

            </section>

        </form>

    </main>

</body>
</html>
