<?php
//Victor Gonçalves 38094649 - Leonardo Valente Montes 42979846 - Enzo Chemin 33402621 - Felipe Paulista Silveira 43389988 - Hygor Daniel Fieszt 41984561

//declare(strict_types=1) ativa o modo de tipagem estrita: o PHP lança erro se os tipos não corresponderem ao esperado.
declare(strict_types=1);
namespace App;

require_once "./Autoload.php";

//Alias para facilitar a chamada dos métodos dos controladores.
use App\Controller\FilmeController as Filme;
use App\Controller\UsuarioController as Usuario;
use App\Controller\CategoriaController as Categoria;
use App\Controller\PaginaController as Pagina;
use App\Controller\MensagemController as Mensagem;
use App\Controller\LocacaoController as Locacao;
use App\Util\Auth;

//Inicia a sessão antes de qualquer saída para o controle de login funcionar.
Auth::iniciar();
?>
<!DOCTYPE HTML>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Locadora de Filmes</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <header>
        <nav>
            <?php require_once("./menu.php");?>
        </nav>
    </header>
    <main>
    <?php
        //Roteamento: o parâmetro "p" da URL define a página. Sem ele, a rota padrão é o catálogo público. Rota inválida cai no 404.
        $page = $_GET["p"] ?? "catalogo";
        match($page) {
            //Páginas de navegação aberta (sem login).
            "catalogo" => Pagina::catalogo(),
            "sobre"    => Pagina::sobre(),
            "contato"  => Pagina::contato(),

            //CRUD Filmes (admin).
            "list"     => Filme::listar(),
            "cad"      => Filme::cadastrar(),
            "alt"      => Filme::editar(),
            "deletar"  => Filme::deletar(),

            //CRUD Categorias (admin).
            "categorias" => Categoria::listar(),
            "catCad"     => Categoria::cadastrar(),
            "catAlt"     => Categoria::editar(),
            "catDel"     => Categoria::deletar(),

            //CRUD Usuários (admin).
            "usuarios" => Usuario::listar(),
            "userAlt"  => Usuario::editar(),
            "userDel"  => Usuario::deletar(),

            //Mensagens de contato (admin).
            "mensagens" => Mensagem::listar(),
            "msgDel"    => Mensagem::deletar(),

            //Locações.
            "locar"    => Locacao::locar(),
            "minhas"   => Locacao::minhas(),
            "locacoes" => Locacao::listar(),

            //Autenticação.
            "login"     => Usuario::login(),
            "registro"  => Usuario::registrar(),
            "recuperar" => Usuario::recuperar(),
            "logout"    => Usuario::logout(),

            default    => require_once("./view/404.php"),
        };
    ?>
    </main>
    <footer>
        <small>
            Copyright &copy; - <?= date("Y") ?>
        </small>
    </footer>
</body>
</html>
