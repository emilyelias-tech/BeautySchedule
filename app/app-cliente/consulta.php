<?php
require_once __DIR__ . "/../init.php";

$cliente_id = (int)($_SESSION['cliente_id'] ?? $_SESSION['id_cliente'] ?? $_SESSION['usuario_id'] ?? 1);

$proximoLembrete = null;
$agendamentos = [];

$mesesCurto = [
    '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr',
    '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
    '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez'
];

if (isset($pdo)) {
    try {
        $stmtLembrete = $pdo->prepare("
            SELECT a.*, p.nome AS procedimento, f.nome AS profissional
            FROM agendamentos a
            INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
            INNER JOIN funcionarios f ON a.id_funcionario = f.id
            WHERE a.id_cliente = ? AND a.status != 'Cancelado' AND CONCAT(a.data, ' ', a.horario) >= NOW()
            ORDER BY a.data ASC, a.horario ASC
            LIMIT 1
        ");
        $stmtLembrete->execute([$cliente_id]);
        $proximoLembrete = $stmtLembrete->fetch(PDO::FETCH_ASSOC);

        $stmtList = $pdo->prepare("
            SELECT a.*, p.nome AS procedimento, f.nome AS profissional
            FROM agendamentos a
            INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
            INNER JOIN funcionarios f ON a.id_funcionario = f.id
            WHERE a.id_cliente = ?
            ORDER BY a.data DESC, a.horario DESC
        ");
        $stmtList->execute([$cliente_id]);
        $agendamentosDB = $stmtList->fetchAll(PDO::FETCH_ASSOC);

        foreach ($agendamentosDB as $row) {
            $partes = explode('-', $row['data']);
            $agendamentos[] = [
                'id_agendamento' => $row['id_agendamento'],
                'dia'          => $partes[2] ?? '01',
                'mes'          => $mesesCurto[$partes[1] ?? '01'] ?? 'Jan',
                'status'       => $row['status'] ?? 'Confirmado',
                'procedimento' => $row['procedimento'] ?? 'Procedimento',
                'hora'         => substr($row['horario'], 0, 5) . ' hrs',
                'profissional' => $row['profissional'] ?? 'Especialista'
            ];
        }
    } catch (PDOException $e) {
    }
}

$posProcedimento = [
    'titulo' => ($proximoLembrete && isset($proximoLembrete['procedimento'])) ? $proximoLembrete['procedimento'] . ' • Cuidados' : 'Procedimento • Cuidados',
    'dicas' => [
        ['numero' => '01', 'texto' => 'Use protetor solar diariamente e evite exposição.'],
        ['numero' => '02', 'texto' => 'Evite usar maquiagem e ácidos nas próximas 24 horas.'],
        ['numero' => '03', 'texto' => 'Não esprema a pele e mantenha o rosto bem hidratado.']
    ]
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seus Agendamentos - Beauty Schedule</title>
  <link rel="stylesheet" href="../css/consulta.css">
</head>
<body>

  <?php require_once __DIR__ . "/../nav-cli.php"; ?>

  <main class="container">
    
    <section class="page-header">
      <h1>Seus Agendamentos</h1>
      <p>Acompanhe seus próximos cuidados, lembretes e orientações pós-atendimento</p>
    </section>

    <?php if ($proximoLembrete): ?>
    <section class="reminder-card">
      <div class="reminder-left">
        <div class="bell-circle">
          <svg width="62" height="35" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
          </svg>
        </div>
        <div class="informacao">
          <span class="small-label">Lembrete do seu próximo cuidado</span>
          <h2>Procedimento: <?= strtolower($proximoLembrete['procedimento']) ?> - <?= !is_numeric($proximoLembrete['data_hora']) ? date('d/m/Y H:i', strtotime($proximoLembrete['data_hora'])) : $proximoLembrete['data_hora'] ?></h2>
          <p>Chegue com 10 minutos de antecedência. Nós avisaremos você novamente 24 horas antes.</p>
        </div>
      </div>
      <button class="botao">Adicionar a sua agenda</button>
    </section>
    <?php endif; ?>

    <section class="schedule-section">
      <span class="eyebrow">Próximos horários</span>
      <h2 class="section-title">Sua agenda</h2>

      <div class="appointments-list">
        <?php foreach ($agendamentos as $item): ?>
          <article class="appointment-card">
            <div class="card-left">
              <div class="date-badge">
                <span class="day"><?= $item['dia'] ?></span>
                <span class="month"><?= $item['mes'] ?></span>
              </div>
              <div class="appointment-details">
                <span class="status-pill"><?= $item['status'] ?></span>
                <h3><?= $item['procedimento'] ?></h3>
                <p><?= $item['hora'] ?> com <?= $item['profissional'] ?></p>
              </div>
            </div>
            <a href="informacao.php?id=<?= (int)$item['id_agendamento'] ?>" class="botao">Detalhes</a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="aftercare-section">
      <span class="eyebrow">Depois do seu procedimento</span>
      <h2 class="section-title">Cuide dos seus resultados</h2>

      <div class="aftercare-card">
        <div class="aftercare-header">
          <div class="sparkle-circle">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
              <path d="M12 3l1.8 4.2L18 9l-4.2 1.8L12 15l-1.8-4.2L6 9l4.2-1.8L12 3z"/>
              <path d="M18 15l.9 2.1L21 18l-2.1.9L18 21l-.9-2.1L15 18l2.1-.9L18 15z"/>
            </svg>
          </div>
          <div>
            <span class="small-label"><?= $posProcedimento['titulo'] ?></span>
            <h3 class="h3">Seu cuidado continua em casa</h3>
          </div>
        </div>

        <div class="tips-grid">
          <?php foreach ($posProcedimento['dicas'] as $dica): ?>
            <div class="tip-item">
              <span class="tip-number"><?= $dica['numero'] ?></span>
              <p><?= $dica['texto'] ?></p>
            </div>
          <?php endforeach; ?>
        </div>

        <a href="feedback.php" class="botao">Avaliar atendimento</a>
      </div>
    </section>

  </main>

</body>
</html>