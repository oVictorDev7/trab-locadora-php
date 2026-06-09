<?php
namespace App\Util;

class Functions{
    //Pasta onde as capas dos filmes são salvas.
    const PASTA_UPLOAD = __DIR__ . "/../assets/uploads/";
    //Tipos de imagem aceitos no upload.
    const TIPOS_PERMITIDOS = ["image/jpeg", "image/png", "image/webp", "image/gif"];

    static function prepararTexto(string $texto): string {
        return trim(htmlentities($texto));
    }

    //Recebe um item de $_FILES e salva a imagem em assets/uploads, retornando o nome do arquivo gerado.
    //Retorna null quando nenhum arquivo foi enviado. Lança exceção em caso de erro de validação.
    static function salvarImagem(?array $arquivo): ?string {
        //Nenhum arquivo enviado (campo opcional).
        if ($arquivo === null || !isset($arquivo["tmp_name"]) || $arquivo["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($arquivo["error"] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Falha ao enviar a imagem");
        }
        //Valida o tipo real do arquivo, não apenas a extensão.
        $tipo = mime_content_type($arquivo["tmp_name"]);
        if (!in_array($tipo, self::TIPOS_PERMITIDOS, true)) {
            throw new \RuntimeException("Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF");
        }

        //Garante que a pasta de upload exista.
        if (!is_dir(self::PASTA_UPLOAD)) {
            mkdir(self::PASTA_UPLOAD, 0777, true);
        }

        //Gera um nome único para evitar colisão e preserva a extensão original.
        $extensao = pathinfo($arquivo["name"], PATHINFO_EXTENSION);
        $nome = uniqid("filme_", true) . "." . strtolower($extensao);

        if (!move_uploaded_file($arquivo["tmp_name"], self::PASTA_UPLOAD . $nome)) {
            throw new \RuntimeException("Não foi possível salvar a imagem");
        }
        return $nome;
    }

    //Remove uma imagem da pasta de uploads (usado ao trocar ou excluir).
    static function removerImagem(?string $nome): void {
        if ($nome === null || $nome === "") return;
        $caminho = self::PASTA_UPLOAD . $nome;
        if (is_file($caminho)) {
            unlink($caminho);
        }
    }
}
