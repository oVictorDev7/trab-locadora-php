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

    //Realiza a locação de um filme. Exige login (quem não está logado vai para o login).
    public static function locar(): void {
        Auth::exigirLogin();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["filme_id"])) {
            try{
                Csrf::validar();

                $filmeId = (int) $_POST["filme_id"];
                //Garante que o filme realmente existe antes de registrar a locação.
                $filme = FilmeDao::buscarPorId($filmeId);
                if ($filme === null) {
                    throw new Exception("Filme não encontrado");
                }

                $usuario = Auth::usuario();
                $locacao = Locacao::criar(null, (int) $usuario["id"], $filmeId);
                LocacaoDao::cadastrar($locacao);

                //Redireciona para "minhas locações" com aviso de sucesso.
                header("Location: ?p=minhas&ok=1");
                exit;

            }catch(Exception $e){
                self::$msg = $e->getMessage();
            }
        }
        //Em caso de erro (ou acesso indevido), mostra a página de minhas locações com a mensagem.
        self::minhas();
    }

    //Locações do próprio usuário logado.
    public static function minhas(): void {
        Auth::exigirLogin();
        $usuario = Auth::usuario();
        $locacoes = LocacaoDao::listarPorUsuario((int) $usuario["id"]);
        $sucesso = isset($_GET["ok"]);
        locacaoView::minhas($locacoes, $sucesso, self::$msg);
    }

    //Listagem para o admin: todas as locações, mostrando quem alugou qual filme.
    public static function listar(): void {
        Auth::exigirAdmin();
        $locacoes = LocacaoDao::listar();
        locacaoView::listar($locacoes);
    }
}
