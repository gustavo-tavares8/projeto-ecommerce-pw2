CREATE DATABASE IF NOT EXISTS gusttastore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gusttastore;

CREATE TABLE IF NOT EXISTS produto (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    nome_produto VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    valor DECIMAL(10,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS imagem (
    id_imagem INT AUTO_INCREMENT PRIMARY KEY,
    nome_imagem VARCHAR(255) NOT NULL,
    fk_id_produto INT NOT NULL,
    FOREIGN KEY (fk_id_produto) REFERENCES produto(id_produto) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(32) NOT NULL
);
