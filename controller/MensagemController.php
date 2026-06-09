<?php
namespace App\Controller;

use App\Util\Auth;
use App\Dal\MensagemDao;
use App\View\mensagemView;
use Exception;

class MensagemController{
    public static ?string $msg = null;

    //Só admin vê as mensagens de contato recebidas.
    public static function listar(?int $deletar = null): void {
        Auth::exigirAdmin();
        $mensagens = MensagemDao::listar();
        mensagemView::listar($mensagens, $deletar);
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
                MensagemDao::excluir((int) $_GET["deletar"]);
            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
            header("Location: ?p=mensagens");
            exit;
        }
    }
}
