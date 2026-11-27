CREATE DATABASE Myfinance;
USE Myfinance;

-- 1. Tabela de Usuários
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL, -- Nunca salve senhas em texto puro
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2.Tabela de Categorias
CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    tipo_padrao ENUM('RECEITA', 'DESPESA') NOT NULL
);

-- 3. Tabela de Transações
CREATE TABLE transacoes (
    id_transacao INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    descricao VARCHAR(100),
    valor DECIMAL(10, 2) NOT NULL, -- Precisão fixa para valores monetários
    tipo_movimentacao ENUM('RECEITA', 'DESPESA') NOT NULL,
    data_ocorrencia DATE NOT NULL,
    
    -- Chaves Estrangeiras
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id_categoria) ON DELETE RESTRICT
);