<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!empty($email)) {
        try {
            $pdo = database();
            
            // Verifica se o e-mail existe na tabela de clientes
            $stmt = $pdo->prepare("SELECT id, nome FROM clientes WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                // Aqui você pode integrar o envio real de e-mail (ex: PHPMailer)
                // Para fins práticos do projeto, exibimos uma mensagem de sucesso simulada
                $mensagem = 'As instruções para redefinir a palavra-passe foram enviadas para o seu e-mail.';
            } else {
                $erro = 'Não encontramos nenhuma conta associada a este e-mail.';
            }
        } catch (Throwable $exception) {
            $erro = 'Ocorreu um erro ao processar a solicitação. Tente novamente mais tarde.';
        }
    } else {
        $erro = 'Por favor, insira o seu e-mail.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/login.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>
    
    <div class="fundo">
        <div class="texto">
            <h3>Recuperação de Acesso</h3>
            <h1>Esqueceu a sua senha?</h1>
        </div>

        <img src="/app/imagens/Logotipo Botânico BS Beauty Schedule.png" alt="logo">

        <!-- Mensagens de Feedback -->
        <?php if (!empty($erro)): ?>
            <div style="color: #a94442; background-color: #f2dede; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensagem)): ?>
            <div style="color: #3c763d; background-color: #dff0d8; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                <?php echo htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="esqueci-senha.php" method="POST" class="campo">
            <label><h4>E-mail</h4></label>
            <input type="email" name="email" id="email" placeholder="seu@email.com" required>

            <button type="submit" style="margin-top: 20px;">Enviar Instruções  →</button>
        </form>

        <h5 style="margin-top: 20px;">Lembrou-se da senha? <a class="a" href="login.php">Faça login</a></h5>        
    </div>

</body>
</html>