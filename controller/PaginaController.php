<?php
namespace App\Controller;

use App\Util\Functions as Util;
use App\Util\Auth;
use App\Util\Csrf;
use App\Model\Mensagem;
use App\Dal\FilmeDao;
use App\Dal\MensagemDao;
use App\View\paginaView;
use Exception;

//Controla as páginas de navegação aberta (acessíveis sem login): catálogo, sobre e contato.
class PaginaController{
    public static ?string $msg = null;
    public static ?string $sucesso = null;

    //Vitrine pública: lista os filmes em cards, sem exigir login.
    public static function catalogo(): void {
        $filmes = FilmeDao::listar();
        //O catálogo é público, mas só quem está logado pode locar.
        paginaView::catalogo($filmes, Auth::estaLogado());
    }

    public static function sobre(): void {
        paginaView::sobre();
    }

    public static function contato(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nome"])) {
            $nome     = Util::prepararTexto($_POST["nome"]);
            $email    = Util::prepararTexto($_POST["email"] ?? "");
            $mensagem = Util::prepararTexto($_POST["mensagem"] ?? "");

            try{
                Csrf::validar();

                //A validação dos campos fica no factory do model.
                $msg = Mensagem::criar(null, $nome, $email, $mensagem);
                MensagemDao::cadastrar($msg);

                self::$sucesso = "Mensagem enviada com sucesso, " . $msg->getNome() . "! Em breve retornaremos.";

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        paginaView::contato(self::$msg, self::$sucesso);
    }
}
