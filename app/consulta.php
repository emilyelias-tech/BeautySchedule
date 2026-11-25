<?php
// Dados dos agendamentos (depois você pode conectar com o banco de dados)
$proximoLembrete = [
    'procedimento' => 'Limpeza de pele - ',
    'data_hora' => '18 de Dezembro, as 11:00 '
];

$agendamentos = [
    [
        'dia' => '18',
        'mes' => 'Dez',
        'status' => 'Confirmado',
        'procedimento' => 'Limpeza de pele',
        'hora' => '11:00 hrs',
        'profissional' => 'Mariana Lopes'
    ],
   
];

$posProcedimento = [
    'titulo' => 'Limpeza de pele • 18 Dez',
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="consulta.css">
</head>
<body>

  <!-- Barra de Navegação -->
  <header class="navbar">
    <div class="nav-container">
      <a href="inicio.php" class="logo">
        <span class="logo-monogram">BS</span>
        <span class="logo-text">Beauty<br>Schedule</span>
      </a>
          </svg>
        </button>
        </button>
      </div>
    </div>
  </header>

  <!-- Conteúdo Principal -->
  <main class="container">
    
    <!-- Cabeçalho da Página -->
    <section class="page-header">
      <h1>Seus Agendamentos</h1>
      <p>Acompanhe seus próximos cuidados, lembretes e orientações pós-atendimento</p>
    </section>

    <!-- Banner de Lembrete -->
    <section class="reminder-card">
      <div class="reminder-left">
        <div class="bell-circle">
          <svg width="62" height="35" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22a2 2 0 0 0 2-2h-4a2 2 0 0 0 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4a1.5 1.5 0 0 0-3 0v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
          </svg>
        </div>
        <div class="reminder-info">
          <span class="small-label">Lembrete do seu próximo cuidado</span>
-           <h2>Sua <?= strtolower($proximoLembrete['procedimento']) ?>
<?= $proximoLembrete['data_hora'] ?></h2>
          <p>Chegue com 10 minutos de antecedência. Nós avisaremos você novamente 24 horas antes.</p>
        </div>
      </div>
      <button class="btn-pill">Adicionar a sua agenda</button>
    </section>

    <!-- Seção: Sua agenda -->
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
-                 <p><?= $item['hora'] ?> com 
<?= $item['profissional'] ?></p>
              </div>
            </div>
            <button class="btn-pill">Detalhes</button>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Seção: Pós-procedimento -->
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
            <h3 class ="h3">Seu cuidado continua em casa</h3>
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

        <button class="btn-pill">Avaliar atendimento</button>
      </div>
    </section>

  </main>

</body>
</html>