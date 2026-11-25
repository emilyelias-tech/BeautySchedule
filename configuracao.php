<?php
session_start();

// 1. Inicializa dados pessoais na sessão
if (!isset($_SESSION['dados_pessoais'])) {
    $_SESSION['dados_pessoais'] = [
        'nome' => 'Emily Elias',
        'email' => 'emilyelias@gmail.com',
        'data_nasc' => '15/05/1998',
        'telefone' => '(11) 98765-4321',
        'cpf' => ''
    ];
}

// 2. Inicializa senha padrão
if (!isset($_SESSION['senha_usuario'])) {
    $_SESSION['senha_usuario'] = '123456';
}

// 3. Inicializa pontos do Beauty Club
if (!isset($_SESSION['pontos'])) {
    $_SESSION['pontos'] = 680;
}

// 4. Inicializa configurações
if (!isset($_SESSION['configuracoes'])) {
    $_SESSION['configuracoes'] = [
        'tema' => 'claro',
        'texto_maior' => false,
        'alto_contraste' => false,
        'reduzir_animacoes' => false,
        'notif_agendamento' => true,
        'notif_email' => true,
        'notif_pedidos' => true,
        'notif_novidades' => false,
        'seg_biometria' => false,
        'seg_perfil_visivel' => true,
        'idioma' => 'pt-BR',
        'fuso' => 'America/Sao_Paulo'
    ];
}

// 5. Inicializa dispositivos conectados
if (!isset($_SESSION['dispositivos'])) {
    $_SESSION['dispositivos'] = [
        ['nome' => 'Windows • Chrome (Este dispositivo)', 'local' => 'São Caetano do Sul, SP', 'atual' => true],
        ['nome' => 'iPhone 15 • Safari', 'local' => 'São Paulo, SP', 'atual' => false]
    ];
}

// Ação 1: Baixar meus dados completos
if (isset($_GET['baixar_dados'])) {
    $conteudo = "=== BEAUTY SCHEDULE - RELATÓRIO DE DADOS ===\n\n";
    $conteudo .= "Nome: " . $_SESSION['dados_pessoais']['nome'] . "\n";
    $conteudo .= "E-mail: " . $_SESSION['dados_pessoais']['email'] . "\n";
    $conteudo .= "Data de Nascimento: " . ($_SESSION['dados_pessoais']['data_nasc'] ?: 'Não informado') . "\n";
    $conteudo .= "Telefone: " . ($_SESSION['dados_pessoais']['telefone'] ?: 'Não informado') . "\n";
    $conteudo .= "CPF: " . ($_SESSION['dados_pessoais']['cpf'] ?: 'Não informado') . "\n";
    $conteudo .= "Saldo Beauty Club: " . $_SESSION['pontos'] . " Pontos\n";
    $conteudo .= "Endereços salvos: " . (isset($_SESSION['enderecos']) ? count($_SESSION['enderecos']) : 1) . "\n";
    $conteudo .= "Cartões salvos: " . (isset($_SESSION['cartoes']) ? count($_SESSION['cartoes']) : 1) . "\n";

    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="meus-dados-beauty-schedule.txt"');
    echo $conteudo;
    exit;
}

