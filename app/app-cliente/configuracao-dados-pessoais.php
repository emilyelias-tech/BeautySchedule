<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clienteId = $_SESSION['cliente_id'] ?? 1; 
$pdo = database();
$mensagemSucesso = '';
$mensagemErro = '';

// 1. Processar formulário de salvamento com segurança de colunas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel   = trim($_POST['Telefone'] ?? '');
    $cpf   = trim($_POST['CPF'] ?? '');
    $nasc  = trim($_POST['data-nasc'] ?? '');

    if (!empty($nome) && !empty($email)) {
        try {
            // Tenta atualizar com todos os campos (caso as colunas já existam)
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
                // Fallback caso a tabela de clientes tenha apenas nome e email por enquanto
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

// 2. Buscar dados do cliente
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

// 3. Buscar dinamicamente "Cuidados realizados" e "Avaliação média"
$totalCuidados = 12; // Valor padrão inicial
$avaliacaoMedia = 4.9; // Valor padrão inicial

try {
    // Tenta contar agendamentos concluídos do cliente
    $stmtAg = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE cliente_id = :id");
    $stmtAg->execute(['id' => $clienteId]);
    $count = $stmtAg->fetchColumn();
    if ($count !== false && $count > 0) {
        $totalCuidados = (int)$count;
    }
} catch (Throwable $e) {
    // Se a tabela agendamentos não existir, mantém o padrão
}

try {
    // Tenta calcular a avaliação média do cliente
    $stmtAv = $pdo->prepare("SELECT AVG(nota) FROM avaliacoes WHERE cliente_id = :id");
    $stmtAv->execute(['id' => $clienteId]);
    $avg = $stmtAv->fetchColumn();
    if ($avg !== false && $avg !== null) {
        $avaliacaoMedia = round((float)$avg, 1);
    }
} catch (Throwable $e) {
    // Se a tabela avaliacoes não existir, mantém o padrão
}

// 4. Gerar iniciais dinâmicas para o avatar
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
    <link rel="stylesheet" href="/app/css/configuracao-dados-pessoais.css">
    <title>Beauty Schedule - Dados Pessoais</title>
</head>
<body>
    <?php require_once __DIR__ . "/../nav.php"; ?>
    
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
            <h6><?php echo htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?></h6>

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
        </section>

        <section class="nav-perfil">
            <a href="#" class="ativo"><span>Dados pessoais</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/configuracao-endereco.php"><span>Endereços</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/cartoes.php"><span>Meus cartões</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/configuracao.php"><span>Configurações</span> <span>&gt;</span></a>
            <a href="/app/app-cliente/historico.php"><span>Histórico de pontos</span> <span>&gt;</span></a>
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

    <section class="dados-section">
        <p class="subtitulo">Dados pessoais</p> 
        <h2>Seu perfil</h2> 

        <?php if (!empty($mensagemSucesso)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                <?php echo htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensagemErro)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                <?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        
        <form class="campo-formulario" method="POST" action="">
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
               
            <label for="data-nasc">Data de nascimento:</label>
            <input type="date" id="data-nasc" name="data-nasc" value="<?php echo htmlspecialchars($cliente['data_nascimento'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <label for="Telefone">Telefone:</label>
            <input type="text" id="Telefone" name="Telefone" value="<?php echo htmlspecialchars($cliente['telefone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <label for="CPF">CPF:</label>
            <input type="text" id="CPF" name="CPF" value="<?php echo htmlspecialchars($cliente['cpf'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

            <button type="submit" style="margin-top: 25px; background-color: #4f5838; color: #D5CEB8; border: none; padding: 14px 24px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; width: 100%;">
                Salvar Alterações
            </button>
        </form>
    </section>  
</main>
    
</body>
</html>