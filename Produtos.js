function adicionarAoCarrinho(nome, preco) {
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho.push({ nome: nome, preco: preco });
    localStorage.setItem('carrinho', JSON.stringify(carrinho));

    // Redireciona para a página do carrinho
    window.location.href = "Finalizar.html";
}

// Filtrar produtos por categoria
const categorias = document.querySelectorAll('.categorias li');
const produtos = document.querySelectorAll('.produto');

categorias.forEach(cat => {
    cat.addEventListener('click', () => {
        categorias.forEach(c => c.classList.remove('active'));
        cat.classList.add('active');
        const categoriaSelecionada = cat.getAttribute('data-category');

        produtos.forEach(prod => {
            if(categoriaSelecionada === 'all' || prod.getAttribute('data-category') === categoriaSelecionada) {
                prod.style.display = 'block';
            } else {
                prod.style.display = 'none';
            }
        });
    });
});
