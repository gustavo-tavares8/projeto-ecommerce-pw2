<?php
// Esta página é o cadastro de usuário do seu projeto original.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar - gusttastore</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body class="cadastro-page">

<div class="cadastro-container">

    <div class="cadastro-box">

        <div class="cadastro-header">
            <h1>Crie sua conta</h1>
            <p>É rápido, fácil e seguro.</p>
        </div>

        <form
            class="cadastro-form"
            action="PHP/Usuario.class.php"
            method="POST"
        >

            <div class="cadastro-field">
                <label for="nome">Nome</label>

                <input
                    type="text"
                    name="nome"
                    id="nome"
                    placeholder="Digite seu nome"
                    required
                >
            </div>


            <div class="cadastro-field">
                <label for="email">E-mail</label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Digite seu e-mail"
                    required
                >
            </div>


            <div class="cadastro-field">
                <label for="senha">Senha</label>

                <input
                    type="password"
                    name="senha"
                    id="senha"
                    placeholder="Digite sua senha"
                    required
                >
            </div>


            <label class="cadastro-termos">
                <input
                    type="checkbox"
                    name="termos"
                    required
                >

                <span>
                    Eu concordo com os termos.
                </span>
            </label>


            <button
                type="submit"
                class="cadastro-button"
            >
                Cadastrar
            </button>

        </form>


        <div class="cadastro-login">
            <a href="CadProduto.php">
                Ir para cadastro de produto
            </a>
        </div>

    </div>

</div>

</body>
</html>