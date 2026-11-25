<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clienteId =$_SESSION['cliente_id'] ?? 1;
$pdo = database();
$mensagemSucesso = '';$mensagemErro = '';

// Garante que a tabela de endereços exista no banco de dados (evita erro 500 se ainda não foi criada)
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS enderecos_clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            titulo VARCHAR(50) DEFAULT 'Casa',
            cep VARCHAR(15) NOT NULL,
            estado VARCHAR(2) NOT NULL,
            rua VARCHAR(150) NOT NULL,
            numero VARCHAR(20) NOT NULL,
            complemento VARCHAR(100) DEFAULT '',
            bairro VARCHAR(100) NOT NULL,
            cidade VARCHAR(100) NOT NULL,
            principal TINYINT(1) DEFAULT 0
        )
    ");
} catch (Throwable $e) {
    // Segue caso o usuário não tenha permissão DDL
}

// 1. Ação: Definir um endereço como Principal
if (isset($_GET['principal'])) {
    $idPrincipal = (int)$_GET['principal'];
    try {
        $pdo->prepare("UPDATE enderecos_clientes SET principal = 0 WHERE cliente_id = :cid")->execute(['cid' => $clienteId]);$pdo->prepare("UPDATE enderecos_clientes SET principal = 1 WHERE id = :id AND cliente_id = :cid")->execute(['id' => $idPrincipal, 'cid' =>$clienteId]);
        header("Location: enderecos.php");
        exit;
    } catch (Throwable $e) {$mensagemErro = "Erro ao definir endereço principal.";
    }
}

// 2. Ação: Excluir endereço
if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    try {
        $pdo->prepare("DELETE FROM enderecos_clientes WHERE id = :id AND cliente_id = :cid")->execute(['id' => $idExcluir, 'cid' =>$clienteId]);
        header("Location: enderecos.php");
        exit;
    } catch (Throwable $e) {$mensagemErro = "Erro ao excluir endereço.";
    }
}

