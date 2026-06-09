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
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("INSERT INTO filmes (titulo, genero, ano, valor_locacao, duracao, idade_recomendada, imagem) VALUES (:titulo, :genero, :ano, :valor, :duracao, :idade, :imagem)");
            $sql->bindValue(":titulo", $filme->getTitulo(), PDO::PARAM_STR);
            $sql->bindValue(":genero", $filme->getGenero(), PDO::PARAM_STR);
            $sql->bindValue(":ano", $filme->getAno(), PDO::PARAM_INT);
            $sql->bindValue(":valor", $filme->getValorLocacao(), PDO::PARAM_STR);
            $sql->bindValue(":duracao", $filme->getDuracao(), PDO::PARAM_INT);
            $sql->bindValue(":idade", $filme->getIdadeRecomendada(), PDO::PARAM_STR);
            $sql->bindValue(":imagem", $filme->getImagem(), $filme->getImagem() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $sql->execute();
            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM filmes");
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $filmes = [];
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
