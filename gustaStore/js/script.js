/* =====================================================
   NEXUS STORE
   Main JavaScript
===================================================== */


const products = [

    {
        id: 1,

        name: "Nexus MK Pro",

        category: "Teclado mecânico",

        description:
            "Teclado mecânico premium para performance.",

        price: 1299.90,

        image:
            "assets/imagens/keyboard-02.jpg",

        tag: "Destaque"
    },


    {
        id: 2,

        name: "Nexus X Pro",

        category: "Mouse gamer",

        description:
            "Sensor de alta precisão e construção ultraleve.",

        price: 899.90,

        image:
            "assets/imagens/mouse-02.jpg",

        tag: "Novo"
    },


    {
        id: 3,

        name: "Nexus H7 Wireless",

        category: "Headset",

        description:
            "Áudio premium com conexão com e sem fio.",

        price: 1199.90,

        image:
            "assets/imagens/headset-02-removebg-preview.png",

        tag: "Premium"
    },


    {
        id: 4,

        name: "Nexus Surface X",

        category: "Mousepad",

        description:
            "Superfície desenvolvida para máxima precisão.",

        price: 349.90,

        image:
            "assets/imagens/mousepad-02-removebg-preview.png",

        tag: ""
    }

];


/* =====================================================
   ESTADO DO CARRINHO
===================================================== */

let cart = [];


/* =====================================================
   ELEMENTOS
===================================================== */

const productsGrid =
    document.getElementById("productsGrid");

const cartButton =
    document.getElementById("cartButton");

const cartDrawer =
    document.getElementById("cartDrawer");

const cartOverlay =
    document.getElementById("cartOverlay");

const cartClose =
    document.getElementById("cartClose");

const cartItems =
    document.getElementById("cartItems");

const cartTotal =
    document.getElementById("cartTotal");

const cartCount =
    document.querySelector(".cart-count");

const searchButton =
    document.getElementById("searchButton");

const searchOverlay =
    document.getElementById("searchOverlay");

const searchClose =
    document.getElementById("searchClose");

const searchInput =
    document.getElementById("searchInput");

const newsletterForm =
    document.getElementById("newsletterForm");


/* =====================================================
   FORMATAR PREÇO
===================================================== */

function formatPrice(price) {

    return price.toLocaleString(
        "pt-BR",
        {
            style: "currency",
            currency: "BRL"
        }
    );

}


/* =====================================================
   RENDERIZAR PRODUTOS
===================================================== */

function renderProducts(items = products) {

    productsGrid.innerHTML = "";


    if (items.length === 0) {

        productsGrid.innerHTML = `

            <div class="no-products">

                <h3>
                    Nenhum produto encontrado.
                </h3>

            </div>

        `;

        return;
    }


    items.forEach(product => {

        const card =
            document.createElement("article");

        card.className = "product-card";


        card.innerHTML = `

            <div class="product-image">

                ${
                    product.tag

                    ? `
                        <span class="product-tag">
                            ${product.tag}
                        </span>
                    `
                    : ""
                }

                <img
                    src="${product.image}"
                    alt="${product.name}"
                    loading="lazy"
                >

            </div>


            <div class="product-info">

                <span class="product-category">
                    ${product.category}
                </span>

                <h3 class="product-name">
                    ${product.name}
                </h3>

                <p class="product-description">
                    ${product.description}
                </p>


                <div class="product-bottom">

                    <strong class="product-price">
                        ${formatPrice(product.price)}
                    </strong>


                    <button
                        class="add-cart"
                        data-product-id="${product.id}"
                        aria-label="Adicionar ao carrinho"
                    >

                        <svg
                            width="17"
                            height="17"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path d="M12 5v14"></path>

                            <path d="M5 12h14"></path>

                        </svg>

                    </button>

                </div>

            </div>

        `;


        productsGrid.appendChild(card);

    });


    /*
        Depois de criar os produtos,
        adicionamos os eventos dos botões.
    */

    document
        .querySelectorAll(".add-cart")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const id =
                        Number(
                            button.dataset.productId
                        );

                    addToCart(id);

                }
            );

        });

}


/* =====================================================
   ADICIONAR AO CARRINHO
===================================================== */

function addToCart(productId) {

    const product =
        products.find(
            item => item.id === productId
        );


    if (!product) {
        return;
    }


    const existing =
        cart.find(
            item => item.id === productId
        );


    if (existing) {

        existing.quantity += 1;

    } else {

        cart.push({

            ...product,

            quantity: 1

        });

    }


    updateCart();

    openCart();

}


/* =====================================================
   REMOVER DO CARRINHO
===================================================== */

