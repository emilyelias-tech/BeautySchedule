-- BeautySchedule (MySQL 8.x, utf8mb3)
-- Senha dos clientes de teste: 123456 | funcionários: func123

DROP DATABASE IF EXISTS BeautySchedule;
CREATE DATABASE BeautySchedule CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE BeautySchedule;

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14),
    data_nascimento DATE,
    pontos INT DEFAULT 0,
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE enderecos_clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    titulo VARCHAR(50) DEFAULT 'Casa',
    cep VARCHAR(9),
    estado VARCHAR(2),
    cidade VARCHAR(100),
    bairro VARCHAR(100),
    rua VARCHAR(100),
    numero VARCHAR(10),
    complemento VARCHAR(100),
    principal TINYINT DEFAULT 0,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
);

CREATE TABLE funcionarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cargo VARCHAR(50) DEFAULT 'Funcionário',
    rua VARCHAR(100),
    nro INT,
    cep VARCHAR(9),
    estado VARCHAR(2),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    materiais VARCHAR(100),
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT DEFAULT 0,
    imagem VARCHAR(255)
);

CREATE TABLE procedimentos (
    id_procedimento INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    descricao VARCHAR(300),
    duracao_minutos INT NOT NULL,
    imagem VARCHAR(255)
);

CREATE TABLE funcionario_procedimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    id_procedimento INT NOT NULL,
    UNIQUE (id_funcionario, id_procedimento),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id),
    FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

CREATE TABLE disponibilidade_funcionario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

CREATE TABLE bloqueios_agenda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    motivo VARCHAR(200),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

CREATE TABLE agendamentos (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    data DATE NOT NULL,
    horario TIME NOT NULL,
    duracao_minutos INT NOT NULL,
    id_funcionario INT NOT NULL,
    id_cliente INT NOT NULL,
    id_procedimento INT NOT NULL,
    status VARCHAR(30) DEFAULT 'Pendente',
    observacao TEXT,
    motivo_cancelamento VARCHAR(300),
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_funcionario, data, horario),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

CREATE TABLE feedback (
    id_feedback INT PRIMARY KEY AUTO_INCREMENT,
    id_agendamento INT NOT NULL UNIQUE,
    nota DECIMAL(2,1) NOT NULL,
    descricao VARCHAR(300),
    data_feedback DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_agendamento) REFERENCES agendamentos(id_agendamento) ON DELETE CASCADE
);

CREATE TABLE vendas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_funcionario INT,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

CREATE TABLE itens_venda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_venda INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venda) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);

-- ---------------------------------------------------------------------
-- Dados iniciais
-- ---------------------------------------------------------------------

INSERT INTO clientes (nome, telefone, email, senha, pontos) VALUES
('Ana Silva',      '11987654321', 'ana@gmail.com',      '$2y$10$bln1ecIXIsMonBwOXmc.A.AY9/87Pu75qYVhxWIWe648SozQ8VdZK', 120),
('Beatriz Santos', '11976543210', 'bia@gmail.com',      '$2y$10$bln1ecIXIsMonBwOXmc.A.AY9/87Pu75qYVhxWIWe648SozQ8VdZK',  80),
('Carla Oliveira', '11965432109', 'carla@gmail.com',    '$2y$10$bln1ecIXIsMonBwOXmc.A.AY9/87Pu75qYVhxWIWe648SozQ8VdZK',  40),
('Mariana Costa',  '11954321098', 'mariana@gmail.com',  '$2y$10$bln1ecIXIsMonBwOXmc.A.AY9/87Pu75qYVhxWIWe648SozQ8VdZK', 200),
('Juliana Souza',  '11943210987', 'juliana@gmail.com',  '$2y$10$bln1ecIXIsMonBwOXmc.A.AY9/87Pu75qYVhxWIWe648SozQ8VdZK',   0);

INSERT INTO enderecos_clientes (cliente_id, titulo, cep, estado, cidade, bairro, rua, numero, principal) VALUES
(1, 'Casa', '09010-100', 'SP', 'Santo André', 'Centro',   'Rua das Flores',    '120', 1),
(2, 'Casa', '09020-200', 'SP', 'Santo André', 'Jardim',   'Rua Brasil',        '250', 1),
(3, 'Casa', '09030-300', 'SP', 'Santo André', 'Vila',     'Rua Central',        '85', 1),
(4, 'Casa', '09040-400', 'SP', 'Santo André', 'Bairro',   'Rua das Palmeiras',  '45', 1),
(5, 'Casa', '09050-500', 'SP', 'Santo André', 'Centro',   'Rua São Paulo',     '300', 1);

