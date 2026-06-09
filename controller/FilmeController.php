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
        //Só admin pode cadastrar. Redireciona quem não tem permissão.
        Auth::exigirAdmin();

        //Verifica se o formulário foi submetido via POST.
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["titulo"])) {
            //Sanitiza os campos de texto individualmente (a imagem vem em $_FILES, não em $_POST).
            $titulo  = Util::prepararTexto($_POST["titulo"]);
            $genero  = Util::prepararTexto($_POST["genero"] ?? "");
            $ano     = Util::prepararTexto($_POST["ano"] ?? "");
            $valor   = Util::prepararTexto($_POST["valor_locacao"] ?? "");
            $duracao = Util::prepararTexto($_POST["duracao"] ?? "");
            $idade   = Util::prepararTexto($_POST["idade_recomendada"] ?? "");

            try{
                //Valida o token CSRF antes de qualquer escrita.
                Csrf::validar();
                //Salva a imagem enviada (se houver) e guarda o nome do arquivo.
                $imagem = Util::salvarImagem($_FILES["imagem"] ?? null);
                //Cria o filme via factory (que valida todos os campos). ID null porque será gerado pelo banco.
                $filme = Filme::criar(null, $titulo, $genero, (int) $ano, (float) $valor, $duracao, $idade, $imagem);
                FilmeDao::cadastrar($filme);
                //Redireciona após sucesso.
                header("Location: ?p=list");
                exit;

            }catch(Exception $e){
                //Guarda a mensagem de erro para exibir na view.
                self::$msg = $e->getMessage();
            }
        }
        //Sempre exibe o formulário (com erro, se houver). As categorias alimentam o select de gênero.
        filmeView::formulario(self::$msg, null, CategoriaDao::listar());
    }

    public static function editar() : void {
        Auth::exigirAdmin();

        $filme = null;

        //Se o formulário de edição foi submetido via POST, processa o UPDATE.
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
            $id      = (int) Util::prepararTexto($_POST["id"]);
            $titulo  = Util::prepararTexto($_POST["titulo"]);
            $genero  = Util::prepararTexto($_POST["genero"] ?? "");
            $ano     = Util::prepararTexto($_POST["ano"] ?? "");
            $valor   = Util::prepararTexto($_POST["valor_locacao"] ?? "");
            $duracao = Util::prepararTexto($_POST["duracao"] ?? "");
            $idade   = Util::prepararTexto($_POST["idade_recomendada"] ?? "");

            try{
                //Valida o token CSRF antes de qualquer escrita.
                Csrf::validar();
                //Recupera o filme atual para saber a imagem que já está salva.
                $atual = FilmeDao::buscarPorId($id);
                $imagem = $atual?->getImagem();

                //Se uma nova imagem foi enviada, salva e descarta a antiga.
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

        //Carrega o filme pelo id da URL para preencher o formulário.
        if (isset($_GET["alt"])) {
            $filme = FilmeDao::buscarPorId((int) $_GET["alt"]);
        }

        filmeView::formulario(self::$msg, $filme, CategoriaDao::listar());
    }

    public static function listar(?int $deletar = null) : void {
        //Listagem administrativa exige admin.
        Auth::exigirAdmin();
        $filmes = FilmeDao::listar();
        filmeView::listar($filmes, $deletar);
    }

    public static function deletar() : void {
        Auth::exigirAdmin();

        //Mostra a confirmação antes de excluir.
        if (isset($_GET["del"])) {
            self::listar((int)$_GET["del"]);
        }
        //Confirmado: exclui e volta para a lista.
        if (isset($_GET["deletar"])) {
            $id = (int)$_GET["deletar"];
            //Remove também o arquivo de imagem associado, se existir.
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
