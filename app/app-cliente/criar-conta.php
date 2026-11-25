<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!empty($nome) && !empty($email) && !empty($senha)) {
        try {
            $pdo = database();

            $stmtCheck = $pdo->prepare("SELECT id FROM clientes WHERE email = :email LIMIT 1");
            $stmtCheck->execute(['email' => $email]);
            
            if ($stmtCheck->fetch()) {
                $erro = 'Este e-mail já está registado no sistema.';
            } else {
                $senha = password_hash($senha, PASSWORD_DEFAULT);
                $stmtInsert = $pdo->prepare("INSERT INTO clientes (nome, email, senha) VALUES (:nome, :email, :senha)");
                $stmtInsert->execute([
                    'nome' => $nome,
                    'email' => $email,
                    'senha' => $senha
                ]);

                $sucesso = 'Conta criada com sucesso! Redirecionando para o login...';
                header("refresh:2;url=login.php");
            }
        } catch (Throwable $exception) {
            $erro = 'Erro ao criar a conta. Tente novamente mais tarde.';
        }
    } else {
        $erro = 'Por favor, preencha todos os campos obrigatórios.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta | BeautySchedule</title>
    <link rel="stylesheet" href="../css/criar-conta.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>
    
    <div class="fundo">
        <div class="container-login">
            <div class="lado-imagem">
                <img src="../imagens/Logo Beauty Schedule Verde e Bege.png" alt="Logot">
            </div>
            <div class="lado-formulario">
                <div class="texto">
                    <h3>Junte-se a nós</h3>
                    <h1>Crie a sua conta</h1>
                </div>

                <?php if (!empty($erro)): ?>
                    <div style="color: #a94442; background-color: #f2dede; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                        <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sucesso)): ?>
                    <div style="color: #3c763d; background-color: #dff0d8; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center;">
                        <?php echo htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <form action="criar-conta.php" method="POST" class="form-login">
                    <div class="campo">
                        <label for="nome">Nome Completo</label>
                        <input type="text" name="nome" id="nome" placeholder="Seu Nome" required>
                    </div>

                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="campo">
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" id="senha" placeholder="• • • • • •" required>
                    </div>

                    <button type="submit" class="btn-entrar" style="margin-top: 10px;">Cadastrar  →</button>
                </form>

                <h5 class="cadastro-link">Já tem uma conta? <a href="login.php">Faça login</a></h5>        
            </div>
        </div>
    </div>

</body>
</html>