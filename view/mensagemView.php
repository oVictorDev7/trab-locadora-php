<?php
namespace App\View;

use App\Model\Mensagem;

class mensagemView{

    public static function listar(array $mensagens, ?int $deletar = null): void {
        if ($deletar !== null): ?>
        <div class="alert">
            Você deseja realmente deletar esta mensagem?
            <a href="?p=msgDel&deletar=<?= $deletar ?>">Confirmar</a> |
            <a href="?p=mensagens">Cancelar</a>
            <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
        <?php endif; ?>
        <section>
            <h1>Mensagens de Contato</h1>
            <?php if (empty($mensagens)): ?>
                <p>Nenhuma mensagem recebida ainda.</p>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Data</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Mensagem</th>
                        <th>Deletar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mensagens as $mensagem): ?>
                    <tr>
                        <td><?= $mensagem->getId() ?></td>
                        <td><?= date("d/m/Y H:i", strtotime($mensagem->getDataEnvio())) ?></td>
                        <td><?= $mensagem->getNome() ?></td>
                        <td><?= $mensagem->getEmail() ?></td>
                        <td><?= nl2br($mensagem->getMensagem()) ?></td>
                        <td><a href="?p=msgDel&del=<?= $mensagem->getId() ?>">Deletar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>
        <?php
    }
}
