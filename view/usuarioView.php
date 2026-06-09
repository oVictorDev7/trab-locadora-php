<?php
namespace App\View;

use App\Model\Usuario;
use App\Util\Csrf;
use App\Controller\UsuarioController;

class usuarioView{

    public static function login(?string $msg): void {
        $emailLembrado = $_COOKIE[UsuarioController::COOKIE_EMAIL] ?? "";
        if($msg !== null): ?>
        <div class="erro">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section class="auth">
            <h1>Entrar</h1>
            <form action="?p=login" method="post">
                <?= Csrf::campo() ?>
                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" value="<?= $emailLembrado ?>" required>

                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>

                <label class="check">
                    <input type="checkbox" name="lembrar" <?= $emailLembrado !== "" ? "checked" : "" ?>>
                    Lembrar meu e-mail
                </label>

                <button type="submit">Entrar</button>
            </form>
            <p class="auth-link">Não tem conta? <a href="?p=registro">Cadastre-se</a></p>
            <p class="auth-link">Esqueceu a senha? <a href="?p=recuperar">Recuperar</a></p>
        </section>
        <?php
    }

    public static function registro(?string $msg): void {
        if($msg !== null): ?>
        <div class="erro">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section class="auth">
            <h1>Criar conta</h1>
            <form action="?p=registro" method="post">
                <?= Csrf::campo() ?>
                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" required>

                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" required>

                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="Somente números" required>

                <label for="nascimento">Data de nascimento:</label>
                <input type="date" name="nascimento" id="nascimento" required>

                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>

                <button type="submit">Cadastrar</button>
            </form>
            <p class="auth-link">Já tem conta? <a href="?p=login">Entrar</a></p>
        </section>
        <?php
    }

    public static function recuperar(?string $msg, ?string $sucesso): void {
        if($sucesso !== null): ?>
        <div class="sucesso">
            <?= $sucesso ?> <a href="?p=login">Entrar agora</a>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php elseif($msg !== null): ?>
        <div class="erro">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section class="auth">
            <h1>Recuperar senha</h1>
            <p>Confirme seus dados para definir uma nova senha.</p>
            <form action="?p=recuperar" method="post">
                <?= Csrf::campo() ?>
                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="Somente números" required>

                <label for="nascimento">Data de nascimento:</label>
                <input type="date" name="nascimento" id="nascimento" required>

                <label for="nova_senha">Nova senha:</label>
                <input type="password" name="nova_senha" id="nova_senha" required>

                <label for="confirmar_senha">Confirmar nova senha:</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" required>

                <button type="submit">Redefinir senha</button>
            </form>
            <p class="auth-link">Lembrou a senha? <a href="?p=login">Entrar</a></p>
        </section>
        <?php
    }

    public static function listar(array $usuarios, ?int $deletar = null): void {
        if ($deletar !== null): ?>
        <div class="alert">
            Você deseja realmente deletar este usuário?
            <a href="?p=userDel&deletar=<?= $deletar ?>">Confirmar</a> |
            <a href="?p=usuarios">Cancelar</a>
            <span class="close" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
        <?php endif; ?>
        <section>
            <h1>Usuários</h1>
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>CPF</th>
                        <th>Nascimento</th>
                        <th>Tipo</th>
                        <th>Alterar</th>
                        <th>Deletar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario->getId() ?></td>
                        <td><?= $usuario->getNome() ?></td>
                        <td><?= $usuario->getEmail() ?></td>
                        <td><?= $usuario->getCpf() ?></td>
                        <td><?= $usuario->getNascimento() ?></td>
                        <td><?= $usuario->getTipo() ?></td>
                        <td><a href="?p=userAlt&alt=<?= $usuario->getId() ?>">Alterar</a></td>
                        <td><a href="?p=userDel&del=<?= $usuario->getId() ?>">Deletar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php
    }

    public static function formulario(?string $msg, ?Usuario $usuario = null): void {
        if($msg !== null): ?>
        <div class="alert">
            <?= $msg ?>
            <span class="close" onclick="this.parentElement.style.display='none'">&times;</span>
        </div>
        <?php endif; ?>
        <section class="auth">
            <h1>Editar usuário</h1>
            <form action="?p=userAlt" method="post">
                <?= Csrf::campo() ?>
                <label for="id">Id</label>
                <input type="text" name="id" id="id" value="<?= $usuario ? $usuario->getId() : "" ?>" readonly>

                <label for="nome">Nome:</label>
                <input type="text" name="nome" id="nome" value="<?= $usuario ? $usuario->getNome() : "" ?>" required>

                <label for="email">E-mail:</label>
                <input type="email" name="email" id="email" value="<?= $usuario ? $usuario->getEmail() : "" ?>" required>

                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" maxlength="14" value="<?= $usuario ? $usuario->getCpf() : "" ?>" required>

                <label for="nascimento">Data de nascimento:</label>
                <input type="date" name="nascimento" id="nascimento" value="<?= $usuario ? $usuario->getNascimento() : "" ?>" required>

                <label for="tipo">Tipo:</label>
                <select name="tipo" id="tipo">
                    <option value="usuario" <?= $usuario && $usuario->getTipo() === "usuario" ? "selected" : "" ?>>usuario</option>
                    <option value="admin" <?= $usuario && $usuario->getTipo() === "admin" ? "selected" : "" ?>>admin</option>
                </select>

                <button type="submit">Salvar</button>
            </form>
        </section>
        <?php
    }
}
