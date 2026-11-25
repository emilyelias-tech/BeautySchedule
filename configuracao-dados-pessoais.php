<?php
session_start();

if (!isset($_SESSION['dados_pessoais'])) {
    $_SESSION['dados_pessoais'] = [
        'nome' => 'Emily Elias',
        'email' => 'emilyelias@gmail.com',
        'data_nasc' => '',
        'telefone' => '',
        'cpf' => ''
    ];
}

if (!isset($_SESSION['pontos'])) {
    $_SESSION['pontos'] = 680;
}

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) !== 11 or preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

$dados = $_SESSION['dados_pessoais'];
$erros = [];
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nasc = trim($_POST['data-nasc'] ?? '');
    $telefone = trim($_POST['Telefone'] ?? '');
    $cpf = trim($_POST['CPF'] ?? '');

    $dados = [
        'nome' => $nome,
        'email' => $email,
        'data_nasc' => $data_nasc,
        'telefone' => $telefone,
        'cpf' => $cpf
    ];

    if (strlen($nome) < 3 or !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nome)) {
        $erros['nome'] = 'Digite um nome válido (apenas letras, mínimo 3 caracteres).';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros['email'] = 'Digite um endereço de e-mail válido (ex: nome@email.com).';
    }

    if ($data_nasc !== '') {
        if (!preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data_nasc, $partes_data)) {
            $erros['data_nasc'] = 'Use o formato DD/MM/AAAA (ex: 15/05/1998).';
        } elseif (!checkdate((int)$partes_data[2], (int)$partes_data[1], (int)$partes_data[3]) or (int)$partes_data[3] < 1900 or (int)$partes_data[3] > (int)date('Y')) {
            $erros['data_nasc'] = 'Data de nascimento inválida.';
        }
    }

    if ($telefone !== '') {
        $tel_numeros = preg_replace('/[^0-9]/', '', $telefone);
        if (strlen($tel_numeros) < 10 or strlen($tel_numeros) > 11) {
            $erros['telefone'] = 'Digite um telefone válido com DDD (ex: (11) 99999-9999).';
        }
    }

    if ($cpf !== '') {
        if (!validarCPF($cpf)) {
            $erros['cpf'] = 'CPF inválido. Verifique os números digitados.';
        }
    }

    if (empty($erros)) {
        $_SESSION['dados_pessoais'] = $dados;
        header("Location: " . $_SERVER['PHP_SELF'] . "?salvo=1");
        exit;
    }
}

if (isset($_GET['salvo'])) {
    $mensagem_sucesso = 'Dados salvos com sucesso!';
}

