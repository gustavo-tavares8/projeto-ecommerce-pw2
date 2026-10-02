<?php
require_once __DIR__ . '/class/Usuario.class.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $pdo = new PDO(
        "mysql:dbname=gusttastore;host=localhost;charset=utf8mb4",
        "root",
        ""
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<h1>Erro na conexão com o banco de dados.</h1>");
}

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($nome === '' || $email === '' || $senha === '') {
    die("<h1>Preencha todos os campos.</h1>");
}

$usuario = new Usuario($pdo);

if ($usuario->checkUser($email)) {
    die("<h1>Este e-mail já está cadastrado.</h1>");
}

if ($usuario->inserirUsuario($nome, $email, $senha)) {
    // A página HTML/HomePage.html do projeto original não existia mais
    // neste pacote de arquivos, então o cadastro agora leva direto para
    // o catálogo de produtos.
    header("Location: catalogo.php");
    exit;
} else {
    die("<h1>Erro ao cadastrar usuário.</h1>");
}
