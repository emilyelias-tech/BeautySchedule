-- Ajuste na tabela clientes:
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14),
    data_nascimento DATE,
    pontos INT DEFAULT 680,
    foto VARCHAR(255),
    rua VARCHAR(100),
    nro INT,
    cep VARCHAR(9),
    estado VARCHAR(2),
    data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Ajuste na tabela produtos (inclusão de descricao):
CREATE TABLE produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    materiais VARCHAR(100),
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT DEFAULT 0,
    imagem VARCHAR(255),
    ativo BOOLEAN DEFAULT TRUE
);

-- Ajuste na tabela agendamentos (inclusão de observacao):
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

-- Tabela enderecos_clientes:
CREATE TABLE enderecos_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    titulo VARCHAR(50) DEFAULT 'Casa',
    cep VARCHAR(15) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(100) DEFAULT '',
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    principal TINYINT(1) DEFAULT 0,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Correção do INSERT final em database/schema.sql:
INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) VALUES
(1, 1, 1, 35.90),
(1, 2, 1, 39.90),
(2, 3, 2, 12.50),
(2, 4, 1, 27.40),
(3, 4, 1, 45.00),
(4, 5, 1, 29.90),
(4, 1, 1, 35.90);