<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Filme;

use PDO;
use Exception;
use PDOException;

abstract class FilmeDao{

    public static function cadastrar(Filme $filme) : int {
        try{
            //Obtendo a conexão com o banco de dados.
            $pdo = Conn::getConn();
            //Query de inserção com parâmetros nomeados para evitar SQL injection.
            $sql = $pdo->prepare("INSERT INTO filmes (titulo, genero, ano, valor_locacao, duracao, idade_recomendada, imagem) VALUES (:titulo, :genero, :ano, :valor, :duracao, :idade, :imagem)");
            //Associando os valores aos parâmetros, especificando o tipo de cada um. Valor DECIMAL vai como string.
            $sql->bindValue(":titulo", $filme->getTitulo(), PDO::PARAM_STR);
            $sql->bindValue(":genero", $filme->getGenero(), PDO::PARAM_STR);
            $sql->bindValue(":ano", $filme->getAno(), PDO::PARAM_INT);
            $sql->bindValue(":valor", $filme->getValorLocacao(), PDO::PARAM_STR);
            $sql->bindValue(":duracao", $filme->getDuracao(), PDO::PARAM_INT);
            $sql->bindValue(":idade", $filme->getIdadeRecomendada(), PDO::PARAM_STR);
            //Imagem pode ser nula quando o filme é cadastrado sem capa.
            $sql->bindValue(":imagem", $filme->getImagem(), $filme->getImagem() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $sql->execute();
            //Retornando o ID recém-inserido.
            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    //Recupera todos os filmes e retorna uma lista de objetos Filme.
    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM filmes");
            $sql->execute();
            //fetchAll retorna um array associativo, um elemento por filme.
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $filmes = [];
            //Criando um objeto Filme para cada registro usando a factory method criar.
            foreach($res as $dados){
                $filmes[] = self::montar($dados);
            }
            return $filmes;

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function excluir(int $id): void{
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("DELETE FROM filmes WHERE id=?");
            $sql->execute([$id]);

            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao deletar Filme");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function buscarPorId(int $id): ?Filme {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM filmes WHERE id=?");
            $sql->execute([$id]);

            $dados = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            return self::montar($dados);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function editar(Filme $filme) : void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("UPDATE filmes SET titulo=?, genero=?, ano=?, valor_locacao=?, duracao=?, idade_recomendada=?, imagem=? WHERE id=?");
            $sql->execute([
                $filme->getTitulo(),
                $filme->getGenero(),
                $filme->getAno(),
                $filme->getValorLocacao(),
                $filme->getDuracao(),
                $filme->getIdadeRecomendada(),
                $filme->getImagem(),
                $filme->getId()
            ]);
            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao processar a edição");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //Monta um objeto Filme a partir de uma linha do banco (evita repetir o Filme::criar).
    private static function montar(array $dados): Filme {
        return Filme::criar(
            (int) $dados["id"],
            $dados["titulo"],
            $dados["genero"],
            (int) $dados["ano"],
            (float) $dados["valor_locacao"],
            (string) $dados["duracao"],
            $dados["idade_recomendada"],
            $dados["imagem"]
        );
    }
}
