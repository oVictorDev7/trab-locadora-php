<?php
namespace App\Util;

use App\Model\Usuario;

//Centraliza o controle de sessão e de permissões. Toda a checagem de login/admin passa por aqui.
class Auth{

    //Garante que a sessão esteja iniciada antes de qualquer leitura/escrita.
    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    //Guarda na sessão os dados essenciais do usuário logado.
    public static function login(Usuario $usuario): void {
        self::iniciar();
        $_SESSION["usuario"] = [
            "id"    => $usuario->getId(),
            "nome"  => $usuario->getNome(),
            "email" => $usuario->getEmail(),
            "tipo"  => $usuario->getTipo(),
        ];
    }

    public static function logout(): void {
        self::iniciar();
        session_unset();
        session_destroy();
    }

    public static function estaLogado(): bool {
        self::iniciar();
        return isset($_SESSION["usuario"]);
    }

    public static function ehAdmin(): bool {
        self::iniciar();
        return isset($_SESSION["usuario"]) && $_SESSION["usuario"]["tipo"] === "admin";
    }

    //Retorna os dados do usuário logado (ou null).
    public static function usuario(): ?array {
        self::iniciar();
        return $_SESSION["usuario"] ?? null;
    }

    //Se não estiver logado, redireciona para a tela de login.
    public static function exigirLogin(): void {
        if (!self::estaLogado()) {
            header("Location: ?p=login");
            exit;
        }
    }

    //Se não for admin, redireciona para a home. Protege cadastro/edição/exclusão.
    public static function exigirAdmin(): void {
        self::exigirLogin();
        if (!self::ehAdmin()) {
            header("Location: ?p=catalogo");
            exit;
        }
    }
}
