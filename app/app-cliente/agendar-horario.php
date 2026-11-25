<?php
ob_start();
session_start();
require_once __DIR__ . "/../init.php";

if (!isset($_SESSION['agendamento_atual']) || empty($_SESSION['agendamento_atual']['data'])) {
    header('Location: agendar-procedimento.php');
    exit;
}

$agendamento = $_SESSION['agendamento_atual'];

$meses = [
    '01' => 'janeiro', '02' => 'fevereiro', '03' => 'março',
    '04' => 'abril',   '05' => 'maio',      '06' => 'junho',
    '07' => 'julho',   '08' => 'agosto',    '09' => 'setembro',
    '10' => 'outubro', '11' => 'novembro',  '12' => 'dezembro'
];

$partesData = explode('-', $agendamento['data']);
$dia = isset($partesData[2]) ? (int)$partesData[2] : '';
$mesNome = isset($partesData[1], $meses[$partesData[1]]) ? $meses[$partesData[1]] : '';
$dataFormatada = "{$dia} de {$mesNome}, as {$agendamento['horario']}";

$valorFormatado = number_format((float)$agendamento['valor'], 0, ',', '.');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $id_cliente = (int)($_SESSION['id_cliente'] ?? $_SESSION['usuario']['id'] ?? $_SESSION['usuario']['id_cliente'] ?? 1);
    
  
    $id_funcionario = (int)($agendamento['id_funcionario'] ?? $agendamento['id_profissional'] ?? 0);
    if ($id_funcionario <= 0 && !empty($agendamento['nome_profissional'])) {
        $stmtFunc = $pdo->prepare("SELECT id FROM funcionarios WHERE nome = :nome LIMIT 1");
        $stmtFunc->execute([':nome' => $agendamento['nome_profissional']]);
        $id_funcionario = (int)($stmtFunc->fetchColumn() ?: 1);
    }
    if ($id_funcionario <= 0) {
        $id_funcionario = 1;
    }

   if (isset($_POST['confirmar'])) {
    $id_cliente = $_SESSION['id_cliente'] ?? $_SESSION['usuario']['id_cliente'] ?? 1;
    $id_funcionario = $agendamento['id_funcionario'] ?? $agendamento['id_profissional'] ?? 1;

    try {
        $resultado = create($pdo, 'agendamentos', [
            'id_cliente'      => $id_cliente,
            'id_funcionario'  => $id_funcionario,

            'id_procedimento' => (int)$agendamento['id_procedimento'],
            'duracao_minutos' => (int)$agendamento['duracao_minutos'],
        

            'data'            => $agendamento['data'],
            'horario'         => $agendamento['horario'],
            'status'          => 'Confirmado'
        ]);


        $novoId = (is_numeric($resultado) && (int)$resultado > 0)
            ? (int)$resultado
            : (int)$pdo->lastInsertId();

        if ($novoId > 0) {
            $_SESSION['ultimo_agendamento_id'] = $novoId;
        }

        $_SESSION['agendamento_concluido'] = [
            'id_agendamento' => $novoId,
            'data'           => $agendamento['data'],
            'horario'        => $agendamento['horario']
];

        $_SESSION['agendamento_concluido'] = [
            'data'    => $agendamento['data'],
            'horario' => $agendamento['horario']

        ];

        unset($_SESSION['agendamento_atual']);

        if (!headers_sent()) {
            header('Location: agendar4.php');
            exit;
        } else {
            echo "<script>window.location.href = 'agendar4.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $erro = "Este horário já está reservado para esta profissional. Volte e escolha outro horário ou data.";
        } else {
            $erro = "Erro ao confirmar agendamento: " . $e->getMessage();
        }
        header('Location: agendar4.php');
        exit;
    } catch (Exception $e) {
        $erro = "Erro ao confirmar agendamento: " . $e->getMessage();
    }
}
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Agendamento - BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/agendar-horario.css">
</head>
<body>

    <?php require_once __DIR__ . '/../nav.php'; ?>

    <main>
        <div class="container">
            
            <div class="card-header">
                <div class="progresso-wrapper">
                    <div class="progresso-barras">
                        <span class="barra ativa"></span>
                        <span class="barra ativa"></span>
                        <span class="barra ativa"></span>
                    </div>
                    <span class="subtitulo">Agendamento</span>
                </div>

                <a href="index.php" class="btn-fechar" title="Fechar">x</a>
            </div>

            <h2>Revise seu agendamento</h2>

            <?php if (!empty($erro)): ?>
                <div style="background-color: #f8d7da; color: #721c24; border-left: 4px solid #721c24; padding: 12px 14px; border-radius: 4px; margin-bottom: 18px; font-size: 14px;">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/app/app-cliente/agendar-reservado.php">
                
                <div class="resumo-box">
                    
                    <div class="resumo-item">
                        <div class="resumo-icone">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l1.9 5.8L20 10.7l-5.8 1.9L12 18.5l-1.9-5.9L4 10.7l6.1-1.9L12 3z"/>
                                <path d="M5 3v4M3 5h4M19 17v4M17 19h4"/>
                            </svg>
                        </div>
                        <div class="resumo-texto">
                            <span class="resumo-label">Procedimento</span>
                            <span class="resumo-valor"><?= htmlspecialchars($agendamento['nome_procedimento']) ?></span>
                        </div>
                    </div>

                    <hr class="resumo-divisor">

                    <div class="resumo-item">
                        <div class="resumo-icone">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M5 20c0-3.8 3.1-7 7-7s7 3.2 7 7"/>
                            </svg>
                        </div>
                        <div class="resumo-texto">
                            <span class="resumo-label">Profissional</span>
                            <span class="resumo-valor"><?= htmlspecialchars($agendamento['nome_profissional']) ?></span>
                        </div>
                    </div>

                    <hr class="resumo-divisor">

                    <div class="resumo-item">
                        <div class="resumo-icone">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1E1E1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="17" rx="3"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                            </svg>
                        </div>
                        <div class="resumo-texto">
                            <span class="resumo-label">Data e Horario</span>
                            <span class="resumo-valor"><?= htmlspecialchars($dataFormatada) ?></span>
                        </div>
                    </div>

                    <hr class="resumo-divisor">

                    <div class="resumo-total">
                        <span class="total-label">Total:</span>
                        <span class="total-valor">R$<?= htmlspecialchars($valorFormatado) ?></span>
                    </div>

                </div>

                <div class="acoes-rodape">
                    <a href="/app/app-cliente/agendar-profissional.php" class="btn-voltar">Voltar</a>
                    <button type="submit" name="confirmar" class="btn-continuar">
                        Continuar 
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>