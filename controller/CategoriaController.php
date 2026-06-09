<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Util\Auth;
use App\Util\Csrf;
use App\Model\Categoria;
use App\Dal\CategoriaDao;
use App\View\categoriaView;
use Exception;

class CategoriaController{
    public static ?string $msg = null;

    public static function cadastrar(): void {
        //Só admin gerencia categorias.
        Auth::exigirAdmin();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nome"])) {
            try{
                //Valida o token CSRF antes de qualquer escrita.
                Csrf::validar();

                $nome      = Util::prepararTexto($_POST["nome"]);
                $descricao = Util::prepararTexto($_POST["descricao"] ?? "");

                $categoria = Categoria::criar(null, $nome, $descricao === "" ? null : $descricao);
                CategoriaDao::cadastrar($categoria);
                header("Location: ?p=categorias");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        categoriaView::formulario(self::$msg);
    }

    public static function editar(): void {
        Auth::exigirAdmin();

        $categoria = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            try{
                Csrf::validar();

                $id        = (int) Util::prepararTexto($_POST["id"]);
                $nome      = Util::prepararTexto($_POST["nome"]);
                $descricao = Util::prepararTexto($_POST["descricao"] ?? "");

                $categoria = Categoria::criar($id, $nome, $descricao === "" ? null : $descricao);
                CategoriaDao::editar($categoria);
                header("Location: ?p=categorias");
                exit;
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }

        //Carrega a categoria pelo id da URL para preencher o formulário.
        if (isset($_GET["alt"])) {
            $categoria = CategoriaDao::buscarPorId((int) $_GET["alt"]);
        }

        categoriaView::formulario(self::$msg, $categoria);
    }

    public static function listar(?int $deletar = null): void {
        Auth::exigirAdmin();
        $categorias = CategoriaDao::listar();
        categoriaView::listar($categorias, $deletar);
    }

    public static function deletar(): void {
        Auth::exigirAdmin();

        //Mostra a confirmação antes de excluir.
        if (isset($_GET["del"])) {
            self::listar((int) $_GET["del"]);
        }
        //Confirmado: exclui e volta para a lista.
        if (isset($_GET["deletar"])) {
            try{
                CategoriaDao::excluir((int) $_GET["deletar"]);
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
            header("Location: ?p=categorias");
            exit;
        }
    }
}
