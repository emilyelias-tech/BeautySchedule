

-- tabela clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255) NOT NULL,
    rua VARCHAR(100),
    nro INT,
    cep VARCHAR(9),
    estado VARCHAR(2),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- tabela funcionarios
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

-- tabela produtos
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    materiais VARCHAR(100),
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT DEFAULT 0,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE
);

-- tabela procedimentos
CREATE TABLE procedimentos (
    id_procedimento INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    descricao VARCHAR(300),
    duracao_minutos INT NOT NULL,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE
);

-- tabela de procedimentos dos funcionarios
CREATE TABLE funcionario_procedimentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    id_procedimento INT NOT NULL,
    UNIQUE (id_funcionario, id_procedimento),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id),
    FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

-- tabela de disponibilidade
CREATE TABLE disponibilidade_funcionario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

-- tabela de bloqueios da agenda
CREATE TABLE bloqueios_agenda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcionario INT NOT NULL,
    data DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME NOT NULL,
    motivo VARCHAR(200),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

-- tabela de agendamentos
CREATE TABLE agendamentos (
    id_agendamento INT PRIMARY KEY AUTO_INCREMENT,
    data DATE NOT NULL,
    horario TIME NOT NULL,
    duracao_minutos INT NOT NULL,
    id_funcionario INT NOT NULL,
    id_cliente INT NOT NULL,
    id_procedimento INT NOT NULL,
    status VARCHAR(30) DEFAULT 'Pendente',
    motivo_cancelamento VARCHAR(300),
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (id_funcionario, data, horario),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_procedimento) REFERENCES procedimentos(id_procedimento)
);

-- tabela de feedback
CREATE TABLE feedback (
    id_feedback INT PRIMARY KEY AUTO_INCREMENT,
    id_agendamento INT NOT NULL UNIQUE,
    nota INT NOT NULL,
    descricao VARCHAR(300),
    data_feedback DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_agendamento) REFERENCES agendamentos(id_agendamento)
);

-- tabela de vendas
CREATE TABLE vendas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_cliente INT NOT NULL,
    id_funcionario INT,
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id)
);

-- tabela de itens da venda
CREATE TABLE itens_venda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_venda INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venda) REFERENCES vendas(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);

INSERT INTO clientes (nome, telefone, email, senha, rua, nro, cep, estado) VALUES
('Ana Silva', '11987654321', 'ana@gmail.com', '12345', 'Rua das Flores', 120, '09010-100', 'SP'),
('Beatriz Santos', '11976543210', 'bia@gmail.com', '56789', 'Rua Brasil', 250, '09020-200', 'SP'),
('Carla Oliveira', '11965432109', 'carla@gmail.com', '91011', 'Rua Central', 85, '09030-300', 'SP'),
('Mariana Costa', '11954321098', 'mariana@gmail.com', '121314', 'Rua das Palmeiras', 45, '09040-400', 'SP'),
('Juliana Souza', '11943210987', 'juliana@gmail.com', '252627', 'Rua São Paulo', 300, '09050-500', 'SP');

INSERT INTO funcionarios (nome, telefone, email, senha, cargo, rua, nro, cep, estado) VALUES
('Fernanda Lima', '11911112222', 'fernanda@gmail.com', 'fer123', 'Cabeleireira', 'Rua A', 100, '09010-100', 'SP'),
('Camila Rocha', '11922223333', 'camila@gmail.com', 'cam456', 'Manicure', 'Rua B', 200, '09020-200', 'SP'),
('Patricia Alves', '11933334444', 'patricia@gmail.com', 'pat789', 'Esteticista', 'Rua C', 300, '09030-300', 'SP'),
('Rafael Mendes', '11944445555', 'rafael@gmail.com', 'raf101', 'Gerente', 'Rua D', 400, '09040-400', 'SP');

INSERT INTO produtos (nome, materiais, preco, quantidade, imagem, ativo) VALUES
('Shampoo Profissional', 'Produtos capilares', 35.90, 20, 'imagens/1.png', TRUE),
('Condicionador', 'Produtos capilares', 39.90, 15, 'imagens/2.png', TRUE),
('Esmalte Vermelho', 'Produtos para unhas', 12.50, 30, 'imagens/3.png', TRUE),
('Creme Hidratante', 'Produtos para pele', 45.00, 10, 'imagens/4.png', TRUE),
('Óleo Capilar', 'Produtos capilares', 29.90, 25, 'imagens/5.png', TRUE);

