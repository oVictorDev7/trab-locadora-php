<?php
//Usando o autoload para carregar as classes automaticamente, sem precisar usar require_once em cada arquivo, seguindo a estrutura de namespaces e diretórios do projeto.
spl_autoload_register(function ($namespace){
    //Convertendo o namespace da classe em um caminho de arquivo, substituindo as barras invertidas por separadores de diretório e removendo o prefixo "App\" para localizar o arquivo correto dentro da pasta do projeto.
    $arquivo = __DIR__ . DIRECTORY_SEPARATOR . str_replace(['App\\', '\\'],['', DIRECTORY_SEPARATOR], $namespace) . '.php';
    //Verificando se o arquivo existe antes de tentar carregá-lo, para evitar erros de inclusão de arquivos inexistentes.
    if (file_exists($arquivo)) {
        require_once $arquivo;
    }
});
