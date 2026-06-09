<?php
namespace App\Util;

//Proteção contra CSRF (Cross-Site Request Forgery).
//Gera um token único por sessão e valida em toda submissão POST.
class Csrf{

    //Retorna o token da sessão, criando um novo se ainda não existir.
    public static function token(): string {
        Auth::iniciar();
        if (empty($_SESSION["csrf"])) {
            //random_bytes gera bytes aleatórios criptograficamente seguros.
            $_SESSION["csrf"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf"];
    }

    //Devolve o campo hidden pronto para ser inserido dentro dos formulários.
    public static function campo(): string {
        return '<input type="hidden" name="csrf" value="' . self::token() . '">';
    }

    //Compara o token enviado no POST com o guardado na sessão. Lança exceção se não baterem.
    public static function validar(): void {
        Auth::iniciar();
        $enviado = $_POST["csrf"] ?? "";
        //hash_equals evita ataques de timing na comparação das strings.
        if (empty($_SESSION["csrf"]) || !hash_equals($_SESSION["csrf"], $enviado)) {
            throw new \RuntimeException("Falha na verificação de segurança (CSRF). Recarregue a página e tente novamente.");
        }
    }
}