$nome_cartao = $_SESSION['dados_pessoais']['nome'];
$email_cartao = $_SESSION['dados_pessoais']['email'];
$partes_nome = array_values(array_filter(explode(' ', $nome_cartao)));
if (count($partes_nome) >= 2) {
    $iniciais = strtoupper(substr($partes_nome[0], 0, 1) . substr(end($partes_nome), 0, 1));
} elseif (count($partes_nome) === 1) {
    $iniciais = strtoupper(substr($partes_nome[0], 0, 2));
} else {
    $iniciais = 'BS';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="configuracao-dados-pessoais.css">
    <title>Beauty Schedule - Dados Pessoais</title>
</head>
<body>
<header> 
    <div class="logo">
        <h1>BS</h1>
        <div>
            <p>Beauty</p>
            <p>Schedule</p>
        </div>
    </div>
</header>

<main>
    <div class="coluna-esquerda">
        <section class="perfil-section">
            <div class="avatar"><?= htmlspecialchars($iniciais) ?></div>
            <h4><?= htmlspecialchars($nome_cartao) ?></h4>
            <h6><?= htmlspecialchars($email_cartao) ?></h6>

            <div class="stats-box">
                <div class="stat-item">
                    <span class="stat-num">12</span>
                    <span class="stat-desc">Cuidados realizados</span>
                </div>

                <div class="stat-divider"></div>

                <div class="stat-item">
                    <span class="stat-num">4.9</span>
                    <span class="stat-desc">Sua avaliação média</span>
                </div>
            </div>
        </section>

        <section class="nav-perfil">
            <a href="configuracao-dados-pessoais.php" class="ativo"><span>Dados pessoais</span> <span>&gt;</span></a>
            <a href="configuracao-enderecos.php"><span>Endereços</span> <span>&gt;</span></a>
            <a href="cartoes.php"><span>Meus cartões</span> <span>&gt;</span></a>
            <a href="configuracao.php"><span>Configurações</span> <span>&gt;</span></a>
            <a href="historico.php"><span>Histórico de pontos</span> <span>&gt;</span></a>
        </section>

        <section class="club-card">
            <h4 class="club-title">Beauty club</h4>

            <div class="club-points-box">
                <span class="club-num"><?= (int)$_SESSION['pontos'] ?></span>
                <span class="club-label">Pontos</span>
            </div>

            <p class="club-desc">Voce pode trocar seus pontos por até R$40 de desconto</p>

            <a href="historico.php" class="club-link">Resgatar beneficio &rarr;</a>
        </section>
    </div>

    <section class="dados-section">
        <p class="subtitulo">Dados pessoais</p> 
        <h2>Seu perfil</h2> 

        <?php if ($mensagem_sucesso !== ''): ?>
            <p style="color: #4D5637; font-weight: bold; margin-bottom: 18px;"><?= htmlspecialchars($mensagem_sucesso) ?></p>
        <?php endif; ?>
        
        <form class="campo-formulario" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($dados['nome']) ?>" pattern="[A-Za-zÀ-ÿ\s]{3,}" title="Apenas letras e espaços (mínimo 3 caracteres)" required>
            <?php if (isset($erros['nome'])): ?>
                <span style="color: #7a2e2e; font-size: 13px; margin-top: -15px; margin-bottom: 15px; display: block;"><?= $erros['nome'] ?></span>
            <?php endif; ?>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" required>
            <?php if (isset($erros['email'])): ?>
                <span style="color: #7a2e2e; font-size: 13px; margin-top: -15px; margin-bottom: 15px; display: block;"><?= $erros['email'] ?></span>
            <?php endif; ?>
               
            <label for="data-nasc">Data de nascimento:</label>
            <input type="text" id="data-nasc" name="data-nasc" value="<?= htmlspecialchars($dados['data_nasc']) ?>" placeholder="DD/MM/AAAA" pattern="\d{2}/\d{2}/\d{4}" title="Formato DD/MM/AAAA (ex: 15/05/1998)">
            <?php if (isset($erros['data_nasc'])): ?>
                <span style="color: #7a2e2e; font-size: 13px; margin-top: -15px; margin-bottom: 15px; display: block;"><?= $erros['data_nasc'] ?></span>
            <?php endif; ?>

            <label for="Telefone">Telefone:</label>
            <input type="tel" id="Telefone" name="Telefone" value="<?= htmlspecialchars($dados['telefone']) ?>" placeholder="(00) 00000-0000" pattern="\(?\d{2}\)?\s?\d{4,5}-?\d{4}" title="Digite um telefone válido com DDD (ex: (11) 99999-9999)">
            <?php if (isset($erros['telefone'])): ?>
                <span style="color: #7a2e2e; font-size: 13px; margin-top: -15px; margin-bottom: 15px; display: block;"><?= $erros['telefone'] ?></span>
            <?php endif; ?>

            <label for="CPF">CPF:</label>
            <input type="text" id="CPF" name="CPF" value="<?= htmlspecialchars($dados['cpf']) ?>" placeholder="000.000.000-00" pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}" title="Digite um CPF válido com 11 números (ex: 000.000.000-00)">
            <?php if (isset($erros['cpf'])): ?>
                <span style="color: #7a2e2e; font-size: 13px; margin-top: -15px; margin-bottom: 15px; display: block;"><?= $erros['cpf'] ?></span>
            <?php endif; ?>

            <button type="submit" style="align-self: flex-start; background-color: #4D5637; color: #FFFFFF; font-size: 16px; border: none; border-radius: 25px; padding: 15px 34px; margin-top: 10px; cursor: pointer;">
                Salvar alterações
            </button>
        </form>
    </section>  
</main>
    
</body>
</html>