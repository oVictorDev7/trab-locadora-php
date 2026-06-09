<?php
use App\Util\Auth;
$usuario = Auth::usuario();
$paginaAtual = $_GET["p"] ?? "catalogo";
$ativo = fn(string $p): string => $paginaAtual === $p ? "active" : "";
?>
<a href="./?p=catalogo" class="brand">🎬 Locadora</a>

<div class="nav-links">
    <a href="./?p=catalogo" class="<?= $ativo("catalogo") ?>">Catálogo</a>
    <a href="./?p=sobre" class="<?= $ativo("sobre") ?>">Sobre</a>
    <a href="./?p=contato" class="<?= $ativo("contato") ?>">Contato</a>

    <?php if ($usuario !== null): ?>
        <a href="./?p=minhas" class="<?= $ativo("minhas") ?>">Minhas Locações</a>
    <?php endif; ?>

    <?php if (Auth::ehAdmin()): ?>
        <a href="./?p=list" class="<?= $ativo("list") ?>">Filmes</a>
        <a href="./?p=categorias" class="<?= $ativo("categorias") ?>">Categorias</a>
        <a href="./?p=usuarios" class="<?= $ativo("usuarios") ?>">Usuários</a>
        <a href="./?p=locacoes" class="<?= $ativo("locacoes") ?>">Locações</a>
        <a href="./?p=mensagens" class="<?= $ativo("mensagens") ?>">Mensagens</a>
    <?php endif; ?>
</div>

<div class="nav-user">
    <?php if ($usuario !== null): ?>
        <span class="usuario-logado">Olá, <?= $usuario["nome"] ?></span>
        <a href="./?p=logout">Sair</a>
    <?php else: ?>
        <a href="./?p=login">Entrar</a>
    <?php endif; ?>
</div>
