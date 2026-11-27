<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clienteId = $_SESSION['usuario_id'] ?? $_SESSION['cliente_id'] ?? 1; 
$pdo = database();
$mensagemSucesso = '';
$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel   = trim($_POST['Telefone'] ?? '');
    $cpf   = trim($_POST['CPF'] ?? '');
    $nasc  = trim($_POST['data-nasc'] ?? '');

    if (!empty($nome) && !empty($email)) {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE clientes SET nome = :nome, email = :email, telefone = :tel, cpf = :cpf, data_nascimento = :nasc WHERE id = :id");
            $stmtUpdate->execute([
                'nome'  => $nome,
                'email' => $email,
                'tel'   => $tel,
                'cpf'   => $cpf,
                'nasc'  => $nasc,
                'id'    => $clienteId
            ]);
            $mensagemSucesso = "Dados atualizados com sucesso!";
        } catch (Throwable $e) {
            try {
                $stmtUpdate = $pdo->prepare("UPDATE clientes SET nome = :nome, email = :email WHERE id = :id");
                $stmtUpdate->execute([
                    'nome'  => $nome,
                    'email' => $email,
                    'id'    => $clienteId
                ]);
                $mensagemSucesso = "Dados básicos atualizados com sucesso!";
            } catch (Throwable $ex) {
                $mensagemErro = "Erro ao atualizar: " . $ex->getMessage();
            }
        }
    } else {
        $mensagemErro = "O nome e o e-mail são obrigatórios.";
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $cliente = false;
}

if (!$cliente) {
    $cliente = [
        'nome' => 'Ana Silva',
        'email' => 'ana@gmail.com',
        'telefone' => '11987654321',
        'cpf' => '',
        'data_nascimento' => '',
        'pontos' => 680,
        'foto' => ''
    ];
}

$totalCuidados = 12;
$avaliacaoMedia = 4.9;

try {
    $stmtAg = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE cliente_id = :id");
    $stmtAg->execute(['id' => $clienteId]);
    $count = $stmtAg->fetchColumn();
    if ($count !== false && $count > 0) {
        $totalCuidados = (int)$count;
    }
} catch (Throwable $e) {
}

try {
    $stmtAv = $pdo->prepare("SELECT AVG(nota) FROM avaliacoes WHERE cliente_id = :id");
    $stmtAv->execute(['id' => $clienteId]);
    $avg = $stmtAv->fetchColumn();
    if ($avg !== false && $avg !== null) {
        $avaliacaoMedia = round((float)$avg, 1);
    }
} catch (Throwable $e) {
}

$nomeCompleto = $cliente['nome'] ?? 'Cliente';
$partesNome = explode(' ', trim($nomeCompleto));
$iniciais = strtoupper(substr($partesNome[0], 0, 1));
if (count($partesNome) > 1) {
    $iniciais .= strtoupper(substr(end($partesNome), 0, 1));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/configuracao-dados-pessoais.css">
    <title>Beauty Schedule - Dados Pessoais</title>
</head>
<body>
    <?php require_once __DIR__ . "/../nav-cli.php"; ?>
    
<main>
    <div class="coluna-esquerda">

        <section class="perfil-section">
            <div class="avatar">
                <?php if (!empty($cliente['foto'])): ?>
                    <img src="<?php echo htmlspecialchars($cliente['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <?php echo htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            </div>
            <h4><?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <h5><?php echo htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?></h5>

            <div class="stats-box">
                <div class="stat-item">
                    <span class="stat-num"><?php echo $totalCuidados; ?></span>
                    <span class="stat-desc">Cuidados realizados</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-num"><?php echo number_format($avaliacaoMedia, 1, ',', '.'); ?></span>
                    <span class="stat-desc">Sua avaliação média</span>
                </div>
            </div>
            <br>
            <a href="login.php">Sair da conta</a>
        </section>

        <section class="nav-perfil">
            <a href="/app/app-cliente/configuracao-dados-pessoais.php"><span>Dados pessoais</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/configuracao-enderecos.php"><span>Endereços</span> <span>&gt;</span></a>
            <a href="#" class="ativo"><span>Meus cartões</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/configuracao-preferencias.php"><span>Configurações</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/configuracao-historico.php"><span>Histórico de pontos</span> <span>&gt;</span></a>
        </section>

        <section class="club-card">
            <h4 class="club-title">Beauty club</h4>
            <div class="club-points-box">
                <span class="club-num"><?php echo (int)($cliente['pontos'] ?? 680); ?></span>
                <span class="club-label">Pontos</span>
            </div>
            <p class="club-desc">Você pode trocar seus pontos por até R$40 de desconto</p>
            <a href="resgatar.php" class="club-link">Resgatar benefício &rarr;</a>
        </section>
    </div>

</main>
    
</body>
</html>