<?php
namespace App\View;

use App\Util\Csrf;

//Views das páginas de navegação aberta (catálogo, sobre e contato).
class paginaView{

    //Catálogo público: vitrine de filmes em cards, com modal "Ver mais" (sem precisar de login).
    //$logado indica se deve mostrar o botão de locar dentro do modal.
    public static function catalogo(array $filmes, bool $logado = false): void { ?>
        <section class="home">
            <h1>Catálogo de Filmes</h1>
            <p>Confira nosso acervo. Faça login para alugar.</p>
            <?php if (empty($filmes)): ?>
                <p>Nenhum filme cadastrado ainda.</p>
            <?php else: ?>
            <div class="grid-filmes">
                <?php foreach($filmes as $filme): ?>
                <article class="card">
                    <div class="card-capa">
                        <?php if($filme->getImagem()): ?>
                            <img src="./assets/uploads/<?= $filme->getImagem() ?>" alt="<?= $filme->getTitulo() ?>">
                        <?php else: ?>
                            <div class="sem-capa">Sem imagem</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-info">
                        <h3><?= $filme->getTitulo() ?></h3>
                        <span class="genero"><?= $filme->getGenero() ?></span>
                        <button type="button" class="btn-ver-mais"
                            data-id="<?= $filme->getId() ?>"
                            data-titulo="<?= $filme->getTitulo() ?>"
                            data-genero="<?= $filme->getGenero() ?>"
                            data-ano="<?= $filme->getAno() ?>"
                            data-duracao="<?= $filme->getDuracao() ?> min"
                            data-idade="<?= $filme->getIdadeRecomendada() === "Livre" ? "Livre" : $filme->getIdadeRecomendada() . " anos" ?>"
                            data-valor="R$ <?= number_format($filme->getValorLocacao(), 2, ",", ".") ?>"
                            data-imagem="<?= $filme->getImagem() ? "./assets/uploads/" . $filme->getImagem() : "" ?>">
                            Saber mais
                        </button>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- Modal reutilizado por todos os cards; preenchido dinamicamente via JS. -->
        <div id="modalFilme" class="modal">
            <div class="modal-conteudo">
                <span class="modal-fechar">&times;</span>
                <img id="modalImagem" src="" alt="" style="display:none;">
                <h2 id="modalTitulo"></h2>
                <p><strong>Gênero:</strong> <span id="modalGenero"></span></p>
                <p><strong>Ano:</strong> <span id="modalAno"></span></p>
                <p><strong>Duração:</strong> <span id="modalDuracao"></span></p>
                <p><strong>Idade recomendada:</strong> <span id="modalIdade"></span></p>
                <p><strong>Valor da Locação:</strong> <span id="modalValor"></span></p>

                <?php if ($logado): ?>
                    <!-- Botão de locar: envia o id do filme (preenchido pelo JS) via POST com CSRF. -->
                    <form action="?p=locar" method="post">
                        <?= Csrf::campo() ?>
                        <input type="hidden" name="filme_id" id="modalFilmeId" value="">
                        <button type="submit" class="btn-locar">Locar</button>
                    </form>
                <?php else: ?>
                    <p class="locar-aviso"><a href="?p=login">Entre na sua conta</a> para locar este filme.</p>
                <?php endif; ?>
            </div>
        </div>

        <script src="./assets/modal.js"></script>
        <?php
    }

    public static function sobre(): void { ?>
        <section class="conteudo">
            <h1>Sobre a Locadora</h1>
            <article>
                <p>
                    A <strong>Locadora de Filmes</strong> nasceu da paixão pelo cinema. Há anos
                    reunimos um acervo variado, do clássico ao lançamento, para que você encontre
                    sempre o filme certo para cada momento.
                </p>
                <h2>Nossa missão</h2>
                <p>
                    Levar entretenimento de qualidade até você, com preços justos e um atendimento
                    próximo. Acreditamos que uma boa história tem o poder de transformar a noite de
                    qualquer pessoa.
                </p>
                <h2>Por que escolher a gente?</h2>
                <ul>
                    <li>Acervo organizado por categorias.</li>
                    <li>Valores de locação acessíveis.</li>
                    <li>Cadastro rápido e seguro.</li>
                </ul>
            </article>
        </section>
        <?php
    }

    public static function contato(?string $msg, ?string $sucesso): void {
        if($sucesso !== null): ?>
        <div class="sucesso">
            <?= $sucesso ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php elseif($msg !== null): ?>
        <div class="erro">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section class="auth">
            <h1>Fale Conosco</h1>
            <p>Tem alguma dúvida ou sugestão? Envie sua mensagem.</p>
            <form action="?p=contato" method="post">
                <?= Csrf::campo() ?>
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>

                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>

                <label for="mensagem">Mensagem:</label>
                <textarea name="mensagem" id="mensagem" rows="4" required></textarea>

                <button type="submit">Enviar</button>
            </form>
        </section>
        <?php
    }
}
