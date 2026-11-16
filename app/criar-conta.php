<?php

require_once __DIR__ . "/init.php";

    try{
        $consulta = $pdo->prepare('
            SELECT nome, email, senha FROM clientes ORDER BY nome, email, senha DESC
        ');
        $consulta->execute();
        $clientes = $consulta->fetchAll();
    }catch(PDOException $erro){
        die("Erro ao cadastrar: " . $erro->getMessage());
    }

    ?>

    <html>
        <head></head>
        <body>
            <table border="1" cellpadding="8">
                <thead>
                    <tr>
                        <th>NOME</th>
                        <th>E-MAIL</th>
                        <th>SENHA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($clientes as $a): ?>
                        <tr>
                            <td> <?=htmlspecialchars($a['nome']) ?></td>
                            <td> <?= htmlspecialchars($a['email']) ?></td>
                            <td> <?= htmlspecialchars($a['senha']) ?></td>
                        </tr>
                        <?php endforeach ?>
                </tbody>
            </table>
            <br><br>
        </body>
    </html>