INSERT INTO funcionarios (nome, telefone, email, senha, cargo, rua, nro, cep, estado) VALUES
('Fernanda Lima',  '11911112222', 'fernanda@gmail.com', '$2y$10$zTfPndLABAc3qYvqBKT10.TKyu8VYUdaeXgtXHM/W08pkGhvSeN9i', 'Cabeleireira', 'Rua A', 100, '09010-100', 'SP'),
('Camila Rocha',   '11922223333', 'camila@gmail.com',   '$2y$10$zTfPndLABAc3qYvqBKT10.TKyu8VYUdaeXgtXHM/W08pkGhvSeN9i', 'Manicure',     'Rua B', 200, '09020-200', 'SP'),
('Patricia Alves', '11933334444', 'patricia@gmail.com', '$2y$10$zTfPndLABAc3qYvqBKT10.TKyu8VYUdaeXgtXHM/W08pkGhvSeN9i', 'Esteticista',  'Rua C', 300, '09030-300', 'SP'),
('Rafael Mendes',  '11944445555', 'rafael@gmail.com',   '$2y$10$zTfPndLABAc3qYvqBKT10.TKyu8VYUdaeXgtXHM/W08pkGhvSeN9i', 'Gerente',      'Rua D', 400, '09040-400', 'SP');

-- Imagens: caminho relativo às páginas em app/app-cliente/ (NULL = sem foto)
INSERT INTO produtos (nome, materiais, preco, quantidade, descricao, imagem) VALUES

