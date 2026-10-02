<?php
require_once __DIR__ . '/class/Produto.class.php';

$p = new Produto();
$produto = null;
$imagens = [];
$erro = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {

    $erro = 'Produto não encontrado.';

} elseif (!$p->conecta()) {

    $erro = 'Não foi possível conectar ao banco de dados.';

} else {

    $produto = $p->buscarPorId($id);

    if (!$produto) {
        $erro = 'Produto não encontrado.';
    } else {
        $imagens = $p->listarImagens($id);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $produto ? htmlspecialchars($produto['nome_produto']) . ' - ' : '' ?>gusttastore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="catalogo-page">

<header class="catalogo-header">

    <div>
        <span class="catalogo-logo">GUSTTA</span>
        <h1><?= $produto ? htmlspecialchars($produto['nome_produto']) : 'Produto' ?></h1>
        <p><a href="catalogo.php" class="voltar-link">&larr; Voltar ao catálogo</a></p>
    </div>

    <a href="CadProduto.php" class="catalogo-button">+ Cadastrar produto</a>

</header>

<main class="catalogo-container">

    <?php if ($erro !== ''): ?>

        <div class="catalogo-mensagem erro">
            <?= htmlspecialchars($erro) ?>
        </div>

    <?php else: ?>

        <div class="produto-detalhe">

            <div class="produto-galeria">

                <?php if (count($imagens) > 0): ?>

                    <div class="galeria-principal">
                        <img
                            id="imagemPrincipal"
                            src="uploads/<?= htmlspecialchars($imagens[0]['nome_imagem']) ?>"
                            alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                        >
                    </div>

                    <?php if (count($imagens) > 1): ?>
                        <div class="galeria-miniaturas">
                            <?php foreach ($imagens as $img): ?>
                                <img
                                    src="uploads/<?= htmlspecialchars($img['nome_imagem']) ?>"
                                    class="miniatura"
                                    alt="<?= htmlspecialchars($produto['nome_produto']) ?>"
                                >
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>

                    <div class="sem-foto">Sem foto</div>

                <?php endif; ?>

            </div>

            <div class="produto-detalhe-info">

                <h2><?= htmlspecialchars($produto['nome_produto']) ?></h2>

                <p class="descricao">
                    <?= nl2br(htmlspecialchars($produto['descricao'])) ?>
                </p>

                <div class="preco-detalhe">
                    R$ <?= number_format((float) $produto['valor'], 2, ',', '.') ?>
                </div>

                <div class="detalhe-acoes">
                    <a class="btn-editar" href="editar_produto.php?id=<?= (int) $produto['id_produto'] ?>">Editar</a>

                    <form action="excluir_produto.php" method="POST"
                          onsubmit="return confirm('Excluir este produto e todas as fotos dele? Essa ação não pode ser desfeita.');">
                        <input type="hidden" name="id" value="<?= (int) $produto['id_produto'] ?>">
                        <button type="submit" class="btn-excluir">Excluir</button>
                    </form>
                </div>

            </div>

        </div>

    <?php endif; ?>

</main>

<script>
    // Troca a foto principal ao clicar numa miniatura
    const principal = document.getElementById('imagemPrincipal');
    const minis = document.querySelectorAll('.miniatura');

    if (principal && minis.length) {
        minis[0].classList.add('ativa');
        minis.forEach(m => m.addEventListener('click', () => {
            principal.src = m.src;
            minis.forEach(x => x.classList.remove('ativa'));
            m.classList.add('ativa');
        }));
    }
</script>

</body>
</html>
