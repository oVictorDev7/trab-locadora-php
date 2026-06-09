(function () {
    const modal = document.getElementById("modalFilme");
    if (!modal) return;

    const imagem = document.getElementById("modalImagem");
    const titulo = document.getElementById("modalTitulo");
    const genero = document.getElementById("modalGenero");
    const ano = document.getElementById("modalAno");
    const duracao = document.getElementById("modalDuracao");
    const idade = document.getElementById("modalIdade");
    const valor = document.getElementById("modalValor");
    const filmeId = document.getElementById("modalFilmeId");
    const fechar = modal.querySelector(".modal-fechar");

    document.querySelectorAll(".btn-ver-mais").forEach(function (botao) {
        botao.addEventListener("click", function () {
            titulo.textContent = botao.dataset.titulo;
            genero.textContent = botao.dataset.genero;
            ano.textContent = botao.dataset.ano;
            duracao.textContent = botao.dataset.duracao;
            idade.textContent = botao.dataset.idade;
            valor.textContent = botao.dataset.valor;

            if (filmeId) {
                filmeId.value = botao.dataset.id;
            }

            if (botao.dataset.imagem) {
                imagem.src = botao.dataset.imagem;
                imagem.alt = botao.dataset.titulo;
                imagem.style.display = "block";
            } else {
                imagem.style.display = "none";
            }

            modal.style.display = "flex";
        });
    });

    fechar.addEventListener("click", function () {
        modal.style.display = "none";
    });
    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
})();
