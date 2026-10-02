<?php
require_once __DIR__ . '/class/Produto.class.php';

$p        = new Produto();
$id       = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);
$produto  = null;
$imagens  = [];
$mensagem = '';

if ($id <= 0) {

    $mensagem = 'Produto não encontrado.';

} elseif (!$p->conecta()) {

    $mensagem = 'Não foi possível conectar ao banco de dados.';

} else {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Se o total de fotos passar do limite do PHP (post_max_size),
        // o $_POST chega vazio. Avisa em vez de dar erro confuso.
        if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {

            $mensagem = 'As fotos passaram do limite de upload do servidor. Envie menos fotos ou fotos menores.';

        } else {

            $nome      = trim($_POST['nome_produto'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $valor     = str_replace(',', '.', trim($_POST['valor'] ?? ''));

            if ($nome === '' || $descricao === '' || $valor === '' || !is_numeric($valor)) {

                $mensagem = 'Preencha nome, descrição e um valor válido.';

            } else {

                try {
                    $p->atualizarProduto($id, $nome, $descricao, (float) $valor);

                    foreach (($_POST['remover_imagens'] ?? []) as $idImg) {
                        $p->excluirImagem((int) $idImg, $id);
                    }

                    if (isset($_FILES['imagens'])) {
                        $p->salvarImagens($_FILES['imagens'], $id);
                    }

                    header('Location: exibir_produto.php?id=' . $id);
                    exit;

                } catch (PDOException $e) {
                    $mensagem = 'Erro ao salvar as alterações.';
                }
            }
        }
    }

    $produto = $p->buscarPorId($id);

    if (!$produto) {
        $mensagem = 'Produto não encontrado.';
    } else {
        $imagens = $p->listarImagens($id);
    }
}

// Mantém o que a pessoa digitou se der erro de validação
$vNome  = $_POST['nome_produto'] ?? ($produto['nome_produto'] ?? '');
$vDesc  = $_POST['descricao']    ?? ($produto['descricao'] ?? '');
$vValor = $_POST['valor']        ?? ($produto['valor'] ?? '');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - gusttastore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="produto-cadastro-page">

<div class="produto-cadastro-container">
    <div class="produto-cadastro-box">

        <div class="produto-cadastro-header">
            <h1>Editar Produto</h1>
            <p>Altere os dados, remova ou adicione fotos.</p>
        </div>

        <?php if ($mensagem !== ''): ?>
            <div class="msg"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if ($produto): ?>

        <form class="produto-cadastro-form" action="editar_produto.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= (int) $produto['id_produto'] ?>">

            <div class="produto-field">
                <label for="nome_produto">Nome do produto</label>
                <input type="text" id="nome_produto" name="nome_produto"
                       value="<?= htmlspecialchars($vNome) ?>" required>
            </div>

            <div class="produto-field">
                <label for="valor">Valor (R$)</label>
                <input type="number" id="valor" name="valor" step="0.01" min="0"
                       value="<?= htmlspecialchars($vValor) ?>" required>
            </div>

            <div class="produto-field">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" required><?= htmlspecialchars($vDesc) ?></textarea>
            </div>

            <?php if (count($imagens) > 0): ?>
                <div class="produto-field">
                    <label>Fotos atuais</label>
                    <div class="fotos-atuais">
                        <?php foreach ($imagens as $img): ?>
                            <div class="foto-item">
                                <img src="uploads/<?= htmlspecialchars($img['nome_imagem']) ?>" alt="Foto do produto">
                                <label class="foto-remover">
                                    <input type="checkbox" name="remover_imagens[]" value="<?= (int) $img['id_imagem'] ?>">
                                    Remover
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <small>Marque as fotos que deseja remover.</small>
                </div>
            <?php endif; ?>

            <div class="produto-field">
                <label for="imagens">Adicionar novas fotos</label>
                <input type="file" id="imagens" name="imagens[]" accept="image/*" multiple>
                <small>Você pode selecionar várias imagens de uma vez (jpg, png, gif, webp).</small>
            </div>

            <button type="submit" class="produto-cadastro-button">Salvar alterações</button>

        </form>

        <?php endif; ?>

        <div class="produto-cadastro-links">
            <a href="<?= $produto ? 'exibir_produto.php?id=' . (int) $produto['id_produto'] : 'catalogo.php' ?>">Cancelar</a>
        </div>

    </div>
</div>

</body>
</html>
