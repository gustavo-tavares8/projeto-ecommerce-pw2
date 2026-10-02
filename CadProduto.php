<?php
require_once __DIR__ . '/class/Produto.class.php';

$p = new Produto();
$mensagem = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome      = trim($_POST['nome_produto'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $valor     = str_replace(',', '.', trim($_POST['valor'] ?? ''));

    if ($nome === '' || $descricao === '' || $valor === '' || !is_numeric($valor)) {

        $mensagem = 'Preencha nome, descrição e um valor válido.';

    } elseif (!$p->conecta()) {

        $mensagem = 'Não foi possível conectar ao banco de dados.';

    } else {

        $idProduto = $p->inserirProduto($nome, $descricao, (float) $valor);

        $pastaUploads = __DIR__ . '/uploads';
        if (!is_dir($pastaUploads)) {
            mkdir($pastaUploads, 0777, true);
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $totalImagens = isset($_FILES['imagens']) ? count($_FILES['imagens']['name']) : 0;
        $fotosSalvas  = 0;

        for ($i = 0; $i < $totalImagens; $i++) {

            if ($_FILES['imagens']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $nomeOriginal = $_FILES['imagens']['name'][$i];
            $extensao     = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

            if (!in_array($extensao, $extensoesPermitidas, true)) {
                continue;
            }

            // Gera um nome novo e único para o arquivo, evitando
            // sobrescrever imagens e problemas com nomes inseguros.
            $nomeArquivo     = uniqid('prod_' . $idProduto . '_') . '.' . $extensao;
            $caminhoDestino  = $pastaUploads . '/' . $nomeArquivo;

            if (move_uploaded_file($_FILES['imagens']['tmp_name'][$i], $caminhoDestino)) {
                $p->inserirImagem($nomeArquivo, $idProduto);
                $fotosSalvas++;
            }
        }

        $sucesso  = true;
        $mensagem = 'Produto cadastrado com sucesso'
            . ($fotosSalvas > 0 ? " ({$fotosSalvas} foto(s))." : ', sem fotos.');
    }
}
?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Cadastrar Produto - gusttastore</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body class="produto-cadastro-page">

<div class="produto-cadastro-container">

    <div class="produto-cadastro-box">

        <div class="produto-cadastro-header">
            <h1>Cadastrar Produto</h1>
            <p>Adicione um novo produto ao catálogo da gusttastore.</p>
        </div>

        <?php if ($mensagem !== ''): ?>
            <div class="msg <?= $sucesso ? 'ok' : '' ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <form
            class="produto-cadastro-form"
            action="CadProduto.php"
            method="POST"
            enctype="multipart/form-data"
        >

            <div class="produto-field">
                <label for="nome_produto">Nome do produto</label>
                <input
                    type="text"
                    id="nome_produto"
                    name="nome_produto"
                    placeholder="Ex: Calça, Celular, Fogão, Sofá..."
                    required
                >
            </div>

            <div class="produto-field">
                <label for="valor">Valor (R$)</label>
                <input
                    type="number"
                    id="valor"
                    name="valor"
                    step="0.01"
                    min="0"
                    placeholder="Ex: 129.90"
                    required
                >
            </div>

            <div class="produto-field">
                <label for="descricao">Descrição</label>
                <textarea
                    id="descricao"
                    name="descricao"
                    placeholder="Descreva o produto..."
                    required
                ></textarea>
            </div>

            <div class="produto-field">
                <label for="imagens">Fotos do produto</label>
                <input
                    type="file"
                    id="imagens"
                    name="imagens[]"
                    accept="image/*"
                    multiple
                >
                <small>Você pode selecionar uma ou várias imagens de uma vez.</small>
            </div>

            <button type="submit" class="produto-cadastro-button">
                Cadastrar
            </button>

        </form>

        <div class="produto-cadastro-links">
            <a href="catalogo.php">Ver catálogo de produtos</a>
        </div>

    </div>

</div>

</body>
</html>
