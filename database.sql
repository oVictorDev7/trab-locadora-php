CREATE DATABASE IF NOT EXISTS locadora;
USE locadora;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL UNIQUE,
    descricao VARCHAR(255) NULL
);

CREATE TABLE filmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    genero VARCHAR(60) NOT NULL,
    ano INT NOT NULL,
    valor_locacao DECIMAL(10,2) NOT NULL,
    duracao INT NOT NULL,
    idade_recomendada VARCHAR(10) NOT NULL DEFAULT 'Livre',
    imagem VARCHAR(255) NULL
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    nascimento DATE NOT NULL,
    tipo ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario'
);

CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE locacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    filme_id INT NOT NULL,
    data_locacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
);

INSERT INTO categorias (nome, descricao) VALUES
    ('Ação', 'Filmes com muita adrenalina e cenas de tirar o fôlego'),
    ('Comédia', 'Filmes leves e divertidos para rir'),
    ('Terror', 'Filmes de suspense e medo');

INSERT INTO usuarios (nome, email, senha, cpf, nascimento, tipo) VALUES (
    'Administrador',
    'admin@locadora.com',
    '$2y$10$sYL..fnsT0UXZzzZVXdkteq9Zu/xdUfcKayu7CPYLIA4lcKwxYOim',
    '00000000000',
    '2000-01-01',
    'admin'
);
