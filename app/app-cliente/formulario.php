<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = isset($_SESSION['usuario_id']);

$databaseStatus = 'Conectado';

try {
    require_once __DIR__ . '/../init.php';
    $pdo->query('SELECT 1');
} catch (Throwable $exception) {
    $databaseStatus = 'Indisponível';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/app/css/formulario.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php" ?>
    
    <div class="fundo">
        <div class="formulario">
            
        <form action="formmulario-enviado.php">

            <h2>Seus dados</h2>

                <label>Nome completo: </label>
                <input type="text" name="nome" id="nome" placeholder="Seu nome" required>

                <label>Telefone: </label>
                <input type="tel" name="telefone" id="telefone" placeholder="(11) 99999-9999" required>

                <label>E-mail: </label>
                <input type="email" name="email" id="email" placeholder="seu@gmail.com" required>

            <h2>Sobre o que deseja falar?</h2>

                <select name="categoria" id="gategoria">
                    <option value="agendamento">Agendamento</option>
                    <option value="duvidas">Duvidas sobre procedimentos</option>
                    <option value="valores">Valores e pagamentos</option>
                    <option value="cancelamento">Cancelamento ou alteração de horario</option>
                    <option value="informacoes">informações sobre profissionais</option>
                    <option value="problemas">Problemas com minha conta</option>
                    <option value="elogios">Elogios ou sugestões</option>
                    <option value="reclamacoes">Reclamações</option>
                    <option value="outros">Outros assuntos</option>
                </select>

            <h2>Assunto</h2>

                <textarea id="assunto" name="assunto" rows="2" cols="40" placeholder="Escreva seu assunto" required></textarea>

            <h2>Mensagem</h2>

                <textarea id="mensagem" name="mensagem" rows="6" cols="40" placeholder="Escreva sua mensagem" required></textarea>
            
            <button type="submit">Enviar formulario</button>
        </form>

        </div>
    </div>
</body>
</html>