// 3. Processar Formulário (Salvar Novo ou Editar Existente)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {$enderecoId  = isset($_POST['endereco_id']) ? (int)$_POST['endereco_id'] : 0;
    $titulo      = trim($_POST['titulo'] ?? 'Casa');
    $cep         = trim($_POST['cep'] ?? '');
    $estado      = trim($_POST['estado'] ?? '');
    $rua         = trim($_POST['rua'] ?? '');
    $numero      = trim($_POST['numero'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $bairro      = trim($_POST['bairro'] ?? '');
    $cidade      = trim($_POST['cidade'] ?? '');

    if (!empty($cep) && !empty($rua) && !empty($numero) && !empty($cidade) && !empty($estado)) {
        try {
            if ($enderecoId > 0) {
                // Atualizar endereço existente
                $stmt =$pdo->prepare("
                    UPDATE enderecos_clientes 
                    SET titulo = :titulo, cep = :cep, estado = :estado, rua = :rua, numero = :numero, complemento = :comp, bairro = :bairro, cidade = :cidade
                    WHERE id = :id AND cliente_id = :cid
                ");
                $stmt->execute([
                    'titulo' => $titulo ?: 'Casa',
                    'cep'    => $cep,
                    'estado' => $estado,
                    'rua'    => $rua,
                    'numero' => $numero,
                    'comp'   => $complemento,
                    'bairro' => $bairro,
                    'cidade' => $cidade,
                    'id'     => $enderecoId,
                    'cid'    => $clienteId
                ]);
                $mensagemSucesso = "Endereço atualizado com sucesso!";
            } else {
                // Verifica se o cliente já tem algum endereço; se não tiver, este será o Principal
                $stmtCount =$pdo->prepare("SELECT COUNT(*) FROM enderecos_clientes WHERE cliente_id = :cid");
                $stmtCount->execute(['cid' =>$clienteId]);
                $ehPrincipal = ((int)$stmtCount->fetchColumn() === 0) ? 1 : 0;

                // Inserir novo endereço
                $stmt =$pdo->prepare("
                    INSERT INTO enderecos_clientes (cliente_id, titulo, cep, estado, rua, numero, complemento, bairro, cidade, principal)
                    VALUES (:cid, :titulo, :cep, :estado, :rua, :numero, :comp, :bairro, :cidade, :principal)
                ");
                $stmt->execute([
                    'cid'       => $clienteId,
                    'titulo'    => $titulo ?: 'Casa',
                    'cep'       => $cep,
                    'estado'    => $estado,
                    'rua'       => $rua,
                    'numero'    => $numero,
                    'comp'      => $complemento,
                    'bairro'    => $bairro,
                    'cidade'    => $cidade,
                    'principal' => $ehPrincipal
                ]);
                $mensagemSucesso = "Novo endereço salvo com sucesso!";
            }
        } catch (Throwable $e) {$mensagemErro = "Erro ao salvar endereço: " . $e->getMessage();
        }
    } else {
        $mensagemErro = "Preencha pelo menos CEP, Estado, Rua, Número e Cidade.";
    }
}

// 4. Buscar endereço para edição (caso o usuário tenha clicado em "Editar endereço")
$enderecoEditando = null;
if (isset($_GET['editar'])) {
    $idEditar = (int)$_GET['editar'];
    try {
        $stmtEdit =$pdo->prepare("SELECT * FROM enderecos_clientes WHERE id = :id AND cliente_id = :cid LIMIT 1");
        $stmtEdit->execute(['id' => $idEditar, 'cid' =>$clienteId]);
        $enderecoEditando =$stmtEdit->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {$enderecoEditando = null;
    }
}

// 5. Listar todos os endereços do cliente (colocando o Principal no topo)
$listaEnderecos = [];
try {
    $stmtList =$pdo->prepare("SELECT * FROM enderecos_clientes WHERE cliente_id = :cid ORDER BY principal DESC, id DESC");
    $stmtList->execute(['cid' =>$clienteId]);
    $listaEnderecos =$stmtList->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {$listaEnderecos = [];
}

// 6. Buscar dados dinâmicos do Perfil (Lateral Esquerda)
try {
    $stmt =$pdo->prepare("SELECT * FROM clientes WHERE id = :id LIMIT 1");
    $stmt->execute(['id' =>$clienteId]);
    $cliente =$stmt->fetch(PDO::FETCH_ASSOC);
} catch (Throwable $e) {$cliente = false;
}

if (!$cliente) {$cliente = [
        'nome' => 'Emily Elias',
        'email' => 'emilyelias@gmail.com',
        'pontos' => 680,
        'foto' => ''
    ];
}

// Estatísticas dinâmicas
$totalCuidados = 12;
$avaliacaoMedia = 4.9;
try {
    $stmtAg =$pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE cliente_id = :id");
    $stmtAg->execute(['id' =>$clienteId]);
    $c =$stmtAg->fetchColumn();
    if ($c !== false &&$c > 0) $totalCuidados = (int)$c;

    $stmtAv =$pdo->prepare("SELECT AVG(nota) FROM avaliacoes WHERE cliente_id = :id");
    $stmtAv->execute(['id' =>$clienteId]);
    $a =$stmtAv->fetchColumn();
    if ($a !== false && $a !== null)$avaliacaoMedia = round((float)$a, 1); } catch (Throwable$e) {}

// Iniciais do Avatar
$nomeCompleto =$cliente['nome'] ?? 'Cliente';
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
    <link rel="stylesheet" href="/app/css/configuracao-endereco.css">
    <title>Beauty Schedule - Meus Endereços</title>
</head>
<body>
    <?php include __DIR__ . '/../nav.php'; ?>

    <main class="container-principal">
        <aside class="coluna-esquerda">
            
            <section class="perfil-card">
                <div class="avatar">
                    <?php if (!empty($cliente['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($cliente['foto'], ENT_QUOTES, 'UTF-8'); ?>" alt="Foto" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8'); ?>
                    <?php endif; ?>
                </div>
                <h4><?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="email"><?php echo htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                <hr>
                <div class="estatisticas">
                    <div class="stat-item">
                        <strong><?php echo $totalCuidados; ?></strong>
                        <span>Cuidados Realizados</span>
                    </div>
                    <div class="divisor-vertical"></div>
                    <div class="stat-item">
                        <strong><?php echo number_format($avaliacaoMedia, 1, ',', '.'); ?></strong>
                        <span>Sua Avaliação média</span>
                    </div>
                </div>
                <hr>
            </section>

            <nav class="nav-perfil">
                <a href="configuracao-dados-pessoais.php">Dados pessoais <span>&gt;</span></a>
                <a href="#" class="ativo">Endereços <span>&gt;</span></a>
                <a href="cartoes.php">Meus cartões <span>&gt;</span></a>
                <a href="configuracao.php">Configurações <span>&gt;</span></a>
                <a href="historico.php">Histórico de pontos <span>&gt;</span></a>
            </nav>

            <!-- Card Beauty Club -->
            <section class="beauty-club-card">
                <p class="club-titulo">Beauty club</p>
                <div class="pontos-info">
                    <span class="pontos-numero"><?php echo (int)($cliente['pontos'] ?? 680); ?></span>
                    <span class="pontos-texto">Pontos</span>
                </div>
                <p class="club-desc">Você pode trocar seus pontos por até R$40 de desconto</p>
                <a href="resgatar.php" class="resgatar-