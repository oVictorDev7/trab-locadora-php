<?php
namespace App\Util;

class Functions{
    static function prepararTexto(string $texto): string {
        return trim(htmlentities($texto));
    }
}
