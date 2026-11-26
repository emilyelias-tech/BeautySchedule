<?php
declare(strict_types=1);
require_once __DIR__ . '/../init.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário | BeautySchedule</title>
    <link rel="stylesheet" href="/app/css/formulario.css">
</head>
<body>

    <?php require_once __DIR__ . "/../nav.php"; ?>
    
    <div class="fundo">
        <div class="container-login">
            <div class="lado-imagem">
                <img src="/app/imagens/Logo Beauty Schedule Verde e Bege.png" alt="Logo Beauty Schedule">
            </div>

            <div class="lado-formulario">
                <div class="texto">
                    <h1>Formulário de Contato</h1>
                </div>

                <form action="formulario-enviado.php" method="POST" class="form-login">

                    <div class="campo">
                        <label for="nome">Nome completo</label>
                        <input type="text" name="nome" id="nome" placeholder="Seu nome aqui" required>
                    </div>

                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" placeholder="seu@email.com" required>
                    </div>

                    <div class="campo">
                        <label for="categoria">Sobre o que deseja falar?</label>
                        <select name="categoria" id="categoria">
                            <option value="agendamento">Agendamento</option>
                            <option value="duvidas">Dúvidas sobre procedimentos</option>
                            <option value="valores">Valores e pagamentos</option>
                            <option value="cancelamento">Cancelamento ou alteração de horário</option>
                            <option value="informacoes">Informações sobre profissionais</option>
                            <option value="problemas">Problemas com minha conta</option>
                            <option value="elogios">Elogios ou sugestões</option>
                            <option value="reclamações">Reclamações</option>
                            <option value="outros">Outros assuntos</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" rows="3" placeholder="Escreva sua mensagem" required></textarea>
                    </div>
            
                    <button type="submit" class="btn-entrar">Enviar formulário →</button>
                </form>

            </div>
        </div>
    </div>

</body>
</html>