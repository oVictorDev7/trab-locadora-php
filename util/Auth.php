<?php
namespace App\Util;

use App\Model\Usuario;

class Auth{

    public static function iniciar(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

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

    public static function usuario(): ?array {
        self::iniciar();
        return $_SESSION["usuario"] ?? null;
    }

    public static function exigirLogin(): void {
        if (!self::estaLogado()) {
            header("Location: ?p=login");
            exit;
        }
    }

    public static function exigirAdmin(): void {
        self::exigirLogin();
        if (!self::ehAdmin()) {
            header("Location: ?p=catalogo");
            exit;
        }
    }
}
