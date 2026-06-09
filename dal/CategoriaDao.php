<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Categoria;

use PDO;
use Exception;
use PDOException;

abstract class CategoriaDao{

    public static function cadastrar(Categoria $categoria): int {
        try{
            $pdo = Conn::getConn();
            //Insert com parâmetros nomeados para evitar SQL injection.
            $sql = $pdo->prepare("INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)");
            $sql->bindValue(":nome", $categoria->getNome(), PDO::PARAM_STR);
            $sql->bindValue(":descricao", $categoria->getDescricao(), $categoria->getDescricao() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $sql->execute();

            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            //Código 23000 = violação de chave única (nome já cadastrado).
            if ($e->getCode() === "23000") {
                throw new Exception("Já existe uma categoria com esse nome");
            }
            throw new PDOException($e->getMessage());
        }
    }

    //Recupera todas as categorias e retorna uma lista de objetos Categoria.
    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM categorias ORDER BY nome");
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $categorias = [];
            foreach($res as $dados){
                $categorias[] = Categoria::criar(
                    (int) $dados["id"],
                    $dados["nome"],
                    $dados["descricao"]
                );
            }
            return $categorias;

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function buscarPorId(int $id): ?Categoria {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
            $sql->execute([$id]);

            $dados = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            return Categoria::criar(
                (int) $dados["id"],
                $dados["nome"],
                $dados["descricao"]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function editar(Categoria $categoria): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("UPDATE categorias SET nome = ?, descricao = ? WHERE id = ?");
            $sql->execute([
                $categoria->getNome(),
                $categoria->getDescricao(),
                $categoria->getId()
            ]);
            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao processar a edição");
            }
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new Exception("Já existe uma categoria com esse nome");
            }
            throw $e;
        }
    }

    public static function excluir(int $id): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
            $sql->execute([$id]);

            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao deletar categoria");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
