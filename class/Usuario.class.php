<?php

class Usuario
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function inserirUsuario($nome, $email, $senha)
    {
        $sql = "INSERT INTO usuario (nome, email, senha) VALUES (:n, :e, :s)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":n", $nome);
        $stmt->bindValue(":e", $email);
        $stmt->bindValue(":s", md5($senha));

        return $stmt->execute();
    }

    public function checkUser($email)
    {
        $sql = "SELECT * FROM usuario WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":email", $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function checkPass($email, $senha)
    {
        $sql = "SELECT * FROM usuario WHERE email = :e AND senha = :s";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":e", $email);
        $stmt->bindValue(":s", md5($senha));
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function listarUsuarios()
    {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $usuarios = $stmt->fetchAll();
        return $usuarios !== false ? $usuarios : [];
    }

    public function listarUsuarioPorId($id)
    {
        $sql = "SELECT * FROM usuario WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":i", $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch();
        return $usuario !== false ? $usuario : [];
    }

    public function apagarUsuario($id)
    {
        $sql = "DELETE FROM usuario WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":i", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function alterarUsuario($id, $nome, $email, $senha)
    {
        $sql = "UPDATE usuario SET nome = :n, email = :e, senha = :s WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":n", $nome);
        $stmt->bindValue(":e", $email);
        $stmt->bindValue(":s", md5($senha));
        $stmt->bindValue(":i", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
