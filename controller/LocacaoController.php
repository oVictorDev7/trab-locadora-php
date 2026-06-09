<?php
namespace App\Controller;

use App\Util\Auth;
use App\Util\Csrf;
use App\Model\Locacao;
use App\Dal\FilmeDao;
use App\Dal\LocacaoDao;
use App\View\locacaoView;
use Exception;

class LocacaoController{
    public static ?string $msg = null;

    public static function locar(): void {
        Auth::exigirLogin();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filme_id"])) {
            try{
                Csrf::validar();

                $filmeId = (int) $_POST["filme_id"];
                $filme = FilmeDao::buscarPorId($filmeId);
                if ($filme === null) {
                    throw new Exception("Filme não encontrado");
                }

                $usuario = Auth::usuario();
                $locacao = Locacao::criar(null, (int) $usuario["id"], $filmeId);
                LocacaoDao::cadastrar($locacao);

                header("Location: ?p=minhas&ok=1");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        self::minhas();
    }

    public static function minhas(): void {
        Auth::exigirLogin();
        $usuario = Auth::usuario();
        $locacoes = LocacaoDao::listarPorUsuario((int) $usuario["id"]);
        $sucesso = isset($_GET["ok"]);
        locacaoView::minhas($locacoes, $sucesso, self::$msg);
    }

    public static function listar(): void {
        Auth::exigirAdmin();
        $locacoes = LocacaoDao::listar();
        locacaoView::listar($locacoes);
    }
}
