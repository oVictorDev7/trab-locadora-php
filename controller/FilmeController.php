<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Util\Auth;
use App\Util\Csrf;
use App\Model\Filme;
use App\Dal\FilmeDao;
use App\Dal\CategoriaDao;
use App\View\filmeView;
use Exception;

class FilmeController{
    public static ?string $msg = null;

    public static function cadastrar(): void {
        Auth::exigirAdmin();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["titulo"])) {
            $titulo  = Util::prepararTexto($_POST["titulo"]);
            $genero  = Util::prepararTexto($_POST["genero"] ?? "");
            $ano     = Util::prepararTexto($_POST["ano"] ?? "");
            $valor   = Util::prepararTexto($_POST["valor_locacao"] ?? "");
            $duracao = Util::prepararTexto($_POST["duracao"] ?? "");
            $idade   = Util::prepararTexto($_POST["idade_recomendada"] ?? "");

            try{
                Csrf::validar();
                $imagem = Util::salvarImagem($_FILES["imagem"] ?? null);
                $filme = Filme::criar(null, $titulo, $genero, (int) $ano, (float) $valor, $duracao, $idade, $imagem);
                FilmeDao::cadastrar($filme);
                header("Location: ?p=list");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        filmeView::formulario(self::$msg, null, CategoriaDao::listar());
    }

    public static function editar() : void {
        Auth::exigirAdmin();

        $filme = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id      = (int) Util::prepararTexto($_POST["id"]);
            $titulo  = Util::prepararTexto($_POST["titulo"]);
            $genero  = Util::prepararTexto($_POST["genero"] ?? "");
            $ano     = Util::prepararTexto($_POST["ano"] ?? "");
            $valor   = Util::prepararTexto($_POST["valor_locacao"] ?? "");
            $duracao = Util::prepararTexto($_POST["duracao"] ?? "");
            $idade   = Util::prepararTexto($_POST["idade_recomendada"] ?? "");

            try{
                Csrf::validar();
                $atual = FilmeDao::buscarPorId($id);
                $imagem = $atual?->getImagem();

                $nova = Util::salvarImagem($_FILES["imagem"] ?? null);
                if ($nova !== null) {
                    Util::removerImagem($imagem);
                    $imagem = $nova;
                }

                $filme = Filme::criar($id, $titulo, $genero, (int) $ano, (float) $valor, $duracao, $idade, $imagem);
                FilmeDao::editar($filme);
                header("Location: ?p=list");
                exit;
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }

        if (isset($_GET["alt"])) {
            $filme = FilmeDao::buscarPorId((int) $_GET["alt"]);
        }

        filmeView::formulario(self::$msg, $filme, CategoriaDao::listar());
    }

    public static function listar(?int $deletar = null) : void {
        Auth::exigirAdmin();
        $filmes = FilmeDao::listar();
        filmeView::listar($filmes, $deletar);
    }

    public static function deletar() : void {
        Auth::exigirAdmin();

        if (isset($_GET["del"])) {
            self::listar((int)$_GET["del"]);
        }
        if (isset($_GET["deletar"])) {
            $id = (int)$_GET["deletar"];
            $filme = FilmeDao::buscarPorId($id);
            if ($filme !== null) {
                Util::removerImagem($filme->getImagem());
            }
            FilmeDao::excluir($id);
            header("Location: ?p=list");
            exit;
        }
    }
}
