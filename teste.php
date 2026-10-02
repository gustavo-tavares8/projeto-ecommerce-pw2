<?php

require __DIR__ . '/class/Produto.class.php';

$p = new Produto();

$con = $p->conecta();

if ($con) {
    echo "<h1>Conectado com sucesso!</h1>";
} else {
    echo "<h1>Falha ao conectar!</h1>";
}
