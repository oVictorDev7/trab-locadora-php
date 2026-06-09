CREATE DATABASE IF NOT EXISTS locadora;
USE locadora;

-- CRUD: Categorias de filmes.
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(60) NOT NULL UNIQUE,
    descricao VARCHAR(255) NULL
);

-- CRUD: Filmes.
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

-- CRUD: Usuarios. cpf e nascimento sao usados na recuperacao de senha.
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    nascimento DATE NOT NULL,
    tipo ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario'
);

-- Mensagens enviadas pelo formulario de contato (visiveis para o admin).
CREATE TABLE mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Locacoes: registra qual usuario alugou qual filme.
CREATE TABLE locacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    filme_id INT NOT NULL,
    data_locacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (filme_id) REFERENCES filmes(id) ON DELETE CASCADE
);

-- Categorias iniciais de exemplo.
INSERT INTO categorias (nome, descricao) VALUES
    ('Ação', 'Filmes com muita adrenalina e cenas de tirar o fôlego'),
    ('Comédia', 'Filmes leves e divertidos para rir'),
    ('Terror', 'Filmes de suspense e medo');

-- Usuário admin padrão. Senha: admin123 (hash gerado com password_hash).
INSERT INTO usuarios (nome, email, senha, cpf, nascimento, tipo) VALUES (
    'Administrador',
    'admin@locadora.com',
    '$2y$10$sYL..fnsT0UXZzzZVXdkteq9Zu/xdUfcKayu7CPYLIA4lcKwxYOim',
    '00000000000',
    '2000-01-01',
    'admin'
);
