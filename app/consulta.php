<?php
ob_start();
session_start();
require_once __DIR__ . "/init.php";

$id_cliente = $_SESSION['id_cliente'] ?? $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_cliente'] ?? 1;

$id_agendamento = $_GET['id'] ?? $_SESSION['ultimo_agendamento_id'] ?? $_SESSION['agendamento_concluido']['id_agendamento'] ?? null;

$mensagem = '';
$erro = '';
$agendamento = null;

if ($id_agendamento) {
    $stmt = $pdo->prepare("
        SELECT a.*, p.nome AS nome_procedimento, p.valor, f.nome AS nome_profissional
        FROM agendamentos a
        INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
        INNER JOIN funcionarios f ON a.id_funcionario = f.id
        WHERE a.id_agendamento = :id
    ");
    $stmt->execute([':id' => $id_agendamento]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$agendamento) {
    $stmt = $pdo->prepare("
        SELECT a.*, p.nome AS nome_procedimento, p.valor, f.nome AS nome_profissional
        FROM agendamentos a
        INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
        INNER JOIN funcionarios f ON a.id_funcionario = f.id
        WHERE a.id_cliente = :id_cliente
        ORDER BY a.id_agendamento DESC
        LIMIT 1
    ");
    $stmt->execute([':id_cliente' => $id_cliente]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['salvar_observacao']) && $agendamento) {
    $observacao = trim($_POST['observacao'] ?? '');
    $stmt = $pdo->prepare("UPDATE agendamentos SET observacao = :obs WHERE id_agendamento = :id");
    $stmt->execute([
        ':obs' => $observacao,
        ':id'  => $agendamento['id_agendamento']
    ]);

    header('Location: meus-agendamentos.php?id=' . (int)$agendamento['id_agendamento']);
    exit;
}

if (isset($_POST['cancelar_agendamento']) && $agendamento) {
    $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'Cancelado' WHERE id_agendamento = :id");
    $stmt->execute([':id' => $agendamento['id_agendamento']]);

    unset($_SESSION['ultimo_agendamento_id'], $_SESSION['agendamento_concluido']);

    header('Location: index.php?cancelado=1');
    exit;
}

$meses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
    '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro'
];

if ($agendamento) {
    $partesData = explode('-', $agendamento['data']);
    $dia = (int)$partesData[2];
    $mesNome = $meses[$partesData[1]];
    $ano = $partesData[0];
    $dataFormatada = "{$dia} de {$mesNome} de {$ano}";
    $horarioFormatado = substr($agendamento['horario'], 0, 5);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Agendamento - BeautySchedule</title>
    <link rel="stylesheet" href="css/consulta.css">
</head>
<body>

    <?php require_once __DIR__ . '/nav.php'; ?>

    <main>
        <div class="container">

            <?php if (!$agendamento): ?>
                <div class="card-header">
                    <div class="titulo-grupo">
                        <span class="subtitulo">Consulta</span>
                        <h2>Nenhum agendamento encontrado</h2>
                    </div>
                    <a href="index.php" class="btn-fechar" title="Fechar">x</a>
                </div>
                <p class="texto-vazio-consulta">Você ainda não possui agendamentos registrados.</p>
                <a href="agendar1.php" class="btn-salvar btn-link-agendar">Fazer agendamento</a>
            <?php else: ?>

                <div class="card-header">
                    <div class="titulo-grupo">
                        <span class="subtitulo">Detalhes do agendamento</span>
                        <h2><?= htmlspecialchars($agendamento['nome_procedimento']) ?></h2>
                    </div>

                    <a href="index.php" class="btn-fechar" title="Fechar">x</a>
                </div>

                <div class="status-wrapper status-wrapper-acoes">
                    <span class="badge-status"><?= htmlspecialchars($agendamento['status']) ?></span>
                    <a href="feedback.php?id=<?= (int)$agendamento['id_agendamento'] ?>" class="link-avaliar-topo">
                        Avaliar atendimento
                    </a>
                </div>

                <?php if (!empty($mensagem)): ?>
                    <p class="mensagem-sucesso"><?= htmlspecialchars($mensagem) ?></p>
                <?php endif; ?>

                <div class="grid-detalhes">
                    
                    <div class="detalhe-celula">
                        <div class="detalhe-icone">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3E452E" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                            </svg>
                        </div>
                        <div class="detalhe-info">
                            <span class="detalhe-label">Data</span>
                            <span class="detalhe-valor"><?= htmlspecialchars($dataFormatada) ?></span>
                        </div>
                    </div>

                    <div class="detalhe-celula">
                        <div class="detalhe-icone">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9"/>
                                <polyline points="12 7 12 12 15 14"/>
                            </svg>
                        </div>
                        <div class="detalhe-info">
                            <span class="detalhe-label">Horário</span>
                            <span class="detalhe-valor"><?= htmlspecialchars($horarioFormatado) ?></span>
                        </div>
                    </div>

                    <div class="detalhe-celula">
                        <div class="detalhe-icone">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M5 20c0-3.8 3.1-7 7-7s7 3.2 7 7"/>
                            </svg>
                        </div>
                        <div class="detalhe-info">
                            <span class="detalhe-label">Profissional</span>
                            <span class="detalhe-valor"><?= htmlspecialchars($agendamento['nome_profissional']) ?></span>
                        </div>
                    </div>

                    <div class="detalhe-celula">
                        <div class="detalhe-icone">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l1.9 5.8L20 10.7l-5.8 1.9L12 18.5l-1.9-5.9L4 10.7l6.1-1.9L12 3z"/>
                                <path d="M19 16v4M17 18h4"/>
                            </svg>
                        </div>
                        <div class="detalhe-info">
                            <span class="detalhe-label">Duração</span>
                            <span class="detalhe-valor"><?= htmlspecialchars($agendamento['duracao_minutos']) ?> Minutos</span>
                        </div>
                    </div>

                </div>

                <form method="POST" class="form-consulta">
                    <div class="campo-observacao">
                        <label for="observacao" class="label-observacao">Observação para a profissional</label>
                        <textarea 
                            name="observacao" 
                            id="observacao" 
                            class="textarea-observacao" 
                            placeholder="Ex: Tenho sensibilidade na pele, alergias ou alguma preferência para o atendimento"
                        ><?= htmlspecialchars($agendamento['observacao'] ?? '') ?></textarea>
                    </div>

                    <div class="acoes-rodape">
                        <button 
                            type="button" 
                            class="btn-cancelar"
                            onclick="abrirModalCancelar()"
                        >
                            Cancelar agendamento
                        </button>

                        <button type="submit" name="salvar_observacao" class="btn-salvar">
                            Salvar observação
                        </button>
                    </div>
                </form>

                <div id="modal-cancelar" class="modal-overlay" onclick="fecharModalFora(event)">
                    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modal-cancelar-titulo">
                        <div class="modal-topo">
                            <span class="subtitulo">Confirmação</span>
                            <button type="button" class="btn-fechar" onclick="fecharModalCancelar()" title="Fechar">x</button>
                        </div>

                        <h3 id="modal-cancelar-titulo" class="modal-titulo">Cancelar agendamento?</h3>
                        <p class="modal-descricao">
                            Tem certeza que deseja cancelar a reserva de 
                            <strong><?= htmlspecialchars($agendamento['nome_procedimento']) ?></strong> 
                            no dia <strong><?= htmlspecialchars($dataFormatada) ?></strong> às <strong><?= htmlspecialchars($horarioFormatado) ?></strong>?
                        </p>

                        <form method="POST" class="modal-acoes">
                            <button type="button" class="btn-modal-manter" onclick="fecharModalCancelar()">
                                Manter reserva
                            </button>

                            <button type="submit" name="cancelar_agendamento" class="btn-modal-confirmar-delete">
                                Sim, cancelar
                            </button>
                        </form>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <script>
        function abrirModalCancelar() {
            const modal = document.getElementById('modal-cancelar');
            if (modal) modal.classList.add('ativo');
        }

        function fecharModalCancelar() {
            const modal = document.getElementById('modal-cancelar');
            if (modal) modal.classList.remove('ativo');
        }

        function fecharModalFora(event) {
            if (event.target.id === 'modal-cancelar') {
                fecharModalCancelar();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModalCancelar();
            }
        });
    </script>

</body>
</html>