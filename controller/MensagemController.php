<?php
namespace App\Controller;

use App\Util\Auth;
use App\Dal\MensagemDao;
use App\View\mensagemView;
use Exception;

class MensagemController{
    public static ?string $msg = null;

    public static function listar(?int $deletar = null): void {
        Auth::exigirAdmin();
        $mensagens = MensagemDao::listar();
        mensagemView::listar($mensagens, $deletar);
    }

    public static function deletar(): void {
        Auth::exigirAdmin();

        if (isset($_GET["del"])) {
            self::listar((int) $_GET["del"]);
        }
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
