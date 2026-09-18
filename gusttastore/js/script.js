// Controle de quantidade
let menos = document.querySelector(".menos");
let mais = document.querySelector(".mais");
let qtd = document.getElementById("qtd");

mais.addEventListener("click", () => {
    qtd.value = parseInt(qtd.value) + 1;
});

menos.addEventListener("click", () => {
    if (parseInt(qtd.value) > 1) {
        qtd.value = parseInt(qtd.value) - 1;
    }
});

// Seletor de cor
let colorOptions = document.querySelectorAll('.color-option');
let colorName = document.getElementById('colorName');
let previewDot = document.getElementById('previewDot');
let previewText = document.getElementById('previewText');

// Cor selecionada inicial (a que já vem com a classe "selected" no HTML)
let corSelecionada = document.querySelector('.color-option.selected')?.dataset.name || null;

colorOptions.forEach(option => {
    option.addEventListener("click", () => {
        colorOptions.forEach(o => o.classList.remove("selected"));
        option.classList.add("selected");

        corSelecionada = option.dataset.name;

        colorName.textContent = option.dataset.name;
        previewText.textContent = option.dataset.name;
        previewDot.style.background = option.dataset.color;
    });
});

// Seleção de tamanhos (só existe em páginas que têm #container-tamanhos, ex: tenis.html)
let tamanhoSelecionado = null;
const containerTamanhos = document.getElementById('container-tamanhos');

if (containerTamanhos) {
    for (let i = 36; i <= 45; i++) {
        const botao = document.createElement('button');
        botao.innerText = i;
        botao.classList.add('tamanho');
        botao.dataset.tamanho = i;

        botao.addEventListener('click', function () {
            document.querySelectorAll('.tamanho').forEach(b => {
                b.style.background = '#333';
                b.style.color = '#fff';
            });

            this.style.background = 'black';
            this.style.color = 'white';
            tamanhoSelecionado = this.dataset.tamanho;
        });

        containerTamanhos.appendChild(botao);
    }
}

// Função para adicionar ao carrinho
document.querySelector(".btn-carrinho").addEventListener('click', function () {
    if (!corSelecionada) {
        alert('Por favor, selecione uma cor!');
        return;
    }

    // Só exige tamanho se a página tiver o seletor de tamanhos (ex: tenis.html)
    if (containerTamanhos && !tamanhoSelecionado) {
        alert('Por favor, selecione um tamanho!');
        return;
    }

    let produtoObj = {
        id: 'produto-' + corSelecionada.toLowerCase() + (tamanhoSelecionado ? '-' + tamanhoSelecionado : ''),
        nome: document.getElementById('produto-nome').textContent,
        preco: parseFloat(document.getElementById('produto-preco').dataset.preco),
        quantidade: parseInt(qtd.value),
        cor: corSelecionada,
        ...(tamanhoSelecionado && { tamanho: tamanhoSelecionado })
    };

    let msg = 'Produto adicionado ao carrinho!\nCor: ' + corSelecionada;
    if (tamanhoSelecionado) msg += '\nTamanho: ' + tamanhoSelecionado;
    msg += '\nQuantidade: ' + qtd.value;

    alert(msg);
    console.log('Produto adicionado:', produtoObj);
});