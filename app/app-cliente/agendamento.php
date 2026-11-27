<?php
require_once __DIR__ . "/../init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id   = $_SESSION['cliente_id'] ?? 1;
    $procedimento = $_POST['procedimento'] ?? '';
    $data_hora    = $_POST['data_hora'] ?? '';
    $profissional = $_POST['profissional'] ?? '';

    if (isset($pdo)) {
        $stmt = $pdo->prepare("INSERT INTO agendamentos (cliente_id, procedimento, data_hora, profissional, status) VALUES (?, ?, ?, ?, 'Confirmado')");
        $stmt->execute([$cliente_id, $procedimento, $data_hora, $profissional]);
    }

    header("Location: /app/consulta.php");
    exit;
}
?>