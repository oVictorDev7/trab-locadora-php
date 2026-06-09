<?php
namespace App\Util;

class Csrf{

    public static function token(): string {
        Auth::iniciar();
        if (empty($_SESSION["csrf"])) {
            $_SESSION["csrf"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf"];
    }

    public static function campo(): string {
        return '<input type="hidden" name="csrf" value="' . self::token() . '">';
    }

    public static function validar(): void {
        Auth::iniciar();
        $enviado = $_POST["csrf"] ?? "";
        if (empty($_SESSION["csrf"]) || !hash_equals($_SESSION["csrf"], $enviado)) {
            throw new \RuntimeException("Falha na verificação de segurança (CSRF). Recarregue a página e tente novamente.");
        }
    }
}
