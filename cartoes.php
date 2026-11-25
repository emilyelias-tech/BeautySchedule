<?php
session_start();

// Garante que os dados pessoais existam na sessão
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

// Puxa nome, e-mail e calcula as iniciais do avatar a partir da sessão
$nome_cartao = $_SESSION['dados_pessoais']['nome'];
$email_cartao = $_SESSION['dados_pessoais']['email'];
$partes_nome = array_values(array_filter(explode(' ', $nome_cartao)));
$iniciais = '';
if (count($partes_nome) >= 2) {
    $primeira = substr($partes_nome[0], 0, 1);
    $ultima = substr(end($partes_nome), 0, 1);
    $iniciais = strtoupper($primeira . $ultima);
} elseif (count($partes_nome) === 1) {
    $iniciais = strtoupper(substr($partes_nome[0], 0, 2));
} else {
    $iniciais = 'BS';
}

// Inicializa com o cartão padrão caso ainda não exista na sessão
if (!isset($_SESSION['cartoes'])) {
    $_SESSION['cartoes'] = [
        [
            'bandeira' => 'Visa',
            'ultimos_digitos' => '4821',
            'titular' => 'Emily Elias',
            'validade' => '09/29',
            'principal' => true
        ]
    ];
}

// Apaga o cartão somente após clicar em "Excluir" no modal
if (isset($_GET['confirmar_apagar'])) {
    $indice = (int)$_GET['confirmar_apagar'];
    if (isset($_SESSION['cartoes'][$indice])) {
        unset($_SESSION['cartoes'][$indice]);
        $_SESSION['cartoes'] = array_values($_SESSION['cartoes']);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Verifica se clicou na lixeira (?apagar=indice) para abrir o modal centralizado
$indice_confirmar_exclusao = isset($_GET['apagar']) ? (int)$_GET['apagar'] : null;

// Verifica se clicou em "+ Adicionar Cartão" (?adicionar=1)
$mostrar_formulario = isset($_GET['adicionar']);
$erro_cartao = '';

// Processa o cadastro de novo cartão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_cartao = preg_replace('/[^0-9]/', '', $_POST['numero_cartao'] ?? '');
    $titular = trim($_POST['titular'] ?? '');
    $validade = trim($_POST['validade'] ?? '');
    $cvv = preg_replace('/[^0-9]/', '', $_POST['cvv'] ?? '');
    $bandeira = trim($_POST['bandeira'] ?? 'Visa');

    if (strlen($numero_cartao) < 13 or strlen($numero_cartao) > 19) {
        $erro_cartao = 'Digite um número de cartão válido (13 a 19 dígitos).';
        $mostrar_formulario = true;
    } elseif (strlen($titular) < 3) {
        $erro_cartao = 'Digite o nome do titular como está no cartão.';
        $mostrar_formulario = true;
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $validade, $partes_val)) {
        $erro_cartao = 'Digite a validade no formato MM/AA (ex: 09/29).';
        $mostrar_formulario = true;
    } elseif (
        (int)('20' . $partes_val[2]) < (int)date('Y') or
        ((int)('20' . $partes_val[2]) === (int)date('Y') and (int)$partes_val[1] < (int)date('m'))
    ) {
        $erro_cartao = 'Este cartão está vencido.';
        $mostrar_formulario = true;
    } elseif (strlen($cvv) < 3 or strlen($cvv) > 4) {
        $erro_cartao = 'Digite um CVV válido (3 ou 4 dígitos).';
        $mostrar_formulario = true;
    } else {
        $ultimos_4 = substr($numero_cartao, -4);
        $eh_principal = count($_SESSION['cartoes']) === 0;

        $_SESSION['cartoes'][] = [
            'bandeira' => $bandeira,
            'ultimos_digitos' => $ultimos_4,
            'titular' => $titular,
            'validade' => $validade,
            'principal' => $eh_principal
        ];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cartoes.css">
    <title>Beauty Schedule - Meus Cartões</title>
</head>
<body>

<!-- Modal de Confirmação centralizado na tela -->
<?php if ($indice_confirmar_exclusao !== null and isset($_SESSION['cartoes'][$indice_confirmar_exclusao])): ?>
    <div class="modal-overlay ativo">
        <div class="modal-box">
            <div class="modal-topo">
                <h3 class="modal-titulo">Excluir cartão</h3>
            </div>
            <p class="modal-descricao">
                Tem certeza que deseja excluir o cartão <strong><?= htmlspecialchars($_SESSION['cartoes'][$indice_confirmar_exclusao]['bandeira']) ?> &bull;&bull;&bull;&bull; <?= htmlspecialchars($_SESSION['cartoes'][$indice_confirmar_exclusao]['ultimos_digitos']) ?></strong>? Esta ação não poderá ser desfeita.
            </p>
            <div class="modal-acoes">
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn-modal-manter">Cancelar</a>
                <a href="?confirmar_apagar=<?= $indice_confirmar_exclusao ?>" class="btn-modal-confirmar-delete">Excluir</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<header> 
    <div class="logo">
        <h1>BS</h1>
        <div>
            <p>Beauty</p>
            <p>Schedule</p>
        </div>
    </div>

    <nav class="menu-topo">
        <a href="index.php">Inicio</a>
        <a href="produtos.php">Produtos</a>
        <a href="agendar.php">Agendar</a>
        <a href="consulta.php">Consulta</a>
        <a href="procedimentos.php">Procedimentos</a>
    </nav>

    <div class="icones-topo">
        <a href="carrinho.php" class="icone-circulo" title="Sacola">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4D5637" stroke-width="1.8">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
        </a>
        <a href="configuracao-dados-pessoais.php" class="icone-perfil-topo" title="Perfil">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#D8D0B4">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
        </a>
    </div>
</header>

<main>
    <!-- 1. Caixa da esquerda que agrupa os cartões -->
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
            <a href="configuracao-dados-pessoais.php"><span>Dados pessoais</span> <span>&gt;</span></a>
            <a href="configuracao-enderecos.php"><span>Endereços</span> <span>&gt;</span></a>
            <a href="cartoes.php" class="ativo"><span>Meus cartões</span> <span>&gt;</span></a>
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

    <!-- 2. Coluna da direita: Meus Cartões -->
    <section class="dados-section">
        <div class="topo-cartoes">
            <div>
                <p class="subtitulo">Pagamento Seguro</p> 
                <h2>Meus Cartões</h2> 
            </div>
            <a href="?adicionar=1#form-cartao" class="btn-add-cartao">+ Adicionar Cartão</a>
        </div>

        <!-- Lista de cartões salvos -->
        <div class="lista-cartoes">
            <?php foreach ($_SESSION['cartoes'] as $index => $cartao): ?>
                <div class="cartao-salvo-card">
                    <!-- Desenho do cartão à esquerda -->
                    <div class="cartao-visual">
                        <span class="bolinha-1"></span>
                        <span class="bolinha-2"></span>
                    </div>

                    <!-- Informações do cartão -->
                    <div class="cartao-info">
                        <span class="cartao-tag">
                            <?= $cartao['principal'] ? 'Cartao Principal' : 'Cartao Salvo' ?>
                        </span>
                        <h3 class="cartao-numero">
                            <?= htmlspecialchars($cartao['bandeira']) ?> &bull;&bull;&bull;&bull; <?= htmlspecialchars($cartao['ultimos_digitos']) ?>
                        </h3>
                        <p class="cartao-titular">
                            <?= htmlspecialchars($cartao['titular']) ?> &nbsp;&bull;&nbsp; <?= htmlspecialchars($cartao['validade']) ?>
                        </p>
                    </div>

                    <!-- Ícone da lixeira para apagar -->
                    <a href="?apagar=<?= $index ?>" class="btn-lixeira" title="Excluir cartão">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#4D5637" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulário para adicionar novo cartão (abre ao clicar em + Adicionar Cartão) -->
        <?php if ($mostrar_formulario): ?>
            <form class="form-cartao" id="form-cartao" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <h3 class="titulo-form-cartao">Novo Cartão</h3>

                <?php if ($erro_cartao !== ''): ?>
                    <p class="erro-cartao"><?= htmlspecialchars($erro_cartao) ?></p>
                <?php endif; ?>

                <div class="linha-dupla">
                    <div class="campo-grupo">
                        <label for="numero_cartao">Número do cartão</label>
                        <input type="text" id="numero_cartao" name="numero_cartao" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>

                    <div class="campo-grupo">
                        <label for="bandeira">Bandeira</label>
                        <select id="bandeira" name="bandeira" required>
                            <option value="Visa">Visa</option>
                            <option value="Mastercard">Mastercard</option>
                            <option value="Elo">Elo</option>
                            <option value="American Express">American Express</option>
                        </select>
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="titular">Nome impresso no cartão</label>
                    <input type="text" id="titular" name="titular" value="<?= htmlspecialchars($nome_cartao) ?>" required>
                </div>

                <div class="linha-dupla">
                    <div class="campo-grupo">
                        <label for="validade">Validade (MM/AA)</label>
                        <input type="text" id="validade" name="validade" placeholder="09/29" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" title="Formato MM/AA (ex: 09/29)" required>
                    </div>

                    <div class="campo-grupo">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" pattern="\d{3,4}" title="3 ou 4 dígitos numéricos" required>
                    </div>
                </div>

                <div class="botoes-form-cartao">
                    <button type="submit" class="btn-salvar">Salvar cartão</button>
                    <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn-fechar-form">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </section>  
</main>
    
</body>
</html>