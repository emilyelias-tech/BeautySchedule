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
                $erro = 'E-mail ou senha incorretos.';
            }
        } catch (Throwable $exception) {
            $erro = 'Erro. Tente novamente mais tarde.';
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

    <?php require_once __DIR__ . "/../nav.php"; ?>
    
    <div class="fundo">
        <div class="container-login">
            <div class="lado-imagem">
                <img src="/app/imagens/Logo Beauty Schedule Verde e Bege.png" alt="Logot">
            </div>

            <div class="lado-formulario">
                <div class="texto">
                    <h3>Bem-vindo(a) de volta</h3>
                    <h1>Entre na sua conta</h1>
                </div>

                <?php if (!empty($erro)): ?>
                    <div style="color: #a94442; background-color: #f2dede; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                        <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" class="form-login">
                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="campo">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="• • • • • •" required>
                    </div>

                    <div class="esqueci">
                        <a href="esqueci-senha.php">Esqueci minha senha</a>
                    </div>

                    <button type="submit" class="btn-entrar">Entrar  →</button>
                </form>

                <h5 class="cadastro-link">Ainda não tem conta? <a href="criar-conta.php">Cadastre-se</a></h5>        
            </div>
        </div>
    </div>

</body>
</html>