<?php

class Produto
{
    private $pdo;

    public function conecta()
    {
        $dsn  = "mysql:dbname=gusttastore;host=localhost;charset=utf8mb4";
        $user = "root";
        $pass = "";

        try {
            $this->pdo = new PDO($dsn, $user, $pass);
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

    /**
     * Lista os produtos para o catálogo.
     * Traz apenas UMA imagem (a primeira cadastrada) como capa de cada
     * produto, em vez de dar JOIN direto com a tabela imagem — que
     * duplicava o card do produto no catálogo quando ele tinha mais de
     * uma foto.
     */
    public function listarProdutos()
    {
        $sql = "SELECT
                    p.id_produto,
                    p.nome_produto,
                    p.descricao,
                    p.valor,
                    (
                        SELECT i.nome_imagem
                        FROM imagem i
                        WHERE i.fk_id_produto = p.id_produto
                        ORDER BY i.id_imagem ASC
                        LIMIT 1
                    ) AS nome_imagem
                FROM produto p
                ORDER BY p.id_produto DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Busca um único produto pelo id, para a tela de exibição do produto.
     */
    public function buscarPorId($id)
    {
        $sql = "SELECT id_produto, nome_produto, descricao, valor
                FROM produto
                WHERE id_produto = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $produto = $stmt->fetch();
        return $produto !== false ? $produto : null;
    }

    /**
     * Lista todas as fotos de um produto, para a galeria da tela de
     * exibição do produto.
     */
    public function listarImagens($idProduto)
    {
        $sql = "SELECT id_imagem, nome_imagem
                FROM imagem
                WHERE fk_id_produto = :id
                ORDER BY id_imagem ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $idProduto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Atualiza nome, descrição e valor de UM produto (pelo id).
     */
    public function atualizarProduto($id, $nome, $descricao, $valor)
    {
        $sql = "UPDATE produto
                SET nome_produto = :n, descricao = :d, valor = :p
                WHERE id_produto = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':n', $nome);
        $stmt->bindValue(':d', $descricao);
        $stmt->bindValue(':p', $valor);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Salva várias fotos enviadas pelo <input type="file" name="imagens[]">
     * e vincula todas ao produto. Retorna quantas foram salvas.
     */
    public function salvarImagens($arquivos, $idProduto)
    {
        if (!isset($arquivos['name']) || !is_array($arquivos['name'])) {
            return 0;
        }

        $pasta = dirname(__DIR__) . '/uploads';
        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $salvas = 0;

        foreach ($arquivos['name'] as $i => $nomeOriginal) {
            if ($arquivos['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            if (!in_array($ext, $permitidas, true)) {
                continue;
            }

            $nomeArquivo = uniqid('prod_' . $idProduto . '_') . '.' . $ext;

            if (move_uploaded_file($arquivos['tmp_name'][$i], $pasta . '/' . $nomeArquivo)) {
                $this->inserirImagem($nomeArquivo, $idProduto);
                $salvas++;
            }
        }

        return $salvas;
    }

    /**
     * Remove UMA foto de um produto (registro + arquivo em /uploads).
     */
    public function excluirImagem($idImagem, $idProduto)
    {
        $stmt = $this->pdo->prepare(
            "SELECT nome_imagem FROM imagem
             WHERE id_imagem = :i AND fk_id_produto = :p"
        );
        $stmt->bindValue(':i', $idImagem, PDO::PARAM_INT);
        $stmt->bindValue(':p', $idProduto, PDO::PARAM_INT);
        $stmt->execute();
        $nome = $stmt->fetchColumn();

        if ($nome === false) {
            return false;
        }

        $del = $this->pdo->prepare("DELETE FROM imagem WHERE id_imagem = :i");
        $del->bindValue(':i', $idImagem, PDO::PARAM_INT);
        $del->execute();

        $this->apagarArquivo($nome);
        return true;
    }

    /**
     * Exclui SOMENTE o produto informado, com as fotos dele.
     * Os outros produtos não são tocados.
     */
    public function excluirProduto($id)
    {
        $imagens = $this->listarImagens($id);

        $this->pdo->beginTransaction();
        try {
            $a = $this->pdo->prepare("DELETE FROM imagem WHERE fk_id_produto = :id");
            $a->bindValue(':id', $id, PDO::PARAM_INT);
            $a->execute();

            $b = $this->pdo->prepare("DELETE FROM produto WHERE id_produto = :id");
            $b->bindValue(':id', $id, PDO::PARAM_INT);
            $b->execute();

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return false;
        }

        if ($b->rowCount() === 0) {
            return false;
        }

        foreach ($imagens as $img) {
            $this->apagarArquivo($img['nome_imagem']);
        }
        return true;
    }

    private function apagarArquivo($nome)
    {
        $caminho = dirname(__DIR__) . '/uploads/' . basename($nome);
        if (is_file($caminho)) {
            @unlink($caminho);
        }
    }
}
