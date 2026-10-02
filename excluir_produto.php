<?php
require_once __DIR__ . '/class/Produto.class.php';

// Só aceita POST (evita apagar produto digitando a URL no navegador)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: catalogo.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$p  = new Produto();
$ok = false;

if ($id > 0 && $p->conecta()) {
    try {
        $ok = $p->excluirProduto($id); // apaga SÓ esse produto + fotos dele
    } catch (PDOException $e) {
        $ok = false;
    }
}

header('Location: catalogo.php?msg=' . ($ok ? 'excluido' : 'erro_excluir'));
exit;
