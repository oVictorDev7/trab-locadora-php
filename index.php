<?php

declare(strict_types=1);
namespace App;

require_once "./Autoload.php";

use App\Controller\FilmeController as Filme;
use App\Controller\UsuarioController as Usuario;
use App\Controller\CategoriaController as Categoria;
use App\Controller\PaginaController as Pagina;
use App\Controller\MensagemController as Mensagem;
use App\Controller\LocacaoController as Locacao;
use App\Util\Auth;

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
        $page = $_GET["p"] ?? "catalogo";
        match($page) {
            "catalogo" => Pagina::catalogo(),
            "sobre"    => Pagina::sobre(),
            "contato"  => Pagina::contato(),

            "list"     => Filme::listar(),
            "cad"      => Filme::cadastrar(),
            "alt"      => Filme::editar(),
            "deletar"  => Filme::deletar(),

            "categorias" => Categoria::listar(),
            "catCad"     => Categoria::cadastrar(),
            "catAlt"     => Categoria::editar(),
            "catDel"     => Categoria::deletar(),

            "usuarios" => Usuario::listar(),
            "userAlt"  => Usuario::editar(),
            "userDel"  => Usuario::deletar(),

            "mensagens" => Mensagem::listar(),
            "msgDel"    => Mensagem::deletar(),

            "locar"    => Locacao::locar(),
            "minhas"   => Locacao::minhas(),
            "locacoes" => Locacao::listar(),

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
