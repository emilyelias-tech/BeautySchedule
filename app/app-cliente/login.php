<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!empty($email) && !empty($senha)) {
        try {
            $pdo = database();
            
            $stmt = $pdo->prepare("SELECT * FROM clientes WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'] ?? 'Cliente';

                header("Location: index.php");
                exit;
            } else {
                $erro = 'E-mail ou palavra-passe incorretos.';
            }
        } catch (Throwable $exception) {
            $erro = 'Erro ao tentar ligar à base de dados. Tente novamente mais tarde.';
        }
    } else {
        $erro = 'Por favor, preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/login.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php" ?>
    
    <div class="fundo">
        <div class="texto">
            <h3>Bem-vindo(a) de volta</h3>
            <h1>Entre na sua conta</h1>
        </div>

        <img src="/app/imagens/Logotipo Botânico BS Beauty Schedule.png" alt="logo">

        <?php if (!empty($erro)): ?>
            <div style="color: #a94442; background-color: #f2dede; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="campo">
            <label><h4>E-mail</h4></label>
            <input type="email" name="email" id="email" placeholder="seu@email.com" required>

            <label><h4>Senha</h4></label>
            <input type="password" name="senha" id="senha" placeholder="• • • • • • " required>

            <div class="esqueci">
                <a href="esqueci-senha.php">Esqueci minha senha</a>
            </div>

            <button type="submit">Entrar  →</button>
        </form>

        <h5>Ainda não tem conta? <a class="a" href="criar-conta.php">Cadastre-se</a></h5>        
    </div>
</body>
</html>