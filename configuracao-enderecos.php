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

if (!isset($_SESSION['enderecos'])) {
    $_SESSION['enderecos'] = [
        [
            'titulo' => 'Casa',
            'cep' => '01418-100',
            'estado' => 'SP',
            'rua' => 'Almeida santos',
            'numero' => '1240',
            'complemento' => 'apto 82',
            'bairro' => 'jardins',
            'cidade' => 'Sao paulo',
            'principal' => true
        ]
    ];
}

if (isset($_GET['confirmar_apagar'])) {
    $indice = (int)$_GET['confirmar_apagar'];
    if (isset($_SESSION['enderecos'][$indice])) {
        unset($_SESSION['enderecos'][$indice]);
        $_SESSION['enderecos'] = array_values($_SESSION['enderecos']);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$indice_confirmar_exclusao = isset($_GET['apagar']) ? (int)$_GET['apagar'] : null;

$indice_edicao = null;
$erro_cep = '';
$dados_edicao = [
    'cep' => '',
    'estado' => '',
    'rua' => '',
    'numero' => '',
    'complemento' => '',
    'bairro' => '',
    'cidade' => ''
];

if (isset($_GET['editar'])) {
    $idx = (int)$_GET['editar'];
    if (isset($_SESSION['enderecos'][$idx])) {
        $indice_edicao = $idx;
        $dados_edicao = $_SESSION['enderecos'][$idx];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indice_salvar = $_POST['indice_editar'] ?? '';
    if ($indice_salvar !== '') {
        $indice_edicao = (int)$indice_salvar;
    }

    if (isset($_POST['buscar_cep'])) {
        $cep_digitado = trim($_POST['cep'] ?? '');
        $cep_limpo = preg_replace('/[^0-9]/', '', $cep_digitado);

        $dados_edicao = [
            'cep' => $cep_digitado,
            'estado' => trim($_POST['estado'] ?? ''),
            'rua' => trim($_POST['rua'] ?? ''),
            'numero' => trim($_POST['numero'] ?? ''),
            'complemento' => trim($_POST['complemento'] ?? ''),
            'bairro' => trim($_POST['bairro'] ?? ''),
            'cidade' => trim($_POST['cidade'] ?? '')
        ];

        if (strlen($cep_limpo) === 8) {
            $resposta = false;
            $erro_detalhe = '';

            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://viacep.com.br/ws/{$cep_limpo}/json/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $resposta = curl_exec($ch);
                $erro_detalhe = curl_error($ch);
                curl_close($ch);
            } else {
                $erro_detalhe = 'Extensão cURL está desativada no PHP.';
            }

            if ($resposta === false or empty($resposta)) {
                $contexto = stream_context_create([
                    'http' => ['timeout' => 10, 'header' => "User-Agent: Mozilla/5.0\r\n"],
                    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
                ]);
                $resposta = @file_get_contents("https://viacep.com.br/ws/{$cep_limpo}/json/", false, $contexto);
            }

            if ($resposta !== false and !empty($resposta)) {
                $dados_api = json_decode($resposta, true);
                if (is_array($dados_api) and !isset($dados_api['erro'])) {
                    $dados_edicao['cep'] = $dados_api['cep'] ?? $cep_digitado;
                    $dados_edicao['estado'] = $dados_api['uf'] ?? '';
                    $dados_edicao['rua'] = $dados_api['logradouro'] ?? '';
                    $dados_edicao['bairro'] = $dados_api['bairro'] ?? '';
                    $dados_edicao['cidade'] = $dados_api['localidade'] ?? '';
                } else {
                    $erro_cep = 'CEP não encontrado na base do ViaCEP.';
                }
            } else {
                $erro_cep = 'Não foi possível consultar o CEP (' . ($erro_detalhe ?: 'sem resposta') . ')';
            }
        } else {
            $erro_cep = 'Digite um CEP válido com 8 números.';
        }
    } else {
        if ($indice_salvar !== '' and isset($_SESSION['enderecos'][(int)$indice_salvar])) {
            $i = (int)$indice_salvar;
            $_SESSION['enderecos'][$i]['cep'] = trim($_POST['cep'] ?? '');
            $_SESSION['enderecos'][$i]['estado'] = trim($_POST['estado'] ?? '');
            $_SESSION['enderecos'][$i]['rua'] = trim($_POST['rua'] ?? '');
            $_SESSION['enderecos'][$i]['numero'] = trim($_POST['numero'] ?? '');
            $_SESSION['enderecos'][$i]['complemento'] = trim($_POST['complemento'] ?? '');
            $_SESSION['enderecos'][$i]['bairro'] = trim($_POST['bairro'] ?? '');
            $_SESSION['enderecos'][$i]['cidade'] = trim($_POST['cidade'] ?? '');
        } else {
            $novo_endereco = [
                'titulo' => 'Novo endereço',
                'cep' => trim($_POST['cep'] ?? ''),
                'estado' => trim($_POST['estado'] ?? ''),
                'rua' => trim($_POST['rua'] ?? ''),
                'numero' => trim($_POST['numero'] ?? ''),
                'complemento' => trim($_POST['complemento'] ?? ''),
                'bairro' => trim($_POST['bairro'] ?? ''),
                'cidade' => trim($_POST['cidade'] ?? ''),
                'principal' => false
            ];
            $_SESSION['enderecos'][] = $novo_endereco;
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$estados_brasil = [
    'AC' => 'Acre (AC)',
    'AL' => 'Alagoas (AL)',
    'AP' => 'Amapá (AP)',
    'AM' => 'Amazonas (AM)',
    'BA' => 'Bahia (BA)',
    'CE' => 'Ceará (CE)',
    'DF' => 'Distrito Federal (DF)',
    'ES' => 'Espírito Santo (ES)',
    'GO' => 'Goiás (GO)',
    'MA' => 'Maranhão (MA)',
    'MT' => 'Mato Grosso (MT)',
    'MS' => 'Mato Grosso do Sul (MS)',
    'MG' => 'Minas Gerais (MG)',
    'PA' => 'Pará (PA)',
    'PB' => 'Paraíba (PB)',
    'PR' => 'Paraná (PR)',
    'PE' => 'Pernambuco (PE)',
    'PI' => 'Piauí (PI)',
    'RJ' => 'Rio de Janeiro (RJ)',
    'RN' => 'Rio Grande do Norte (RN)',
    'RS' => 'Rio Grande do Sul (RS)',
    'RO' => 'Rondônia (RO)',
    'RR' => 'Roraima (RR)',
    'SC' => 'Santa Catarina (SC)',
    'SP' => 'São Paulo (SP)',
    'SE' => 'Sergipe (SE)',
    'TO' => 'Tocantins (TO)'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="configuracao-enderecos.css">
    <title>Beauty Schedule - Meus Endereços</title>
</head>
<body>

<?php if ($indice_confirmar_exclusao !== null and isset($_SESSION['enderecos'][$indice_confirmar_exclusao])): ?>
    <div class="modal-overlay ativo">
        <div class="modal-box">
            <div class="modal-topo">
                <h3 class="modal-titulo">Excluir endereço</h3>
            </div>
            <p class="modal-descricao">
                Tem certeza que deseja excluir o endereço <strong><?= htmlspecialchars($_SESSION['enderecos'][$indice_confirmar_exclusao]['titulo']) ?></strong>? Esta ação não poderá ser desfeita.
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
            <a href="configuracao-enderecos.php" class="ativo"><span>Endereços</span> <span>&gt;</span></a>
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
        <div class="topo-enderecos">
            <div>
                <p class="subtitulo">Dados de entrega</p> 
                <h2>Meus endereços</h2> 
            </div>
            <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#form-endereco" class="btn-add-endereco">+ Adicionar endereço</a>
        </div>

        <div class="lista-enderecos-cards">
            <?php foreach ($_SESSION['enderecos'] as $index => $end): ?>
                <div class="endereco-salvo-card">
                    <?php if ($end['principal']): ?>
                        <span class="badge-principal">Principal</span>
                    <?php else: ?>
                        <span class="badge-secundario">Salvo</span>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($end['titulo']) ?></h3>
                    <p class="endereco-detalhes">
                        <?= htmlspecialchars($end['rua']) ?>, <?= htmlspecialchars($end['numero']) ?>
                        <?= !empty($end['complemento']) ? ', ' . htmlspecialchars($end['complemento']) : '' ?><br>
                        <?= htmlspecialchars($end['bairro']) ?> - <?= htmlspecialchars($end['cidade']) ?> - <?= htmlspecialchars($end['estado']) ?><br>
                        Cep <?= htmlspecialchars($end['cep']) ?>
                    </p>

                    <div class="acoes-cartao">
                        <a href="?editar=<?= $index ?>#form-endereco" class="editar-link">Editar endereço</a>
                        <a href="?apagar=<?= $index ?>" class="apagar-link">Apagar endereço</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <form class="form-enderecos" id="form-endereco" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>#form-endereco">
            <input type="hidden" name="indice_editar" value="<?= $indice_edicao !== null ? $indice_edicao : '' ?>">

            <div class="linha-dupla">
                <div class="campo-grupo">
                    <label for="cep">Cep</label>
                    <div class="cep-input-box">
                        <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($dados_edicao['cep']) ?>" placeholder="00000-000" required>
                        <button type="submit" name="buscar_cep" formnovalidate class="btn-buscar-cep">Buscar</button>
                    </div>
                    <?php if ($erro_cep !== ''): ?>
                        <span class="erro-cep"><?= htmlspecialchars($erro_cep) ?></span>
                    <?php endif; ?>
                </div>

                <div class="campo-grupo">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" required>
                        <option value="" disabled <?= $dados_edicao['estado'] === '' ? 'selected' : '' ?>></option>
                        <?php foreach ($estados_brasil as $sigla => $nome): ?>
                            <option value="<?= $sigla ?>" <?= $dados_edicao['estado'] === $sigla ? 'selected' : '' ?>>
                                <?= $nome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="campo-grupo">
                <label for="rua">Rua</label>
                <input type="text" id="rua" name="rua" value="<?= htmlspecialchars($dados_edicao['rua']) ?>" required>
            </div>

            <div class="linha-dupla">
                <div class="campo-grupo">
                    <label for="numero">Numero</label>
                    <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($dados_edicao['numero']) ?>" required>
                </div>

                <div class="campo-grupo">
                    <label for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" value="<?= htmlspecialchars($dados_edicao['complemento']) ?>">
                </div>
            </div>

            <div class="linha-dupla">
                <div class="campo-grupo">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" value="<?= htmlspecialchars($dados_edicao['bairro']) ?>" required>
                </div>

                <div class="campo-grupo">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" value="<?= htmlspecialchars($dados_edicao['cidade']) ?>" required>
                </div>
            </div>

            <button type="submit" name="salvar_endereco" class="btn-salvar">
                <?= $indice_edicao !== null ? 'Atualizar endereço' : 'Salvar endereço' ?>
            </button>
        </form>
    </section>  
</main>
    
</body>
</html>