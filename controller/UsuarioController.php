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

    const COOKIE_EMAIL = "email_lembrado";

    public static function login(): void {
        if (Auth::estaLogado()) {
            header("Location: ?p=catalogo");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
            $email = Util::prepararTexto($_POST["email"]);
            $senha = $_POST["senha"] ?? "";

            try{
                Csrf::validar();

                $usuario = UsuarioDao::buscarPorEmail($email);
                if ($usuario === null || !password_verify($senha, $usuario->getSenha())) {
                    throw new Exception("E-mail ou senha inválidos");
                }

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
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                $usuario = Usuario::criar(null, $nome, $email, $hash, $cpf, $nascimento, "usuario");
                UsuarioDao::cadastrar($usuario);

                Auth::login($usuario);
                header("Location: ?p=catalogo");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        usuarioView::registro(self::$msg);
    }

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

    public static function listar(?int $deletar = null): void {
        Auth::exigirAdmin();
        $usuarios = UsuarioDao::listar();
        usuarioView::listar($usuarios, $deletar);
    }

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

    public static function deletar(): void {
        Auth::exigirAdmin();

        if (isset($_GET["del"])) {
            self::listar((int) $_GET["del"]);
        }
        if (isset($_GET["deletar"])) {
            $id = (int) $_GET["deletar"];
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