// Ação 2: Excluir conta
if (isset($_GET['confirmar_excluir_conta'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ação 3: Trocar Tema (Claro / Escuro / Automático)
if (isset($_GET['set_tema']) && in_array($_GET['set_tema'], ['claro', 'escuro', 'automatico'])) {
    $_SESSION['configuracoes']['tema'] = $_GET['set_tema'];
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ação 4: Desconectar dispositivo remoto
if (isset($_GET['desconectar_disp'])) {
    $idx = (int)$_GET['desconectar_disp'];
    if (isset($_SESSION['dispositivos'][$idx]) && !$_SESSION['dispositivos'][$idx]['atual']) {
        unset($_SESSION['dispositivos'][$idx]);
        $_SESSION['dispositivos'] = array_values($_SESSION['dispositivos']);
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?abrir=dispositivos");
    exit;
}

$mostrar_modal_excluir = isset($_GET['excluir_conta']);
$painel_aberto = $_GET['abrir'] ?? '';
$mensagem_salvo = '';
$erro_senha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se enviou o formulário de alteração de senha
    if (isset($_POST['salvar_nova_senha'])) {
        $atual = $_POST['senha_atual'] ?? '';
        $nova = $_POST['nova_senha'] ?? '';
        $confirma = $_POST['confirma_senha'] ?? '';

        if ($atual !== $_SESSION['senha_usuario']) {
            $erro_senha = 'A senha atual está incorreta (padrão inicial: 123456).';
            $painel_aberto = 'senha';
        } elseif (strlen($nova) < 6) {
            $erro_senha = 'A nova senha deve ter pelo menos 6 caracteres.';
            $painel_aberto = 'senha';
        } elseif ($nova !== $confirma) {
            $erro_senha = 'A confirmação não coincide com a nova senha.';
            $painel_aberto = 'senha';
        } else {
            $_SESSION['senha_usuario'] = $nova;
            header("Location: " . $_SERVER['PHP_SELF'] . "?salvo=senha");
            exit;
        }
    } 
    // Se salvou as preferências gerais
    else {
        $_SESSION['configuracoes']['texto_maior'] = isset($_POST['texto_maior']);
        $_SESSION['configuracoes']['alto_contraste'] = isset($_POST['alto_contraste']);
        $_SESSION['configuracoes']['reduzir_animacoes'] = isset($_POST['reduzir_animacoes']);

        $_SESSION['configuracoes']['notif_agendamento'] = isset($_POST['notif_agendamento']);
        $_SESSION['configuracoes']['notif_email'] = isset($_POST['notif_email']);
        $_SESSION['configuracoes']['notif_pedidos'] = isset($_POST['notif_pedidos']);
        $_SESSION['configuracoes']['notif_novidades'] = isset($_POST['notif_novidades']);

        $_SESSION['configuracoes']['seg_biometria'] = isset($_POST['seg_biometria']);
        $_SESSION['configuracoes']['seg_perfil_visivel'] = isset($_POST['seg_perfil_visivel']);

        $_SESSION['configuracoes']['idioma'] = $_POST['idioma'] ?? 'pt-BR';
        $_SESSION['configuracoes']['fuso'] = $_POST['fuso'] ?? 'America/Sao_Paulo';

        header("Location: " . $_SERVER['PHP_SELF'] . "?salvo=1");
        exit;
    }
}

if (isset($_GET['salvo'])) {
    $mensagem_salvo = $_GET['salvo'] === 'senha' ? 'Senha alterada com sucesso!' : 'Preferências salvas com sucesso!';
}

// Calcula iniciais e classes do tema
$nome_cartao = $_SESSION['dados_pessoais']['nome'];
$email_cartao = $_SESSION['dados_pessoais']['email'];
$partes_nome = array_values(array_filter(explode(' ', $nome_cartao)));
$iniciais = count($partes_nome) >= 2
    ? strtoupper(substr($partes_nome[0], 0, 1) . substr(end($partes_nome), 0, 1))
    : (count($partes_nome) === 1 ? strtoupper(substr($partes_nome[0], 0, 2)) : 'BS');

$cfg = $_SESSION['configuracoes'];
$classes_body = [];
if ($cfg['tema'] === 'escuro') $classes_body[] = 'tema-escuro';
if ($cfg['texto_maior']) $classes_body[] = 'texto-maior';
if ($cfg['alto_contraste']) $classes_body[] = 'alto-contraste';
if ($cfg['reduzir_animacoes']) $classes_body[] = 'reduzir-animacoes';
$attr_body_class = implode(' ', $classes_body);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="configuracao.css">
    <title>Beauty Schedule - Configurações</title>
</head>
<body class="<?= htmlspecialchars($attr_body_class) ?>">

<?php if ($mostrar_modal_excluir): ?>
    <div class="modal-overlay ativo">
        <div class="modal-box">
            <div class="modal-topo">
                <h3 class="modal-titulo">Excluir conta</h3>
            </div>
            <p class="modal-descricao">
                Tem certeza que deseja excluir permanentemente sua conta e todos os seus pontos do Beauty Club? Esta ação não poderá ser desfeita.
            </p>
            <div class="modal-acoes">
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn-modal-manter">Cancelar</a>
                <a href="?confirmar_excluir_conta=1" class="btn-modal-confirmar-delete">Excluir</a>
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
            <a href="cartoes.php"><span>Meus cartões</span> <span>&gt;</span></a>
            <a href="configuracao.php" class="ativo"><span>Configurações</span> <span>&gt;</span></a>
            <a href="historico.php"><span>Histórico de pontos</span> <span>&gt;</span></a>
        </section>

        <section class="club-card">
            <h4 class="club-title">Beauty club</h4>
            <div class="club-points-box">
                <span class="club-num"><?= $_SESSION['pontos'] ?></span>
                <span class="club-label">Pontos</span>
            </div>
            <p class="club-desc">Voce pode trocar seus pontos por até R$40 de desconto</p>
            <a href="historico.php" class="club-link">Resgatar beneficio &rarr;</a>
        </section>
    </div>

    <section class="dados-section">
        <p class="subtitulo">Preferencias</p> 
        <h2>Configurações</h2> 

        <?php if ($mensagem_salvo !== ''): ?>
            <p class="msg-sucesso"><?= htmlspecialchars($mensagem_salvo) ?></p>
        <?php endif; ?>

        <!-- BLOCO 1: Aparência e acessibilidade -->
        <h5 class="categoria-titulo">Aparencia e acessibilidade</h5>
        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="grupo-cards">
                <div class="item-config">
                    <div class="item-texto">
                        <h4>Tema do aplicativo</h4>
                        <p>Escolha como nosso site vai aparecer para voce</p>
                    </div>
                    <div class="grupo-tema">
                        <a href="?set_tema=claro" class="btn-tema <?= $cfg['tema'] === 'claro' ? 'ativo' : '' ?>">Claro</a>
                        <a href="?set_tema=escuro" class="btn-tema <?= $cfg['tema'] === 'escuro' ? 'ativo' : '' ?>">Escuro</a>
                        <a href="?set_tema=automatico" class="btn-tema <?= $cfg['tema'] === 'automatico' ? 'ativo' : '' ?>">Automatico</a>
                    </div>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Texto maior</h4>
                        <p>Aumenta o tamanho dos textos para facilitar a leitura</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="texto_maior" <?= $cfg['texto_maior'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Alto contraste</h4>
                        <p>Reforça a diferença entre textos e fundos</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="alto_contraste" <?= $cfg['alto_contraste'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Reduzir animações</h4>
                        <p>Diminui movimentos e transições na interface</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="reduzir_animacoes" <?= $cfg['reduzir_animacoes'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- BLOCO 2: Notificações -->
            <h5 class="categoria-titulo">Notificações</h5>
            <div class="grupo-cards">
                <div class="item-config">
                    <div class="item-texto">
                        <h4>Lembretes de agendamento</h4>
                        <p>Receber avisos 24 horas e 2 horas antes</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_agendamento" <?= $cfg['notif_agendamento'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Avisos pelo E-mail</h4>
                        <p>Confirmações, alterações e lembretes no seu e-mail</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_email" <?= $cfg['notif_email'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Atualizações de pedidos</h4>
                        <p>Acompanhar preparação, envio e entrega das compras</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_pedidos" <?= $cfg['notif_pedidos'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Novidades e beneficios</h4>
                        <p>Receber ofertas exclusivas do Beauty Club</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_novidades" <?= $cfg['notif_novidades'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

            <!-- BLOCO 3: Privacidade e segurança -->
            <h5 class="categoria-titulo">Privacidade e segurança</h5>
            <div class="grupo-cards">
                <a href="<?= $painel_aberto === 'senha' ? htmlspecialchars($_SERVER['PHP_SELF']) : '?abrir=senha#painel-senha' ?>" class="item-config item-link">
                    <div class="item-texto">
                        <h4>Alterar senha</h4>
                        <p>Atualize sua senha de acesso a conta</p>
                    </div>
                    <span class="seta-acao">&gt;</span>
                </a>

                <?php if ($painel_aberto === 'senha'): ?>
                    <div id="painel-senha" class="subpainel-config">
                        <?php if ($erro_senha !== ''): ?>
                            <p class="erro-subpainel"><?= htmlspecialchars($erro_senha) ?></p>
                        <?php endif; ?>
                        <div class="linha-subpainel">
                            <input type="password" name="senha_atual" placeholder="Senha atual (123456)">
                            <input type="password" name="nova_senha" placeholder="Nova senha (mín. 6 dígitos)">
                            <input type="password" name="confirma_senha" placeholder="Confirmar nova senha">
                            <button type="submit" name="salvar_nova_senha" class="btn-subpainel">Atualizar senha</button>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Acesso por biometria</h4>
                        <p>Use a sua digital ou reconhecimento facial para entrar</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="seg_biometria" <?= $cfg['seg_biometria'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="item-config">
                    <div class="item-texto">
                        <h4>Perfil visivel para profissionais</h4>
                        <p>Permite acesso às suas preferencias durante o atendimento</p>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="seg_perfil_visivel" <?= $cfg['seg_perfil_visivel'] ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <a href="<?= $painel_aberto === 'dispositivos' ? htmlspecialchars($_SERVER['PHP_SELF']) : '?abrir=dispositivos#painel-disp' ?>" class="item-config item-link">
                    <div class="item-texto">
                        <h4>Dispositivos conectados</h4>
                        <p><?= count($_SESSION['dispositivos']) ?> dispositivo(s) com acesso a sua conta</p>
                    </div>
                    <span class="seta-acao">&gt;</span>
                </a>

                <?php if ($painel_aberto === 'dispositivos'): ?>
                    <div id="painel-disp" class="subpainel-config">
                        <?php foreach ($_SESSION['dispositivos'] as $idx => $disp): ?>
                            <div class="item-dispositivo">
                                <div>
                                    <strong><?= htmlspecialchars($disp['nome']) ?></strong>
                                    <span> &mdash; <?= htmlspecialchars($disp['local']) ?></span>
                                </div>
                                <?php if (!$disp['atual']): ?>
                                    <a href="?desconectar_disp=<?= $idx ?>" class="btn-desconectar">Desconectar</a>
                                <?php else: ?>
                                    <span class="tag-atual">Ativo agora</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- BLOCO 4: Idioma e região -->
            <h5 class="categoria-titulo">Idioma e região</h5>
            <div class="card-idioma">
                <div class="campo-idioma">
                    <label for="idioma">Idioma</label>
                    <select id="idioma" name="idioma">
                        <option value="pt-BR" <?= $cfg['idioma'] === 'pt-BR' ? 'selected' : '' ?>>Português (Brasil)</option>
                        <option value="en-US" <?= $cfg['idioma'] === 'en-US' ? 'selected' : '' ?>>English (US)</option>
                        <option value="es-ES" <?= $cfg['idioma'] === 'es-ES' ? 'selected' : '' ?>>Español</option>
                    </select>
                </div>

                <div class="campo-idioma">
                    <label for="fuso">Fuso horário</label>
                    <select id="fuso" name="fuso">
                        <option value="America/Sao_Paulo" <?= $cfg['fuso'] === 'America/Sao_Paulo' ? 'selected' : '' ?>>Brasilia (GMT-3)</option>
                        <option value="America/Manaus" <?= $cfg['fuso'] === 'America/Manaus' ? 'selected' : '' ?>>Manaus (GMT-4)</option>
                        <option value="Europe/Lisbon" <?= $cfg['fuso'] === 'Europe/Lisbon' ? 'selected' : '' ?>>Lisboa (GMT+0)</option>
                    </select>
                </div>
            </div>

            <!-- BLOCO 5: Dados e conta -->
            <h5 class="categoria-titulo">Dados e conta</h5>
            <div class="grupo-cards">
                <a href="?baixar_dados=1" class="item-config item-link">
                    <div class="item-texto">
                        <h4>Baixar meus dados</h4>
                        <p>Receba uma cópia das informações da sua conta</p>
                    </div>
                    <span class="seta-acao">&rarr;</span>
                </a>

                <a href="?excluir_conta=1" class="item-config item-link item-perigo">
                    <div class="item-texto">
                        <h4>Excluir minha conta</h4>
                        <p>Remover permanentemente seus dados e beneficios</p>
                    </div>
                    <span class="seta-acao">&gt;</span>
                </a>
            </div>

            <button type="submit" class="btn-salvar">Salvar preferências</button>
        </form>
    </section>  
</main>
    
</body>
</html>