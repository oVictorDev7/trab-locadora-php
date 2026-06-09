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

class PaginaController{
    public static ?string $msg = null;
    public static ?string $sucesso = null;

    public static function catalogo(): void {
        $filmes = FilmeDao::listar();
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
