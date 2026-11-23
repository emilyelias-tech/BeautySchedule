<?php
    declare(strict_types=1);

    require_once __DIR__ . '/../init.php';

    $idCliente=exigir_cliente();
    $cliente=carregar_perfil($pdo,$idCliente);
    $stmt=$pdo->prepare("SELECT * FROM historico_pontos WHERE id_cliente=? AND descricao <> 'Saldo inicial de demonstração do Beauty Club' ORDER BY criado_em DESC,id_ponto DESC");
    $stmt->execute([$idCliente]);
    $historico=$stmt->fetchAll();
    $pontos=pontos_cliente($pdo,$idCliente);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Histórico de pontos | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/wireframe.css">
</head>
<body><?php
require __DIR__ . '/../nav.php';?>
    <main class="pagina">
        <div class="layout-perfil">
            <?php mostrar_lateral_perfil($pdo,$cliente,'pontos');?>
            
            <section class="conteudo-perfil">
                <p class="sobretitulo">Beauty club</p>
                <h1>Histórico de pontos</h1>
                <p>Seu saldo atual: <strong><?= $pontos ?> pontos</strong>
                </p><?php
                if(!$historico):
                ?><p>Você ainda não possui movimentações de pontos.</p><?php
                else:
                foreach($historico as $mov):
                ?><div class="ponto-linha">
                <div>
                <strong><?= h($mov['descricao']) ?></strong>
                <small style="display:block;margin-top:6px"><?= h(date('d/m/Y',strtotime($mov['criado_em']))) ?></small>
                </div>
                <span class="<?= (int)$mov['pontos']>=0?'pontos-positivo':'pontos-negativo' ?>"><?= (int)$mov['pontos']>0?'+':'' ?><?= (int)$mov['pontos'] ?> pts</span>
                </div><?php
                endforeach;
                endif;
                ?><p style="margin-top:28px">
                <a class="botao" href="resgatar.php">Resgatar benefício →</a>
                </p>
            </section>
        </div>
    </main>
</body>
</html>
