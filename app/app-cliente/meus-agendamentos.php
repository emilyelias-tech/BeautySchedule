<?php
ob_start();
session_start();
require_once __DIR__ . "/init.php";

$id_cliente = $_SESSION['id_cliente'] ?? $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_cliente'] ?? null;
$mensagem = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deletar_agendamento'])) {
    $id_deletar = (int)($_POST['id_agendamento'] ?? 0);

    if ($id_deletar > 0) {
        try {
            $pdo->beginTransaction();

            try {
                $stmtFb = $pdo->prepare("DELETE FROM feedback WHERE id_agendamento = :id");
                $stmtFb->execute([':id' => $id_deletar]);
            } catch (PDOException $ignorar) {
            }

            $stmtDel = $pdo->prepare("DELETE FROM agendamentos WHERE id_agendamento = :id");
            $stmtDel->execute([':id' => $id_deletar]);

            $pdo->commit();
            $mensagem = "Agendamento cancelado com sucesso!";
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erro = "Erro ao cancelar agendamento: " . $e->getMessage();
        }
    } else {
        $erro = "Não foi possível identificar o agendamento para o cancelamento.";
    }
}

$filtro = strtolower(trim($_POST['filtro_atual'] ?? $_GET['filtro'] ?? 'todos'));
$filtrosValidos = ['todos', 'ativos', 'concluidos', 'cancelados'];
if (!in_array($filtro, $filtrosValidos, true)) {
    $filtro = 'todos';
}

$sql = "
    SELECT 
        a.id_agendamento,
        a.data,
        a.horario,
        a.duracao_minutos,
        a.status,
        a.observacao,
        c.nome AS nome_cliente,
        p.nome AS nome_procedimento,
        p.valor,
        f.nome AS nome_profissional
    FROM agendamentos a
    INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
    INNER JOIN funcionarios f ON a.id_funcionario = f.id
    INNER JOIN clientes c ON a.id_cliente = c.id
    WHERE 1=1
";

$params = [];

if (!empty($id_cliente)) {
    $sql .= " AND a.id_cliente = :id_cliente";
    $params[':id_cliente'] = $id_cliente;
}

$sql .= " ORDER BY a.id_agendamento DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$todosAgendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function normalizarStatus(string $status) {
    $s = mb_strtolower(trim((string)$status), 'UTF-8');
    $s = str_replace(['í', 'ú', 'ç', 'ã', 'é', 'ó', 'á'], ['i', 'u', 'c', 'a', 'e', 'o', 'a'], $s);
    return $s;
}

$contadores = [
    'todos'      => count($todosAgendamentos),
    'ativos'     => 0,
    'concluidos' => 0,
    'cancelados' => 0
];

$agendamentos = [];

foreach ($todosAgendamentos as $item) {
    $st = normalizarStatus($item['status']);

    $ehAtivo     = in_array($st, ['confirmado', 'pendente', 'agendado'], true);
    $ehConcluido = in_array($st, ['concluido', 'finalizado', 'realizado'], true);
    $ehCancelado = in_array($st, ['cancelado'], true);

    if ($ehAtivo)     $contadores['ativos']++;
    if ($ehConcluido) $contadores['concluidos']++;
    if ($ehCancelado) $contadores['cancelados']++;

    if ($filtro === 'todos') {
        $agendamentos[] = $item;
    } elseif ($filtro === 'ativos' && $ehAtivo) {
        $agendamentos[] = $item;
    } elseif ($filtro === 'concluidos' && $ehConcluido) {
        $agendamentos[] = $item;
    } elseif ($filtro === 'cancelados' && $ehCancelado) {
        $agendamentos[] = $item;
    }
}

$titulosPorFiltro = [
    'todos'      => 'Agendamentos realizados',
    'ativos'     => 'Agendamentos ativos',
    'concluidos' => 'Agendamentos concluídos',
    'cancelados' => 'Agendamentos cancelados'
];

