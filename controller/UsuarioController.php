<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Util\Auth;
use App\Util\Csrf;
use App\Model\Usuario;
use App\Dal\UsuarioDao;
use App\View\usuarioView;
use Exception;

class UsuarioController{
    public static ?string $msg = null;
    public static ?string $sucesso = null;

    //Nome do cookie usado para lembrar o e-mail no login.
    const COOKIE_EMAIL = "email_lembrado";

    public static function login(): void {
        //Quem já está logado não precisa ver a tela de login.
        if (Auth::estaLogado()) {
            header("Location: ?p=catalogo");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
            $email = Util::prepararTexto($_POST["email"]);
            //A senha não passa por prepararTexto para não alterar os caracteres digitados.
            $senha = $_POST["senha"] ?? "";

            try{
                Csrf::validar();

                $usuario = UsuarioDao::buscarPorEmail($email);
                //password_verify compara a senha digitada com o hash salvo no banco.
                if ($usuario === null || !password_verify($senha, $usuario->getSenha())) {
                    throw new Exception("E-mail ou senha inválidos");
                }

                //COOKIE: se o usuário marcou "lembrar", guarda o e-mail por 30 dias; senão, apaga o cookie.
                if (isset($_POST["lembrar"])) {
                    setcookie(self::COOKIE_EMAIL, $email, time() + 60 * 60 * 24 * 30, "/");
                } else {
                    setcookie(self::COOKIE_EMAIL, "", time() - 3600, "/");
                }

                Auth::login($usuario);
                header("Location: ?p=catalogo");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        usuarioView::login(self::$msg);
    }

    public static function registrar(): void {
        if (Auth::estaLogado()) {
            header("Location: ?p=catalogo");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nome"])) {
            $nome       = Util::prepararTexto($_POST["nome"]);
            $email      = Util::prepararTexto($_POST["email"]);
            $cpf        = Util::prepararTexto($_POST["cpf"] ?? "");
            $nascimento = Util::prepararTexto($_POST["nascimento"] ?? "");
            $senha      = $_POST["senha"] ?? "";

            try{
                Csrf::validar();

                if (trim($senha) === "") {
                    throw new Exception("A senha é obrigatória");
                }
                //Cadastro público sempre cria usuário comum. Admin é definido direto no banco.
                //Guarda o hash da senha, nunca o texto puro.
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $usuario = Usuario::criar(null, $nome, $email, $hash, $cpf, $nascimento, "usuario");
                UsuarioDao::cadastrar($usuario);

                //Loga automaticamente após o cadastro.
                Auth::login($usuario);
                header("Location: ?p=catalogo");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        usuarioView::registro(self::$msg);
    }

    //Recuperação de senha validando CPF + data de nascimento.
    public static function recuperar(): void {
        if (Auth::estaLogado()) {
            header("Location: ?p=catalogo");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cpf"])) {
            $cpf        = preg_replace("/\D/", "", $_POST["cpf"]);
            $nascimento = Util::prepararTexto($_POST["nascimento"] ?? "");
            $novaSenha  = $_POST["nova_senha"] ?? "";
            $confirmar  = $_POST["confirmar_senha"] ?? "";

            try{
                Csrf::validar();

                if (trim($novaSenha) === "") {
                    throw new Exception("Informe a nova senha");
                }
                if ($novaSenha !== $confirmar) {
                    throw new Exception("As senhas não conferem");
                }

                //Só redefine se o par CPF + nascimento existir no banco.
                $usuario = UsuarioDao::buscarPorCpfNascimento($cpf, $nascimento);
                if ($usuario === null) {
                    throw new Exception("CPF ou data de nascimento não conferem");
                }

                $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
                UsuarioDao::redefinirSenha($usuario->getId(), $hash);

                self::$sucesso = "Senha redefinida com sucesso! Você já pode entrar.";

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        usuarioView::recuperar(self::$msg, self::$sucesso);
    }

    //CRUD admin: lista todos os usuários.
    public static function listar(?int $deletar = null): void {
        Auth::exigirAdmin();
        $usuarios = UsuarioDao::listar();
        usuarioView::listar($usuarios, $deletar);
    }

    //CRUD admin: edita nome, e-mail, CPF, nascimento e tipo de um usuário.
    public static function editar(): void {
        Auth::exigirAdmin();

        $usuario = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            try{
                Csrf::validar();

                $id         = (int) Util::prepararTexto($_POST["id"]);
                $nome       = Util::prepararTexto($_POST["nome"]);
                $email      = Util::prepararTexto($_POST["email"]);
                $cpf        = Util::prepararTexto($_POST["cpf"] ?? "");
                $nascimento = Util::prepararTexto($_POST["nascimento"] ?? "");
                $tipo       = Util::prepararTexto($_POST["tipo"] ?? "usuario");

                //A senha não é alterada aqui; reaproveita o hash atual para passar pela factory.
                $atual = UsuarioDao::buscarPorId($id);
                if ($atual === null) {
                    throw new Exception("Usuário não encontrado");
                }

                $usuario = Usuario::criar($id, $nome, $email, $atual->getSenha(), $cpf, $nascimento, $tipo);
                UsuarioDao::editar($usuario);
                header("Location: ?p=usuarios");
                exit;
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }

        if (isset($_GET["alt"])) {
            $usuario = UsuarioDao::buscarPorId((int) $_GET["alt"]);
        }

        usuarioView::formulario(self::$msg, $usuario);
    }

    //CRUD admin: exclui um usuário.
    public static function deletar(): void {
        Auth::exigirAdmin();

        if (isset($_GET["del"])) {
            self::listar((int) $_GET["del"]);
        }
        if (isset($_GET["deletar"])) {
            $id = (int) $_GET["deletar"];
            //Impede que o admin logado apague a própria conta.
            $logado = Auth::usuario();
            if ($logado !== null && (int) $logado["id"] === $id) {
                self::$msg = "Você não pode excluir o próprio usuário";
                self::listar();
                return;
            }
            try{
                UsuarioDao::excluir($id);
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
            header("Location: ?p=usuarios");
            exit;
        }
    }

    public static function logout(): void {
        Auth::logout();
        header("Location: ?p=login");
        exit;
    }
}