function removeFromCart(productId) {

    cart =
        cart.filter(
            item => item.id !== productId
        );


    updateCart();

}


/* =====================================================
   ATUALIZAR CARRINHO
===================================================== */

function updateCart() {

    renderCart();

    updateCartCount();

}


/* =====================================================
   CONTADOR
===================================================== */

function updateCartCount() {

    const quantity =
        cart.reduce(
            (total, item) =>
                total + item.quantity,
            0
        );


    cartCount.textContent =
        quantity;

}


/* =====================================================
   RENDER CARRINHO
===================================================== */

function renderCart() {

    if (cart.length === 0) {

        cartItems.innerHTML = `

            <div class="empty-cart">

                <div class="empty-cart-icon">
                    🛒
                </div>

                <h4>
                    Seu carrinho está vazio
                </h4>

                <p>
                    Adicione alguns produtos
                    para começar.
                </p>

            </div>

        `;

        cartTotal.textContent =
            formatPrice(0);

        return;
    }


    cartItems.innerHTML = "";


    cart.forEach(item => {

        const element =
            document.createElement("div");

        element.className =
            "cart-item";


        element.innerHTML = `

            <div class="cart-item-image">

                <img
                    src="${item.image}"
                    alt="${item.name}"
                >

            </div>


            <div>

                <h4 class="cart-item-name">
                    ${item.name}
                </h4>

                <p class="cart-item-price">
                    ${item.quantity}x
                    ${formatPrice(item.price)}
                </p>

            </div>


            <button
                class="remove-item"
                data-remove-id="${item.id}"
            >
                Remover
            </button>

        `;


        cartItems.appendChild(element);

    });


    document
        .querySelectorAll(".remove-item")
        .forEach(button => {

            button.addEventListener(
                "click",
                () => {

                    const id =
                        Number(
                            button.dataset.removeId
                        );

                    removeFromCart(id);

                }
            );

        });


    const total =
        cart.reduce(
            (sum, item) =>
                sum +
                (
                    item.price *
                    item.quantity
                ),
            0
        );


    cartTotal.textContent =
        formatPrice(total);

}


/* =====================================================
   ABRIR CARRINHO
===================================================== */

function openCart() {

    cartDrawer.classList.add("active");

    cartOverlay.classList.add("active");

    document.body.classList.add("no-scroll");

}


/* =====================================================
   FECHAR CARRINHO
===================================================== */

function closeCart() {

    cartDrawer.classList.remove("active");

    cartOverlay.classList.remove("active");

    document.body.classList.remove("no-scroll");

}


/* =====================================================
   EVENTOS DO CARRINHO
===================================================== */

cartButton.addEventListener(
    "click",
    openCart
);

cartClose.addEventListener(
    "click",
    closeCart
);

cartOverlay.addEventListener(
    "click",
    closeCart
);


/* =====================================================
   BUSCA
===================================================== */

searchButton.addEventListener(
    "click",
    () => {

        searchOverlay.classList.add("active");

        document.body.classList.add("no-scroll");

        setTimeout(
            () => searchInput.focus(),
            100
        );

    }
);


searchClose.addEventListener(
    "click",
    closeSearch
);


function closeSearch() {

    searchOverlay.classList.remove("active");

    document.body.classList.remove("no-scroll");

    searchInput.value = "";

    renderProducts();

}


/* =====================================================
   BUSCAR PRODUTOS
===================================================== */

searchInput.addEventListener(
    "input",
    event => {

        const search =
            event.target.value
                .toLowerCase()
                .trim();


        if (!search) {

            renderProducts();

            return;

        }


        const filtered =
            products.filter(product => {

                return (

                    product.name
                        .toLowerCase()
                        .includes(search)

                    ||

                    product.category
                        .toLowerCase()
                        .includes(search)

                    ||

                    product.description
                        .toLowerCase()
                        .includes(search)

                );

            });


        renderProducts(filtered);

    }
);


/* =====================================================
   ESC
===================================================== */

document.addEventListener(
    "keydown",
    event => {

        if (event.key !== "Escape") {
            return;
        }


        closeCart();

        closeSearch();

    }
);


/* =====================================================
   NEWSLETTER
===================================================== */

newsletterForm.addEventListener(
    "submit",
    event => {

        event.preventDefault();


        const input =
            newsletterForm.querySelector(
                "input"
            );


        if (!input.value) {
            return;
        }


        alert(
            "Obrigado! Você foi inscrito na NEXUS."
        );


        input.value = "";

    }
);


/* =====================================================
   INICIALIZAÇÃO
===================================================== */

renderProducts();

updateCart();
