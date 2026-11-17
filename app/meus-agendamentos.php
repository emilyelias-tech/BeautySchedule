<?php
session_start();
require_once __DIR__ . "/init.php";

$id_cliente = $_SESSION['id_cliente'] ?? $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_cliente'] ?? null;
$mensagem = '';
$erro = '';

// AÇÃO: DELETAR AGENDAMENTO DO BANCO
if (isset($_POST['deletar_agendamento'])) {
    $id_deletar = (int)($_POST['id_agendamento'] ?? 0);

    if ($id_deletar > 0) {
        try {
            $pdo->beginTransaction();

            // Remove feedback vinculado (se existir) para não violar FOREIGN KEY
            $stmtFb = $pdo->prepare("DELETE FROM feedback WHERE id_agendamento = :id");
            $stmtFb->execute([':id' => $id_deletar]);

            // Deleta o agendamento
            $stmtDel = $pdo->prepare("DELETE FROM agendamentos WHERE id_agendamento = :id");
            $stmtDel->execute([':id' => $id_deletar]);

            $pdo->commit();
            $mensagem = "Agendamento excluído com sucesso!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $erro = "Erro ao excluir agendamento: " . $e->getMessage();
        }
    }
}

$filtro = $_GET['filtro'] ?? 'todos';

// Busca os dados verdadeiros do banco
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
        f.nome AS nome_profissional,
        fb.nota AS nota_avaliacao
    FROM agendamentos a
    INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
    INNER JOIN funcionarios f ON a.id_funcionario = f.id
    INNER JOIN clientes c ON a.id_cliente = c.id
    LEFT JOIN feedback fb ON a.id_agendamento = fb.id_agendamento
    WHERE 1=1
";

$params = [];

// Se houver cliente logado na sessão, filtra pelos agendamentos dele; caso contrário, mostra todos do banco
if (!empty($id_cliente)) {
    $sql .= " AND a.id_cliente = :id_cliente";
    $params[':id_cliente'] = $id_cliente;
}

if ($filtro === 'ativos') {
    $sql .= " AND a.status IN ('Confirmado', 'Pendente')";
} elseif ($filtro === 'concluidos') {
    $sql .= " AND a.status = 'Concluído'";
} elseif ($filtro === 'cancelados') {
    $sql .= " AND a.status = 'Cancelado'";
}

$sql .= " ORDER BY a.data DESC, a.horario DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
    '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro'
];

function formatarDataCompleta($dataIso, $meses) {
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
    <title>Meus Agendamentos - BeautySchedule</title>
    <link rel="stylesheet" href="css/consulta.css">
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

    <main>
        <div class="container container-todos">

            <div class="card-header">
                <div class="titulo-grupo">
                    <span class="subtitulo">Gerenciamento de agenda</span>
                    <h2>Agendamentos realizados</h2>
                </div>

                <a href="agendar1.php" class="btn-salvar btn-novo-agendamento">+ Novo agendamento</a>
            </div>

            <!-- Filtros -->
            <div class="filtros-lista">
                <a href="meus_agendamentos.php?filtro=todos" class="filtro-tab <?= $filtro === 'todos' ? 'ativo' : '' ?>">
                    Todos (<?= count($agendamentos) ?>)
                </a>
                <a href="meus_agendamentos.php?filtro=ativos" class="filtro-tab <?= $filtro === 'ativos' ? 'ativo' : '' ?>">
                    Ativos
                </a>
                <a href="meus_agendamentos.php?filtro=concluidos" class="filtro-tab <?= $filtro === 'concluidos' ? 'ativo' : '' ?>">
                    Concluídos
                </a>
                <a href="meus_agendamentos.php?filtro=cancelados" class="filtro-tab <?= $filtro === 'cancelados' ? 'ativo' : '' ?>">
                    Cancelados
                </a>
            </div>

            <?php if (!empty($mensagem)): ?>
                <p class="mensagem-sucesso"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <?php if (isset($_GET['editado'])): ?>
                <p class="mensagem-sucesso">Agendamento atualizado com sucesso!</p>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <?php if (empty($agendamentos)): ?>
                <div class="lista-vazia">
                    <p>Nenhum agendamento encontrado no banco de dados.</p>
                    <a href="agendar1.php" class="btn-salvar">Agendar procedimento</a>
                </div>
            <?php else: ?>
                <div class="lista-agendamentos">
                    <?php foreach ($agendamentos as $item): 
                        $statusSlug = strtolower(str_replace(
                            ['í', 'ú', 'ç', 'ã', 'é', ' '],
                            ['i', 'u', 'c', 'a', 'e', '-'],
                            $item['status']
                        ));
                        $valorFormatado = number_format((float)$item['valor'], 2, ',', '.');
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

                            <!-- Rodapé com botões de Consulta, Editar e Deletar -->
                            <div class="item-acoes">
                                <div class="acoes-esquerda">
                                    <a href="consulta.php?id=<?= (int)$item['id_agendamento'] ?>" class="link-secundario">
                                        Detalhes
                                    </a>
                                    <?php if ($item['status'] !== 'Cancelado'): ?>
                                        <a href="consulta2.php?id=<?= (int)$item['id_agendamento'] ?>" class="link-secundario">
                                            Avaliar
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="acoes-direita">
                                    <a href="editar_agendamento.php?id=<?= (int)$item['id_agendamento'] ?>" class="btn-acao-editar">
                                        Editar
                                    </a>

                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja DELETAR permanentemente este agendamento?');">
                                        <input type="hidden" name="id_agendamento" value="<?= (int)$item['id_agendamento'] ?>">
                                        <button type="submit" name="deletar_agendamento" class="btn-acao-deletar">
                                            Deletar
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>