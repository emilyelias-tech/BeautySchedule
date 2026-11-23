<?php

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dados.css">
    <link rel="stylesheet" href="css/nav.css">
    <title>Beauty Schedule - Meus Endereços</title>
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="container-principal">
        <!-- COLUNA DA ESQUERDA (BARRA LATERAL) -->
        <aside class="coluna-esquerda">
            
            <!-- Card do Perfil -->
            <section class="perfil-card">
                <div class="avatar">EE</div>
                <h4>Emily Elias</h4>
                <p class="email">emilyelias@gmail.com</p>
                <hr>
                <div class="estatisticas">
                    <div class="stat-item">
                        <strong>12</strong>
                        <span>Cuidados Realizados</span>
                    </div>
                    <div class="divisor-vertical"></div>
                    <div class="stat-item">
                        <strong>4.9</strong>
                        <span>Sua Avaliação média</span>
                    </div>
                </div>
                <hr>
            </section>

            <!-- Menu de Navegação Lateral -->
            <nav class="nav-perfil">
                <a href="configuracao-dados-pessoais.php">Dados pessoais <span>&gt;</span></a>
                <a href="#" class="ativo">Endereços <span>&gt;</span></a>
                <a href="cartoes.php">Meus cartões <span>&gt;</span></a>
                <a href="configuracao.php">Configurações <span>&gt;</span></a>
                <a href="historico.php">Histórico de pontos <span>&gt;</span></a>
            </nav>

            <!-- Card Beauty Club -->
            <section class="beauty-club-card">
                <p class="club-titulo">Beauty club</p>
                <div class="pontos-info">
                    <span class="pontos-numero">680</span>
                    <span class="pontos-texto">Ponto</span>
                </div>
                <p class="club-desc">Você pode trocar seus pontos por até R$40 de desconto</p>
                <a href="#" class="resgatar-link">Resgatar beneficio &rarr;</a>
            </section>

        </aside>

        <!-- COLUNA DA DIREITA (CONTEÚDO PRINCIPAL) -->
        <section class="coluna-direita">
            <div class="cabecalho-endereco">
                <div>
                    <p class="subtitulo">Dados de entrega</p>
                    <h2>Meus endereços</h2>
                </div>
                <button type="button" class="btn-adicionar">+ Adicionar endereço</button>
            </div>

            <!-- Card Endereço Principal -->
            <div class="card-endereco-principal">
                <span class="badge-principal">Principal</span>
                <h3>Casa</h3>
                <p>Almeida santos, 1240, apto 82</p>
                <p>Jardins - Sao paulo - SP</p>
                <p>Cep 01418-100</p>
                <a href="#" class="editar-link">Editar endereço</a>
            </div>

            <!-- Formulário de Endereço -->
            <form class="campo-formulario">
                <div class="linha-dupla">
                    <div class="grupo-input">
                        <label for="cep">Cep</label>
                        <input type="text" id="cep" name="cep">
                    </div>
                    <div class="grupo-input">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value=""></option>
                            <option value="SP">São Paulo</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="MG">Minas Gerais</option>
                            <!-- Adicione os outros estados aqui -->
                        </select>
                    </div>
                </div>

                <div class="grupo-input">
                    <label for="rua">Rua</label>
                    <input type="text" id="rua" name="rua">
                </div>

                <div class="linha-dupla">
                    <div class="grupo-input">
                        <label for="numero">Numero</label>
                        <input type="text" id="numero" name="numero">
                    </div>
                    <div class="grupo-input">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento">
                    </div>
                </div>

                <div class="linha-dupla">
                    <div class="grupo-input">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro">
                    </div>
                    <div class="grupo-input">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade">
                    </div>
                </div>

                <button type="submit" class="btn-salvar">Salvar endereço</button>
            </form>
        </section>
    </main>
</body>
</html>