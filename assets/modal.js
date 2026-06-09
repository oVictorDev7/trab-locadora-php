// Controla a abertura/fechamento do modal "Saber mais" na home/catálogo.
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
    // Campo oculto do formulário de locar (só existe quando o usuário está logado).
    const filmeId = document.getElementById("modalFilmeId");
    const fechar = modal.querySelector(".modal-fechar");

    // Cada botão "Saber mais" carrega os dados do filme a partir dos data-attributes.
    document.querySelectorAll(".btn-ver-mais").forEach(function (botao) {
        botao.addEventListener("click", function () {
            titulo.textContent = botao.dataset.titulo;
            genero.textContent = botao.dataset.genero;
            ano.textContent = botao.dataset.ano;
            duracao.textContent = botao.dataset.duracao;
            idade.textContent = botao.dataset.idade;
            valor.textContent = botao.dataset.valor;

            // Informa ao formulário de locação qual filme será alugado.
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

    // Fecha clicando no X ou fora do conteúdo.
    fechar.addEventListener("click", function () {
        modal.style.display = "none";
    });
    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
})();
