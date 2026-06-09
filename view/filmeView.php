<?php
namespace App\View;

use App\Model\Filme;
use App\Util\Csrf;

class filmeView{

    //Exibe a lista de filmes em uma tabela HTML.
    public static function listar(array $filmes, ?int $deletar = null): void {
        if ($deletar !== null): ?>
        <div class="alert">
            Você deseja realmente deletar?
            <a href="?p=deletar&deletar=<?= $deletar?>">Confirmar</a> |
            <a href="?p=list">Cancelar</a>
            <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
        <?php endif; ?>
        <h1>Filmes</h1>
        <p><a href="?p=cad" class="btn-add">+ Novo filme</a></p>
        <table>
            <thead>
                <tr>
                    <th><a href="#">Id</a></th>
                    <th>Capa</th>
                    <th><a href="#">Título</a></th>
                    <th><a href="#">Gênero</a></th>
                    <th><a href="#">Ano</a></th>
                    <th>Duração</th>
                    <th>Idade</th>
                    <th><a href="#">Valor Locação</a></th>
                    <th>Alterar</th>
                    <th>Deletar</th>
                </tr>
            </thead>
            <tbody>
                <!-- Itera sobre os filmes e exibe cada um numa linha, acessando os getters. -->
                <?php foreach($filmes as $filme): ?>
                <tr>
                    <td><?= $filme->getId()?></td>
                    <td>
                        <?php if($filme->getImagem()): ?>
                            <img class="capa-mini" src="./assets/uploads/<?= $filme->getImagem() ?>" alt="<?= $filme->getTitulo() ?>">
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                    <td><?= $filme->getTitulo()?></td>
                    <td><?= $filme->getGenero()?></td>
                    <td><?= $filme->getAno()?></td>
                    <td><?= $filme->getDuracao()?> min</td>
                    <td><?= $filme->getIdadeRecomendada()?></td>
                    <td>R$ <?= number_format($filme->getValorLocacao(), 2, ",", ".") ?></td>
                    <td><a href="?p=alt&alt=<?= $filme->getId() ?>">Alterar</a></td>
                    <td><a href="?p=deletar&del=<?= $filme->getId() ?>">Deletar</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
    }

    //Exibe o formulário de cadastro/edição. Recebe a mensagem de erro, opcionalmente o filme a editar e a lista de categorias para o select.
    public static function formulario(?string $msg, ?Filme $filme = null, array $categorias = []): void {
        if($msg !== null): ?>
        <div class="alert">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
<?php endif; ?>
    <!-- enctype obrigatório para enviar arquivos (a imagem do filme). -->
    <form action="<?= isset($filme)? "?p=alt": "?p=cad" ?>" method="post" enctype="multipart/form-data">
    <?= Csrf::campo() ?>
    <?php if(isset($filme)): ?>
        <label for="id">Id</label>
        <input type="text" name="id" id="id" value="<?= $filme->getId()?>" readonly>
    <?php endif; ?>
    <label>Título:</label>
    <input type="text" name="titulo" value="<?= isset($filme)? $filme->getTitulo() : "" ?>">

    <label>Gênero:</label>
    <?php if (empty($categorias)): ?>
        <p class="aviso-categoria">Nenhuma categoria cadastrada. <a href="?p=catCad">Cadastre uma categoria</a> antes de adicionar filmes.</p>
    <?php else: ?>
    <select name="genero" required>
        <option value="">-- Selecione uma categoria --</option>
        <?php foreach($categorias as $categoria): ?>
            <option value="<?= $categoria->getNome() ?>" <?= (isset($filme) && $filme->getGenero() === $categoria->getNome()) ? "selected" : "" ?>>
                <?= $categoria->getNome() ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>

    <label>Ano:</label>
    <input type="number" name="ano" value="<?= isset($filme)? $filme->getAno() : "" ?>">

    <label>Duração (minutos):</label>
    <input type="number" name="duracao" min="1" value="<?= isset($filme)? $filme->getDuracao() : "" ?>">

    <label>Idade recomendada:</label>
    <select name="idade_recomendada" required>
        <option value="">-- Selecione --</option>
        <?php foreach(Filme::IDADES as $idade): ?>
            <option value="<?= $idade ?>" <?= (isset($filme) && $filme->getIdadeRecomendada() === $idade) ? "selected" : "" ?>>
                <?= $idade === "Livre" ? "Livre" : $idade . " anos" ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Valor Locação:</label>
    <input type="number" step="0.01" name="valor_locacao" value="<?= isset($filme)? $filme->getValorLocacao() : "" ?>">

    <label>Imagem (capa):</label>
    <input type="file" name="imagem" accept="image/*">
    <?php if(isset($filme) && $filme->getImagem()): ?>
        <div class="preview-atual">
            <small>Imagem atual (envie outra para substituir):</small><br>
            <img class="capa-mini" src="./assets/uploads/<?= $filme->getImagem() ?>" alt="capa atual">
        </div>
    <?php endif; ?>

    <button type="submit" name="enviaForm">
    <?= isset($filme)? "Editar": "Salvar" ?>
    </button>
</form>
        <?php
    }
}
