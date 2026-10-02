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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="catalogo-page">

<header class="catalogo-header">

    <div>
        <span class="catalogo-logo">GUSTTA</span>
        <h1>Catálogo</h1>
        <p>Confira nossos produtos.</p>
    </div>

    <a href="CadProduto.php" class="catalogo-button">+ Cadastrar produto</a>

</header>

<main class="catalogo-container">

    <?php if (($_GET['msg'] ?? '') === 'excluido'): ?>
        <div class="msg ok">Produto excluído com sucesso.</div>
    <?php elseif (($_GET['msg'] ?? '') === 'erro_excluir'): ?>
        <div class="msg">Não foi possível excluir o produto.</div>
    <?php endif; ?>

    <?php if ($erro !== ''): ?>

        <div class="catalogo-mensagem erro">
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php elseif (count($produtos) === 0): ?>

        <div class="catalogo-mensagem">
            Nenhum produto cadastrado ainda.
        </div>

    <?php else: ?>

        <div class="catalogo-produtos">

            <?php foreach ($produtos as $produto): ?>

                <div class="produto-card">
                <a class="produto-link" href="exibir_produto.php?id=<?= (int) $produto['id_produto'] ?>">
                    <article class="produto">

                        <?php if (!empty($produto['nome_imagem'])): ?>
                            <div class="produto-imagem">
                                <img
                                    src="uploads/<?= htmlspecialchars($produto['nome_imagem']) ?>"
                                    alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                                >
                            </div>
                        <?php else: ?>
                            <div class="sem-foto">Sem foto</div>
                        <?php endif; ?>

                        <div class="produto-info">

                            <h2><?= htmlspecialchars($produto['nome_produto']) ?></h2>

                            <p class="descricao">
                                <?= nl2br(htmlspecialchars($produto['descricao'])) ?>
                            </p>

                            <div class="produto-footer">
                                <span class="preco">
                                    R$ <?= number_format((float) $produto['valor'], 2, ',', '.') ?>
                                </span>
                            </div>

                        </div>

                    </article>
                </a>

                    <div class="produto-acoes">
                        <a class="btn-editar" href="editar_produto.php?id=<?= (int) $produto['id_produto'] ?>">Editar</a>

                        <form action="excluir_produto.php" method="POST"
                              onsubmit="return confirm('Excluir este produto e todas as fotos dele? Essa ação não pode ser desfeita.');">
                            <input type="hidden" name="id" value="<?= (int) $produto['id_produto'] ?>">
                            <button type="submit" class="btn-excluir">Excluir</button>
                        </form>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</main>

</body>
</html>
