<?php
require_once __DIR__ . '/class/Produto.class.php';

$p = new Produto();
$produtos = [];
$erro = '';

if ($p->conecta()) {
    $produtos = $p->listarProdutos();
} else {
    $erro = 'Não foi possível conectar ao banco de dados.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - gusttastore</title>
    <style>
        * { box-sizing:border-box; font-family:Arial,sans-serif; }
        body { margin:0; background:#f5f5f6; color:#222; }
        header { background:#fff; padding:24px; text-align:center; box-shadow:0 2px 10px rgba(0,0,0,.06); }
        header h1 { margin:0 0 8px; }
        header a { color:#FD5800; font-weight:bold; text-decoration:none; }
        main { max-width:1100px; margin:30px auto; padding:0 20px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:22px; }
        .produto { background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,.08); }
        .produto img { width:100%; height:220px; object-fit:cover; display:block; background:#eee; }
        .sem-foto { height:220px; display:flex; align-items:center; justify-content:center; background:#eee; color:#777; }
        .info { padding:18px; }
        .info h2 { margin:0 0 8px; font-size:20px; }
        .descricao { color:#666; min-height:45px; }
        .preco { font-size:20px; font-weight:bold; margin-top:14px; color:#FD5800; }
        .vazio, .erro { background:#fff; padding:25px; border-radius:12px; text-align:center; }
    </style>
</head>
<body>
<header>
    <h1>Catálogo gusttastore</h1>
    <a href="CadProduto.php">Cadastrar novo produto</a>
</header>

<main>
    <?php if ($erro !== ''): ?>
        <div class="erro"><?= htmlspecialchars($erro) ?></div>
    <?php elseif (count($produtos) === 0): ?>
        <div class="vazio">Nenhum produto cadastrado ainda.</div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($produtos as $produto): ?>
                <article class="produto">
                    <?php if (!empty($produto['nome_imagem'])): ?>
                        <img src="uploads/<?= htmlspecialchars($produto['nome_imagem']) ?>"
                             alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                    <?php else: ?>
                        <div class="sem-foto">Sem foto</div>
                    <?php endif; ?>
                    <div class="info">
                        <h2><?= htmlspecialchars($produto['nome_produto']) ?></h2>
                        <div class="descricao"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></div>
                        <div class="preco">R$ <?= number_format((float)$produto['valor'], 2, ',', '.') ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