$mensagensVazioPorFiltro = [
    'todos'      => 'Nenhum agendamento encontrado no banco de dados.',
    'ativos'     => 'Você não possui agendamentos ativos (Confirmados ou Pendentes) no momento.',
    'concluidos' => 'Nenhum agendamento foi marcado como Concluído ainda.',
    'cancelados' => 'Nenhum agendamento cancelado encontrado.'
];

$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
    '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro'
];

function formatarDataCompleta(string $dataIso, array $meses) {
    $partes = explode('-', $dataIso);
    if (count($partes) !== 3) return $dataIso;
    $dia = (int)$partes[2];
    $mes = $meses[$partes[1]] ?? $partes[1];
    return "{$dia} de {$mes} de {$partes[0]}";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulosPorFiltro[$filtro]) ?> - BeautySchedule</title>
    <link rel="stylesheet" href="css/consulta.css">
       
    
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

    <main>
        <div class="container container-todos">

            <div class="card-header">
                <div class="titulo-grupo">
                    <span class="subtitulo">Gerenciamento de agenda</span>
                    <h2><?= htmlspecialchars($titulosPorFiltro[$filtro]) ?></h2>
                </div>

                <a href="agendar1.php" class="btn-salvar btn-novo-agendamento">+ Novo agendamento</a>
            </div>

            <div class="filtros-lista">
                <a href="meus-agendamentos.php?filtro=todos" class="filtro-tab <?= $filtro === 'todos' ? 'ativo' : '' ?>">
                    Todos (<?= $contadores['todos'] ?>)
                </a>
                <a href="meus-agendamentos.php?filtro=ativos" class="filtro-tab <?= $filtro === 'ativos' ? 'ativo' : '' ?>">
                    Ativos (<?= $contadores['ativos'] ?>)
                </a>
                <a href="meus-agendamentos.php?filtro=concluidos" class="filtro-tab <?= $filtro === 'concluidos' ? 'ativo' : '' ?>">
                    Concluídos (<?= $contadores['concluidos'] ?>)
                </a>
                <a href="meus-agendamentos.php?filtro=cancelados" class="filtro-tab <?= $filtro === 'cancelados' ? 'ativo' : '' ?>">
                    Cancelados (<?= $contadores['cancelados'] ?>)
                </a>
            </div>

            <?php if (!empty($mensagem)): ?>
                <p class="mensagem-sucesso"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['editado'])): ?>
                <p class="mensagem-sucesso">Agendamento atualizado com sucesso!</p>
            <?php endif; ?>

            <?php if (isset($_GET['cancelado'])): ?>
                <p class="mensagem-sucesso">Agendamento cancelado com sucesso!</p>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <?php if (empty($agendamentos)): ?>
                <div class="lista-vazia">
                    <p><?= htmlspecialchars($mensagensVazioPorFiltro[$filtro]) ?></p>
                    <?php if ($filtro !== 'todos'): ?>
                        <a href="meus-agendamentos.php?filtro=todos" class="btn-cancelar" style="margin-right: 10px;">Ver todos os agendamentos</a>
                    <?php endif; ?>
                    <a href="agendar1.php" class="btn-salvar">+ Novo agendamento</a>
                </div>
            <?php else: ?>
                <div class="lista-agendamentos">
                    <?php foreach ($agendamentos as $item): 
                        $statusSlug = normalizarStatus($item['status']);
                        $valorFormatado = number_format((float)$item['valor'], 2, ',', '.');
                        $dataHoraModal = formatarDataCompleta($item['data'], $meses) . ' às ' . substr($item['horario'], 0, 5);
                    ?>
                        <div class="card-agendamento-item">
                            
                            <div class="item-topo">
                                <div>
                                    <h3><?= htmlspecialchars($item['nome_procedimento']) ?></h3>
                                    <span class="item-profissional">
                                        Profissional: <strong><?= htmlspecialchars($item['nome_profissional']) ?></strong> | 
                                        Cliente: <?= htmlspecialchars($item['nome_cliente']) ?>
                                    </span>
                                </div>

                                <div class="item-status-grupo">
                                    <span class="badge-status status-<?= htmlspecialchars($statusSlug) ?>">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                    <span class="item-valor">R$ <?= htmlspecialchars($valorFormatado) ?></span>
                                </div>
                            </div>

                            <div class="item-dados-grid">
                                <span class="item-meta">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E452E" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="17" rx="2"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                    </svg>
                                    <?= htmlspecialchars(formatarDataCompleta($item['data'], $meses)) ?>
                                </span>

                                <span class="item-meta">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3E452E" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9"/>
                                        <polyline points="12 7 12 12 15 14"/>
                                    </svg>
                                    <?= htmlspecialchars(substr($item['horario'], 0, 5)) ?> (<?= (int)$item['duracao_minutos'] ?> min)
                                </span>
                            </div>

                            <?php if (!empty($item['observacao'])): ?>
                                <div class="item-observacao-box">
                                    <strong>Observação:</strong> <?= htmlspecialchars($item['observacao']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="item-acoes">
                                <div class="acoes-esquerda">
                                    <a href="consulta.php?id=<?= (int)$item['id_agendamento'] ?>" class="link-secundario">
                                        Detalhes
                                    </a>
                                    <?php if ($statusSlug !== 'cancelado'): ?>
                                        <a href="feedback.php?id=<?= (int)$item['id_agendamento'] ?>" class="link-secundario">
                                            Avaliar
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="acoes-direita">
                                    <a href="editar-agendamentos.php?id=<?= (int)$item['id_agendamento'] ?>" class="btn-acao-editar">
                                        Editar
                                    </a>

                                    <button 
                                        type="button" 
                                        class="btn-acao-deletar"
                                        data-id="<?= (int)$item['id_agendamento'] ?>"
                                        data-procedimento="<?= htmlspecialchars($item['nome_procedimento'], ENT_QUOTES) ?>"
                                        data-datahorario="<?= htmlspecialchars($dataHoraModal, ENT_QUOTES) ?>"
                                        onclick="abrirModalDeletar(this)"
                                    >
                                        Deletar
                                    </button>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <div id="modal-deletar" class="modal-overlay" onclick="fecharModalFora(event)">
        <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
            <div class="modal-topo">
                <span class="subtitulo">Confirmação</span>
                <button type="button" class="btn-fechar" onclick="fecharModalDeletar()" title="Fechar">x</button>
            </div>

            <h3 id="modal-titulo" class="modal-titulo">Cancelar agendamento?</h3>
            <p class="modal-descricao">
                Você está prestes a cancelar permanentemente o agendamento de 
                <strong id="modal-nome-procedimento"></strong> (<span id="modal-data-horario"></span>). 
                Essa ação não poderá ser desfeita.
            </p>

            <form method="POST" action="meus-agendamentos.php" class="modal-acoes">
                <input type="hidden" name="id_agendamento" id="modal-id-agendamento" value="">
                <input type="hidden" name="filtro_atual" value="<?= htmlspecialchars($filtro) ?>">
                
                <button type="button" class="btn-modal-manter" onclick="fecharModalDeletar()">
                    Manter agendamento
                </button>

                <button type="submit" name="deletar_agendamento" value="1" class="btn-modal-confirmar-delete">
                    Sim, cancelar
                </button>
            </form>
        </div>
    </div>

    <script>
        function abrirModalDeletar(botao) {
            document.getElementById('modal-id-agendamento').value = botao.getAttribute('data-id');
            document.getElementById('modal-nome-procedimento').textContent = botao.getAttribute('data-procedimento');
            document.getElementById('modal-data-horario').textContent = botao.getAttribute('data-datahorario');
            document.getElementById('modal-deletar').classList.add('ativo');
        }

        function fecharModalDeletar() {
            document.getElementById('modal-deletar').classList.remove('ativo');
        }

        function fecharModalFora(event) {
            if (event.target.id === 'modal-deletar') {
                fecharModalDeletar();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModalDeletar();
            }
        });
    </script>

</body>
</html>