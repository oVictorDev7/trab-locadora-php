<?php
namespace App\View;

use App\Model\Categoria;
use App\Util\Csrf;

class categoriaView{

    public static function listar(array $categorias, ?int $deletar = null): void {
        if ($deletar !== null): ?>
        <div class="alert">
            Você deseja realmente deletar esta categoria?
            <a href="?p=catDel&deletar=<?= $deletar ?>">Confirmar</a> |
            <a href="?p=categorias">Cancelar</a>
            <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
        <?php endif; ?>
        <section>
            <h1>Categorias</h1>
            <p><a href="?p=catCad" class="btn-add">+ Nova categoria</a></p>
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Alterar</th>
                        <th>Deletar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria->getId() ?></td>
                        <td><?= $categoria->getNome() ?></td>
                        <td><?= $categoria->getDescricao() ?? "&mdash;" ?></td>
                        <td><a href="?p=catAlt&alt=<?= $categoria->getId() ?>">Alterar</a></td>
                        <td><a href="?p=catDel&del=<?= $categoria->getId() ?>">Deletar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }

    public static function formulario(?string $msg, ?Categoria $categoria = null): void {
        if($msg !== null): ?>
        <div class="alert">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section>
            <h1><?= isset($categoria) ? "Editar categoria" : "Nova categoria" ?></h1>
            <form action="<?= isset($categoria) ? "?p=catAlt" : "?p=catCad" ?>" method="post">
                <?= Csrf::campo() ?>
                <?php if(isset($categoria)): ?>
                    <label for="id">Id</label>
                    <input type="text" name="id" id="id" value="<?= $categoria->getId() ?>" readonly>
                <?php endif; ?>

                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= isset($categoria) ? $categoria->getNome() : "" ?>" required>

                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" rows="3"><?= isset($categoria) ? $categoria->getDescricao() : "" ?></textarea>

                <button type="submit"><?= isset($categoria) ? "Editar" : "Salvar" ?></button>
            </form>
        </section>
        <?php
    }
}
