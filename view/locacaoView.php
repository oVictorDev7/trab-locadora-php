<?php
namespace App\View;

use App\Model\Locacao;

class locacaoView{

    //"Minhas locações": o que o usuário logado já alugou.
    public static function minhas(array $locacoes, bool $sucesso = false, ?string $msg = null): void {
        if ($sucesso): ?>
        <div class="sucesso">
            Filme locado com sucesso!
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php elseif ($msg !== null): ?>
        <div class="erro">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section>
            <h1>Minhas Locações</h1>
            <?php if (empty($locacoes)): ?>
                <p>Você ainda não locou nenhum filme. Visite o <a href="?p=catalogo">catálogo</a>.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Filme</th>
                        <th>Data da Locação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($locacoes as $locacao): ?>
                    <tr>
                        <td><?= $locacao->getFilmeTitulo() ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($locacao->getDataLocacao())) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
        <?php
    }

    //Listagem para o admin: todas as locações, mostrando quem alugou qual filme.
    public static function listar(array $locacoes): void { ?>
        <section>
            <h1>Locações</h1>
            <?php if (empty($locacoes)): ?>
                <p>Nenhuma locação registrada ainda.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Usuário</th>
                        <th>Filme</th>
                        <th>Data da Locação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($locacoes as $locacao): ?>
                    <tr>
                        <td><?= $locacao->getId() ?></td>
                        <td><?= $locacao->getUsuarioNome() ?></td>
                        <td><?= $locacao->getFilmeTitulo() ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($locacao->getDataLocacao())) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
        <?php
    }
}
