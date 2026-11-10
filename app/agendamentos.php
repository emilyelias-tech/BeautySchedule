<?php

require_once 'init.php';


$clientes = readAll($pdo, 'clientes');


$funcionarios = readAll($pdo, 'funcionarios', 'ativo = TRUE');

$procedimentos = readAll($pdo, 'procedimentos', 'ativo = TRUE');


if (isset($_POST['cadastrar'])) {

    $cliente = $_POST['cliente'];
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $funcionario = $_POST['funcionario'];
    $procedimento = $_POST['procedimento'];

    $dados = read(
        $pdo,
        'procedimentos',
        'id_procedimento = ?',
        [$procedimento]
    );

    create($pdo, 'agendamentos', [
        'id_cliente' => $cliente,
        'data' => $data,
        'horario' => $horario,
        'id_funcionario' => $funcionario,
        'id_procedimento' => $procedimento,
        'duracao_minutos' => $dados['duracao_minutos']
    ]);

    header('Location: agendamentos.php');
    exit;
}

// Listar agendamentos
$sql = "SELECT
            a.id_agendamento,
            c.nome AS cliente,
            a.data,
            a.horario,
            f.nome AS funcionario,
            p.nome AS procedimento
        FROM agendamentos a
        INNER JOIN clientes c ON a.id_cliente = c.id
        INNER JOIN funcionarios f ON a.id_funcionario = f.id
        INNER JOIN procedimentos p ON a.id_procedimento = p.id_procedimento
        ORDER BY a.data, a.horario";

$consulta = $pdo->query($sql);
$agendamentos = $consulta->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Agendamentos - BeautySchedule</title>
    <link rel="stylesheet" href="agendamentos.css">
</head>

<body>

    <div class="container">

        <h1>BeautySchedule</h1>

        <h2>Agendar Atendimento</h2>

        <form method="POST">

            <label>Nome da pessoa:</label>

            <select name="cliente" required>
                <option value="">Selecione o cliente</option>

                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>">
                        <?= htmlspecialchars($cliente['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Dia:</label>
            <input type="date" name="data" required>

            <label>Horário:</label>
            <input type="time" name="horario" required>

            <label>Funcionário desejado:</label>

            <select name="funcionario" required>
                <option value="">Selecione o funcionário</option>

                <?php foreach ($funcionarios as $funcionario): ?>
                    <option value="<?= $funcionario['id'] ?>">
                        <?= htmlspecialchars($funcionario['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tipo de atendimento:</label>

            <select name="procedimento" required>
                <option value="">Selecione o atendimento</option>

                <?php foreach ($procedimentos as $procedimento): ?>
                    <option value="<?= $procedimento['id_procedimento'] ?>">
                        <?= htmlspecialchars($procedimento['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="cadastrar">
                Fazer Agendamento
            </button>

        </form>

        <h2>Agendamentos Realizados</h2>

        <table>

            <tr>
                <th>Cliente</th>
                <th>Dia</th>
                <th>Horário</th>
                <th>Funcionário</th>
                <th>Atendimento</th>
            </tr>

            <?php foreach ($agendamentos as $agendamento): ?>

                <tr>
                    <td><?= htmlspecialchars($agendamento['cliente']) ?></td>

                    <td><?= date('d/m/Y', strtotime($agendamento['data'])) ?></td>

                    <td><?= substr($agendamento['horario'], 0, 5) ?></td>

                    <td><?= htmlspecialchars($agendamento['funcionario']) ?></td>

                    <td><?= htmlspecialchars($agendamento['procedimento']) ?></td>
                </tr>

            <?php endforeach; ?>

        </table>

    </div>

</body>
</html>