('Shampoo Profissional', 'Produtos capilares', 35.90, 20,
'O Shampoo Profissional é um produto desenvolvido para a higienização dos cabelos e do couro cabeludo, auxiliando na remoção de impurezas, resíduos e oleosidade acumulados ao longo do dia.

Sua utilização representa a primeira etapa de uma rotina de cuidados capilares, preparando os fios para receber produtos complementares, como condicionadores, máscaras de tratamento e óleos finalizadores.

É uma opção para quem busca manter os cabelos limpos e bem cuidados, tornando a rotina de higiene mais prática e organizada.

Principais características:
- Produto destinado à limpeza capilar.
- Auxilia na remoção de impurezas.
- Pode complementar diferentes rotinas de cuidados.
- Utilizado antes do condicionador e da máscara.
- Aplicação simples durante o banho.

Benefícios:
- Ajuda a manter os cabelos limpos.
- Auxilia na remoção de resíduos acumulados.
- Prepara os fios para tratamentos posteriores.
- Contribui para uma rotina capilar mais completa.
- Proporciona sensação de limpeza após a lavagem.

Modo de aplicação:
1. Molhe completamente os cabelos.
2. Aplique uma quantidade adequada de shampoo.
3. Massageie suavemente o couro cabeludo com as pontas dos dedos.
4. Distribua a espuma pelos fios.
5. Enxágue completamente.
6. Continue o tratamento com condicionador ou máscara capilar.', NULL),

('Condicionador', 'Produtos capilares', 39.90, 15,
'O Condicionador é um produto fundamental para complementar a rotina de cuidados capilares, sendo utilizado após a lavagem com shampoo. Sua principal função é auxiliar no desembaraço dos fios, proporcionando maciez e contribuindo para uma aparência mais alinhada e bem cuidada.

Durante a lavagem, os cabelos podem apresentar maior dificuldade para desembaraçar. O condicionador ajuda a melhorar a textura dos fios e torna o processo de finalização mais simples. É uma opção para quem deseja manter uma rotina de cuidados completa e preservar a aparência saudável dos cabelos.

Principais características:
- Produto destinado aos cuidados capilares.
- Utilização após o shampoo.
- Auxilia no desembaraço dos fios.
- Contribui para a maciez dos cabelos.
- Aplicação concentrada no comprimento e nas pontas.

Benefícios:
- Ajuda a deixar os fios mais macios.
- Facilita o desembaraço.
- Contribui para uma aparência mais alinhada.
- Complementa o processo de lavagem.
- Proporciona uma sensação agradável ao toque.

Modo de aplicação:
1. Lave os cabelos com shampoo.
2. Retire o excesso de água.
3. Aplique o condicionador no comprimento e nas pontas.
4. Espalhe suavemente pelos fios.
5. Deixe agir conforme as instruções da embalagem.
6. Enxágue completamente.', NULL),

('Esmalte Vermelho', 'Produtos para unhas', 12.50, 30,
'O Esmalte Vermelho é um clássico da beleza e uma excelente opção para quem gosta de unhas marcantes e elegantes. Sua tonalidade proporciona destaque às mãos e combina com diferentes estilos, podendo ser utilizada tanto no dia a dia quanto em ocasiões especiais.

Além de ser uma escolha tradicional, o esmalte vermelho permite diversas combinações com acessórios, roupas e outros elementos da produção. É um produto indispensável para quem gosta de variar as cores das unhas e manter os cuidados com a aparência.

Principais características:
- Tonalidade vermelha.
- Aplicação prática com pincel.
- Pode ser utilizado em diferentes formatos de unhas.
- Combina com diferentes estilos e ocasiões.
- Permite aplicação de uma ou mais camadas.

Benefícios:
- Proporciona destaque às unhas.
- Complementa diferentes produções.
- Permite criar um visual clássico e marcante.
- Pode ser combinado com decorações e outros esmaltes.
- Ideal para renovar a aparência das unhas.

Modo de aplicação:
1. Limpe e prepare as unhas.
2. Aplique uma camada de base.
3. Passe o esmalte vermelho cuidadosamente.
4. Aguarde a secagem entre as camadas.
5. Finalize com extra brilho, se desejar.', NULL),

('Creme Hidratante', 'Produtos para pele', 45.00, 10,
'O Creme Hidratante é um produto essencial para quem deseja manter uma rotina de cuidados com a pele, auxiliando na reposição e manutenção da hidratação. Sua utilização contribui para deixar a pele com uma sensação mais confortável, macia e com aparência bem cuidada.

A hidratação é uma etapa importante para diferentes tipos de pele, pois ajuda a preservar sua barreira natural e a reduzir a sensação de ressecamento. O creme pode ser incorporado aos cuidados diários, sendo utilizado após a limpeza da pele ou sempre que necessário, conforme as recomendações do fabricante.

Principais características:
- Textura cremosa.
- Desenvolvido para hidratação da pele.
- Aplicação simples e prática.
- Pode complementar a rotina de cuidados faciais ou corporais, conforme indicação.
- Adequado para uso conforme as orientações do fabricante.

Benefícios:
- Ajuda a manter a hidratação da pele.
- Contribui para uma sensação de maciez.
- Auxilia na redução da sensação de ressecamento.
- Deixa a pele com aparência mais cuidada.
- Complementa a rotina diária de skincare.

Modo de aplicação:
1. Realize a limpeza da região desejada.
2. Seque delicadamente a pele.
3. Aplique uma quantidade adequada do creme.
4. Espalhe com movimentos suaves.
5. Aguarde a absorção do produto.', NULL),

('Óleo Capilar', 'Produtos capilares', 29.90, 25,
'O Óleo Capilar é um produto desenvolvido para proporcionar um cuidado complementar aos cabelos, ajudando a melhorar a aparência dos fios e oferecendo um acabamento mais bonito e alinhado. É especialmente interessante para quem deseja cuidar do comprimento e das pontas, regiões que frequentemente apresentam maior aparência de ressecamento.

Sua aplicação é simples e pode ser incorporada tanto à finalização quanto à rotina diária de cuidados capilares. Utilizado na quantidade adequada, contribui para proporcionar brilho, maciez e uma aparência mais disciplinada aos fios.

Principais características:
- Produto destinado à finalização e aos cuidados capilares.
- Auxilia na aparência das pontas.
- Contribui para o brilho dos fios.
- Pode ser utilizado em cabelos úmidos ou secos, conforme a indicação.
- Aplicação prática e rápida.

Benefícios:
- Ajuda a reduzir o aspecto de ressecamento.
- Proporciona aparência mais brilhante.
- Contribui para fios visualmente mais alinhados.
- Ajuda a melhorar a aparência das pontas.
- Complementa a finalização dos cabelos.

Modo de aplicação:
1. Coloque algumas gotas do produto nas mãos.
2. Espalhe o óleo entre as palmas.
3. Aplique suavemente no comprimento e nas pontas.
4. Distribua de maneira uniforme.
5. Finalize os cabelos como desejar.', NULL),

('Gel de Limpeza Facial', 'Produtos para pele', 48.99, 18,
'O Gel de Limpeza Facial é um produto desenvolvido para realizar a higienização diária da pele, auxiliando na remoção de impurezas, resíduos de maquiagem e excesso de oleosidade acumulados ao longo do dia.

A limpeza facial é uma das etapas fundamentais de uma rotina de skincare, pois prepara a pele para receber outros produtos, como séruns, hidratantes e protetores solares. Sua textura em gel proporciona uma aplicação prática e permite espalhar o produto facilmente pelo rosto.

Principais características:
- Textura em gel.
- Desenvolvido para limpeza facial.
- Auxilia na remoção de impurezas.
- Pode ser utilizado na rotina da manhã e da noite.
- Prepara a pele para as próximas etapas do skincare.

Benefícios:
- Ajuda a remover resíduos acumulados na pele.
- Auxilia no controle do excesso de oleosidade superficial.
- Proporciona sensação de limpeza e frescor.
- Contribui para uma rotina facial mais completa.
- Facilita a preparação da pele para outros cosméticos.

Modo de aplicação:
1. Umedeça o rosto com água.
2. Coloque uma pequena quantidade do gel nas mãos.
3. Espalhe pelo rosto com movimentos circulares suaves.
4. Enxágue completamente.
5. Continue sua rotina com hidratante e protetor solar.', '../imagens/gel-limpeza-facial.png'),

('Protetor Solar FPS 50', 'Produtos para pele', 40.76, 25,
'O Protetor Solar FPS é um dos produtos mais importantes para uma rotina de cuidados com a pele. Desenvolvido para ajudar a proteger contra os efeitos da exposição solar, ele contribui para a prevenção dos danos causados pela radiação ultravioleta e auxilia na manutenção da saúde da pele.

Sua utilização diária é recomendada mesmo em dias nublados, pois a exposição aos raios solares também pode ocorrer nessas condições. Além disso, pode ser utilizado como última etapa da rotina de skincare antes da maquiagem.

Principais características:
- Produto destinado à proteção solar.
- Possui fator de proteção solar (FPS).
- Pode ser incorporado à rotina facial diária.
- Pode ser utilizado antes da maquiagem.
- Indicado para complementar os cuidados com a pele.

Benefícios:
- Ajuda a proteger contra os raios UV.
- Contribui para prevenir o envelhecimento precoce causado pelo sol.
- Auxilia na prevenção de manchas relacionadas à exposição solar.
- Protege a pele durante atividades ao ar livre.
- Complementa uma rotina de skincare preventiva.

Modo de aplicação:
1. Realize a limpeza e hidratação da pele.
2. Aplique uma quantidade adequada do protetor.
3. Espalhe uniformemente nas áreas expostas.
4. Aguarde conforme as orientações do fabricante antes da maquiagem.
5. Reaplique conforme indicado na embalagem, especialmente após contato com água ou transpiração.

Indicação: Para pessoas que desejam manter uma rotina diária de proteção e cuidado com a pele.', '../imagens/protetor-solar-fps50.png'),

('Sérum de Vitamina C', 'Produtos para pele', 69.29, 12,
'O Sérum de Vitamina C é um produto desenvolvido para complementar os cuidados faciais, sendo especialmente procurado por quem deseja uma pele com aparência iluminada, uniforme e bem cuidada. A vitamina C é um ingrediente bastante conhecido no universo da skincare por sua ação antioxidante, ajudando a proteger a pele contra os efeitos dos radicais livres.

Sua textura em sérum permite uma aplicação prática e facilita sua inclusão na rotina diária de cuidados. Quando utilizado corretamente, pode contribuir para uma aparência mais radiante e para a manutenção de uma rotina de cuidados completa.

Principais características:
- Contém vitamina C em sua composição.
- Textura em sérum.
- Desenvolvido para cuidados faciais.
- Pode ser incorporado à rotina de skincare.
- Aplicação prática com auxílio de conta-gotas, caso disponível.

Benefícios:
- Contribui para uma aparência mais iluminada.
- Possui propriedades antioxidantes.
- Auxilia na uniformização da aparência da pele.
- Complementa os cuidados diários com o rosto.
- Pode ser combinado com outros produtos de skincare compatíveis.

Modo de aplicação:
1. Lave o rosto com um gel de limpeza facial.
2. Seque delicadamente a pele.
3. Aplique a quantidade recomendada do sérum.
4. Espalhe suavemente pelo rosto.
5. Finalize com hidratante e, durante o dia, protetor solar.', '../imagens/serum-vitamina-c.png'),

('Máscara Capilar Hidratante', 'Produtos capilares', 55.90, 15,
'A Máscara Capilar é um tratamento cosmético desenvolvido para complementar os cuidados com os cabelos, ajudando a melhorar a aparência e a textura dos fios. É uma excelente opção para quem deseja dedicar mais atenção à hidratação capilar e manter os cabelos com aspecto macio, brilhante e bem cuidado.

Sua utilização pode fazer parte de uma rotina semanal de tratamento, especialmente para cabelos que apresentam aparência ressecada, opaca ou dificuldade de manter a maciez.

Principais características:
- Produto destinado ao tratamento capilar.
- Auxilia na manutenção da hidratação dos fios.
- Contribui para uma textura mais macia.
- Pode complementar a rotina de cuidados com os cabelos.
- Aplicação concentrada no comprimento e nas pontas.

Benefícios:
- Ajuda a melhorar a aparência dos fios ressecados.
- Proporciona sensação de maciez.
- Contribui para um aspecto mais saudável.
- Facilita o desembaraço dos cabelos.
- Complementa os cuidados realizados com shampoo e condicionador.

Modo de aplicação:
1. Lave os cabelos com shampoo.
2. Retire o excesso de água.
3. Aplique a máscara no comprimento e nas pontas.
4. Deixe agir pelo tempo indicado na embalagem.
5. Enxágue completamente.', NULL),

('Base Líquida Matte', 'Maquiagem', 42.50, 20,
'A Base Líquida Matte foi desenvolvida para proporcionar uma maquiagem uniforme, com acabamento aveludado e aparência sofisticada. Sua proposta é ajudar a disfarçar pequenas imperfeições, uniformizar o tom da pele e criar uma superfície mais homogênea para a aplicação dos demais produtos de maquiagem.

Principais características:
- Acabamento matte, com aparência menos brilhante.
- Textura líquida que facilita a aplicação.
- Ajuda a uniformizar o tom da pele.
- Proporciona uma aparência mais homogênea.
- Pode ser utilizada em maquiagens leves ou elaboradas.

Benefícios:
- Contribui para uma maquiagem mais uniforme.
- Ajuda a disfarçar pequenas diferenças de tonalidade.
- Facilita a preparação da pele para outros produtos.
- Permite construir camadas conforme a cobertura desejada.

Modo de aplicação:
1. Limpe e hidrate a pele antes da maquiagem.
2. Aplique pequenas quantidades da base no rosto.
3. Espalhe com pincel, esponja ou dedos.
4. Distribua uniformemente até obter o acabamento desejado.', NULL);

-- Imagem dos procedimentos fica NULL até você definir os caminhos das fotos
INSERT INTO procedimentos (nome, categoria, valor, descricao, duracao_minutos) VALUES
-- Cabelo (1-5)
('Corte de Cabelo',      'Cabelo',    50.00, 'Corte feminino ou masculino',                    60),
('Escova',               'Cabelo',    40.00, 'Escova e modelagem dos cabelos',                 45),
('Hidratação Capilar',   'Cabelo',    65.00, 'Tratamento de hidratação dos cabelos',           60),
('Coloração',            'Cabelo',   150.00, 'Coloração e renovação da cor dos cabelos',      120),
('Reconstrução Capilar', 'Cabelo',   100.00, 'Tratamento de reconstrução dos fios',            90),
-- Unhas (6-10)
('Manicure',             'Unhas',     30.00, 'Cuidados e esmaltação das unhas das mãos',       40),
('Pedicure',             'Unhas',     35.00, 'Cuidados e esmaltação das unhas dos pés',        50),
('Esmaltação em Gel',    'Unhas',     65.00, 'Esmaltação em gel nas unhas',                    60),
('Alongamento de Unhas', 'Unhas',    150.00, 'Aplicação de alongamento de unhas',             120),
('Spa dos Pés',          'Unhas',     65.00, 'Cuidado e relaxamento para os pés',              60),
-- Pele (11-15)
('Limpeza de Pele',      'Pele',      90.00, 'Limpeza facial profunda',                        90),
('Peeling Facial',       'Pele',     120.00, 'Procedimento estético de peeling facial',        60),
('Hidratação Facial',    'Pele',      75.00, 'Hidratação e cuidado facial',                    50),
('Design de Sobrancelhas','Pele',     35.00, 'Modelagem e design de sobrancelhas',             30),
('Design com Henna',     'Pele',      50.00, 'Design de sobrancelhas com aplicação de henna',  45),
-- Corporais (16-20)
('Massagem Relaxante',   'Corporais',100.00, 'Massagem corporal relaxante',                    60),
('Drenagem Linfática',   'Corporais',100.00, 'Massagem corporal de drenagem linfática',        60),
('Massagem Modeladora',  'Corporais',110.00, 'Massagem corporal modeladora',                   60),
('Esfoliação Corporal',  'Corporais', 80.00, 'Esfoliação para cuidado da pele do corpo',       50),
('Hidratação Corporal',  'Corporais', 70.00, 'Hidratação da pele do corpo',                    45);

-- Fernanda: cabelo | Camila: unhas | Patricia: pele e corporais | Rafael (gerente): corte
INSERT INTO funcionario_procedimentos (id_funcionario, id_procedimento) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
(2, 6), (2, 7), (2, 8), (2, 9), (2, 10),
(3, 11), (3, 12), (3, 13), (3, 14), (3, 15),
(3, 16), (3, 17), (3, 18), (3, 19), (3, 20),
(4, 1);

-- Segunda a sexta (1 a 5)
INSERT INTO disponibilidade_funcionario (id_funcionario, dia_semana, hora_inicio, hora_fim) VALUES
(1, 1, '08:00:00', '17:00:00'), (1, 2, '08:00:00', '17:00:00'), (1, 3, '08:00:00', '17:00:00'),
(1, 4, '08:00:00', '17:00:00'), (1, 5, '08:00:00', '17:00:00'),
(2, 1, '09:00:00', '18:00:00'), (2, 2, '09:00:00', '18:00:00'), (2, 3, '09:00:00', '18:00:00'),
(2, 4, '09:00:00', '18:00:00'), (2, 5, '09:00:00', '18:00:00'),
(3, 1, '08:00:00', '16:00:00'), (3, 2, '08:00:00', '16:00:00'), (3, 3, '08:00:00', '16:00:00'),
(3, 4, '08:00:00', '16:00:00'), (3, 5, '08:00:00', '16:00:00');

INSERT INTO bloqueios_agenda (id_funcionario, data, hora_inicio, hora_fim, motivo) VALUES
(1, '2026-12-03', '12:00:00', '13:00:00', 'Horário de almoço'),
(2, '2026-12-04', '14:00:00', '15:00:00', 'Compromisso pessoal'),
(3, '2026-12-07', '08:00:00', '10:00:00', 'Reunião interna');

INSERT INTO agendamentos (data, horario, duracao_minutos, id_funcionario, id_cliente, id_procedimento, status) VALUES
('2026-11-17', '14:00:00', 90, 3, 4, 11, 'Concluído'),   -- Limpeza de Pele
('2026-12-01', '09:00:00', 60, 1, 1,  1, 'Confirmado'),   -- Corte de Cabelo
('2026-12-01', '10:00:00', 45, 1, 2,  2, 'Pendente'),     -- Escova
('2026-12-01', '09:00:00', 40, 2, 3,  6, 'Confirmado'),   -- Manicure
('2026-12-02', '15:00:00', 50, 2, 5,  7, 'Cancelado');    -- Pedicure

INSERT INTO feedback (id_agendamento, nota, descricao) VALUES
(1, 5.0, 'Excelente atendimento!');

INSERT INTO vendas (id_cliente, id_funcionario, total) VALUES
(1, 1, 75.80),
(2, 2, 70.00),
(3, 3, 45.00),
(4, 1, 65.80);

INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) VALUES
(1, 1, 1, 35.90),
(1, 2, 1, 39.90),
(2, 3, 2, 12.50),
(2, 4, 1, 45.00),
(3, 4, 1, 45.00),
(4, 5, 1, 29.90),
(4, 1, 1, 35.90);