INSERT INTO procedimentos (nome, valor, descricao, duracao_minutos, imagem, ativo) VALUES
('Corte de Cabelo', 50.00, 'Corte feminino ou masculino', 60, 'imagens/6.png', TRUE),
('Escova', 40.00, 'Escova e modelagem dos cabelos', 45, 'imagens/7.png', TRUE),
('Manicure', 30.00, 'Cuidados e esmaltação das unhas', 40, 'imagens/8.png', TRUE),
('Pedicure', 35.00, 'Cuidados e esmaltação dos pés', 50, 'imagens/9.png', TRUE),
('Limpeza de Pele', 90.00, 'Limpeza profunda da pele', 90, 'imagens/10.png', TRUE),
('Hidratação Capilar', 65.00, 'Tratamento de hidratação dos cabelos', 60, 'imagens/11.png', TRUE);

INSERT INTO funcionario_procedimentos (id_funcionario, id_procedimento) VALUES
(1, 1),
(1, 2),
(1, 6),
(2, 3),
(2, 4),
(3, 5),
(4, 1);

INSERT INTO disponibilidade_funcionario (id_funcionario, dia_semana, hora_inicio, hora_fim, ativo) VALUES
(1, 1, '08:00:00', '17:00:00', TRUE),
(1, 2, '08:00:00', '17:00:00', TRUE),
(1, 3, '08:00:00', '17:00:00', TRUE),
(1, 4, '08:00:00', '17:00:00', TRUE),
(1, 5, '08:00:00', '17:00:00', TRUE),
(2, 1, '09:00:00', '18:00:00', TRUE),
(2, 2, '09:00:00', '18:00:00', TRUE),
(2, 3, '09:00:00', '18:00:00', TRUE),
(2, 4, '09:00:00', '18:00:00', TRUE),
(2, 5, '09:00:00', '18:00:00', TRUE),
(3, 1, '08:00:00', '16:00:00', TRUE),
(3, 2, '08:00:00', '16:00:00', TRUE),
(3, 3, '08:00:00', '16:00:00', TRUE),
(3, 4, '08:00:00', '16:00:00', TRUE),
(3, 5, '08:00:00', '16:00:00', TRUE);

INSERT INTO bloqueios_agenda (id_funcionario, data, hora_inicio, hora_fim, motivo) VALUES
(1, '2026-11-12', '12:00:00', '13:00:00', 'Horário de almoço'),
(2, '2026-11-13', '14:00:00', '15:00:00', 'Compromisso pessoal'),
(3, '2026-11-14', '08:00:00', '10:00:00', 'Reunião interna');

INSERT INTO agendamentos (data, horario, duracao_minutos, id_funcionario, id_cliente, id_procedimento, status) VALUES
('2026-11-16', '09:00:00', 60, 1, 1, 1, 'Confirmado'),
('2026-11-16', '10:00:00', 45, 1, 2, 2, 'Pendente'),
('2026-11-16', '09:00:00', 40, 2, 3, 3, 'Confirmado'),
('2026-11-17', '14:00:00', 90, 3, 4, 5, 'Concluído'),
('2026-11-17', '15:00:00', 50, 2, 5, 4, 'Cancelado');

INSERT INTO feedback (id_agendamento, nota, descricao) VALUES
(4, 5, 'Excelente atendimento!'),
(5, 4, 'Gostei bastante do serviço.');

INSERT INTO vendas (id_cliente, id_funcionario, total) VALUES
(1, 1, 75.80),
(2, 2, 52.40),
(3, 3, 45.00),
(4, 1, 65.80);

INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) VALUES
(1, 1, 1, 35.90),
(1, 2, 1, 39.90),
(2, 3, 2, 12.50),
(2, 4, 1, 27.40),
(3, 4, 1, 45.00),
(4, 5, 1, 29.90),
(4, 1, 1, 35.90);