    //Filtro
    const buttons = document.querySelectorAll('.nav-btn');
    const produtos = document.querySelectorAll('.produto');

    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const categoria = btn.textContent.trim().toLowerCase();

            produtos.forEach(produto => {
                const prodCat = produto.getAttribute('data-category');
                const link = produto.closest('a'); // pega o <a> que envolve o produto

                if (categoria === "todos" || categoria === prodCat) {
                    link.style.display = "block";
                } else {
                    link.style.display = "none";
                }
            });
        });
    });

    const pesquisa = document.querySelector('.pesquisa-text');

    pesquisa.addEventListener('input', (event) => {
        const searchValue = formatacao(event.target.value);
        const items = document.querySelectorAll('.produto');

        items.forEach(item => {
            const itemText = formatacao(item.textContent);
            const link = item.closest('a');

            if (itemText.indexOf(searchValue) !== -1) {
                link.style.display = "block";
            } else {
                link.style.display = "none";
            }
        });
    });

    function formatacao(value) {
        return value
            .toLowerCase()
            .trim();
    }