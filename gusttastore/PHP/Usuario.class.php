<?php
class Usuario
{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSenha()
    {
        return $this->senha;
    }

    public function setSenha($senha)
    {
        $this->senha = $senha;
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

        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll();
        } else {
            return array();
        }
    }

    public function listarUsuarioPorId($id)
    {
        $sql = "SELECT * FROM usuario WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":i", $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        } else {
            return array();
        }
    }

    public function apagarUsuario($id)
    {
        $sql = "DELETE FROM usuario WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":i", $id);
        $stmt->execute();
    }

    public function alterarUsuario($id, $nome, $email, $senha)
    {
        $sql = "UPDATE usuario SET nome = :n, email = :e, senha = :s WHERE id = :i";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":n", $nome);
        $stmt->bindValue(":e", $email);
        $stmt->bindValue(":s", md5($senha));
        $stmt->bindValue(":i", $id);
        $stmt->execute();
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $pdo = new PDO("mysql:dbname=gusttastore;host=localhost;charset=utf8", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }

    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($nome && $email && $senha) {
        $usuario = new Usuario($pdo);

        if ($usuario->checkUser($email)) {
            echo "<h1>Este e-mail já está cadastrado.</h1>";
        } else {
            if ($usuario->inserirUsuario($nome, $email, $senha)) {
                header("Location: ../HTML/HomePage.html");
                exit;
            } else {
                echo "<h1>Erro ao cadastrar usuário.</h1>";
            }
        }
    } else {
        echo "<h1>Preencha todos os campos.</h1>";
    }
}
