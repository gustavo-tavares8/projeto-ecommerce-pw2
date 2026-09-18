<?php

class Produto
{
    private $pdo;

    public function conecta()
    {
        $dns = "mysql:dbname=gusttastore;host=localhost;charset=utf8mb4";
        $user = "root";
        $pass = "";

        try {
            $this->pdo = new PDO($dns, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function inserirProduto($nome, $descricao, $valor)
    {
        $sql = "INSERT INTO produto (nome_produto, descricao, valor)
                VALUES (:n, :d, :p)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':n', $nome);
        $stmt->bindValue(':d', $descricao);
        $stmt->bindValue(':p', $valor);
        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

    public function inserirImagem($nomeImagem, $idProduto)
    {
        $sql = "INSERT INTO imagem (nome_imagem, fk_id_produto)
                VALUES (:n, :id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':n', $nomeImagem);
        $stmt->bindValue(':id', $idProduto);
        return $stmt->execute();
    }

    public function listarProdutos()
    {
        $sql = "SELECT
                    p.id_produto,
                    p.nome_produto,
                    p.descricao,
                    p.valor,
                    i.nome_imagem
                FROM produto p
                LEFT JOIN imagem i ON i.fk_id_produto = p.id_produto
                ORDER BY p.id_produto DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
