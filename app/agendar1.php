<?php

session_start();

require_once __DIR__ . "/init.php";

$procedimentos = readAll($pdo, 'procedimentos');



$erro = '';



if (isset($_POST['cadastrar'])) {

    $procedimento_id = $_POST['procedimento'] ?? '';



    if (empty($procedimento_id)) {

        $erro = "Por favor, selecione um cuidado antes de continuar.";

    } else {

        $dados = read(

            $pdo,

            'procedimentos',

            'id_procedimento = ?',

            [$procedimento_id]

        );



        if (!$dados) {

            $erro = "Procedimento não encontrado.";

        } else {

       

            $_SESSION['agendamento_atual'] = [

                'id_procedimento' => $dados['id_procedimento'],

                'nome_procedimento' => $dados['nome'],

                'duracao_minutos' => $dados['duracao_minutos'],

                'valor' => $dados['valor'] ?? $dados['preco'] ?? 0

            ];



            header('Location: agendar2.php');

            exit;

        }

    }

}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agendamentos - BeautySchedule</title>

    <link rel="stylesheet" href="css/agendamentos.css">

</head>

<body>



    <?php require_once __DIR__ . '/nav.php'; ?>



    <main>

        <div class="container">

         

            <div class="card-header">

                <div class="progresso-wrapper">

                    <div class="progresso-barras">

                        <span class="barra ativa"></span>

                        <span class="barra"></span>

                        <span class="barra"></span>

                    </div>

                    <span class="subtitulo">Agendamento</span>

                </div>



                <a href="index.php" class="btn-fechar" title="Fechar">x</a>

            </div>



            <h2>Escolha seu cuidado</h2>



            <?php if (!empty($erro)): ?>

                <p class="mensagem-erro"><?= htmlspecialchars($erro) ?></p>

            <?php endif; ?>



            <form method="POST">

                <div class="lista-cuidados">

                    <?php foreach ($procedimentos as $index => $proc):

                       

                        $imagem = !empty($proc['imagem']) ? $proc['imagem'] : 'img/procedimento-padrao.jpg';

                        $duracao = $proc['duracao_minutos'] ?? '50';

                        $preco = isset($proc['valor']) ? number_format($proc['valor'], 0, ',', '.') : (isset($proc['preco']) ? number_format($proc['preco'], 0, ',', '.') : '150');

                    ?>

                        <label class="card-cuidado">

                            <input

                                type="radio"

                                name="procedimento"

                                value="<?= htmlspecialchars($proc['id_procedimento']) ?>"

                                <?= $index === 0 ? 'checked' : '' ?>

                                required

                            >

                           

                            <div class="cuidado-conteudo">

                                <img src="<?= htmlspecialchars($imagem) ?>" alt="<?= htmlspecialchars($proc['nome']) ?>" class="cuidado-img">

                               

                                <div class="cuidado-info">

                                    <span class="cuidado-titulo"><?= htmlspecialchars($proc['nome']) ?></span>

                                    <span class="cuidado-detalhes"><?= htmlspecialchars($duracao) ?> min &nbsp;·&nbsp; R$ <?= htmlspecialchars($preco) ?></span>

                                </div>



                                <div class="cuidado-check">

                                    <span class="check-icon">✓</span>

                                </div>

                            </div>

                        </label>

                    <?php endforeach; ?>

                </div>
   <button type="submit" name="cadastrar" class="btn-continuar">Continuar  </button>
         </form>
     </div>
  </main>
</body>

</html>


