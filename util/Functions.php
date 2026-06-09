<?php
namespace App\Util;

class Functions{
    const PASTA_UPLOAD = __DIR__ . "/../assets/uploads/";
    const TIPOS_PERMITIDOS = ["image/jpeg", "image/png", "image/webp", "image/gif"];

    static function prepararTexto(string $texto): string {
        return trim(htmlentities($texto));
    }

    static function salvarImagem(?array $arquivo): ?string {
        if ($arquivo === null || !isset($arquivo["tmp_name"]) || $arquivo["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($arquivo["error"] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Falha ao enviar a imagem");
        }
        $tipo = mime_content_type($arquivo["tmp_name"]);
        if (!in_array($tipo, self::TIPOS_PERMITIDOS, true)) {
            throw new \RuntimeException("Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF");
        }

        if (!is_dir(self::PASTA_UPLOAD)) {
            mkdir(self::PASTA_UPLOAD, 0777, true);
        }

        $extensao = pathinfo($arquivo["name"], PATHINFO_EXTENSION);
        $nome = uniqid("filme_", true) . "." . strtolower($extensao);

        if (!move_uploaded_file($arquivo["tmp_name"], self::PASTA_UPLOAD . $nome)) {
            throw new \RuntimeException("Não foi possível salvar a imagem");
        }
        return $nome;
    }

    static function removerImagem(?string $nome): void {
        if ($nome === null || $nome === "") return;
        $caminho = self::PASTA_UPLOAD . $nome;
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
}
