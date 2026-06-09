<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Mensagem;

use PDO;
use Exception;
use PDOException;

abstract class MensagemDao{

    public static function cadastrar(Mensagem $mensagem): int {
        try{
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("INSERT INTO mensagens (nome, email, mensagem) VALUES (:nome, :email, :mensagem)");
            $sql->bindValue(":nome", $mensagem->getNome(), PDO::PARAM_STR);
            $sql->bindValue(":email", $mensagem->getEmail(), PDO::PARAM_STR);
            $sql->bindValue(":mensagem", $mensagem->getMensagem(), PDO::PARAM_STR);
            $sql->execute();

            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM mensagens ORDER BY data_envio DESC");
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $mensagens = [];
            foreach($res as $dados){
                $mensagens[] = Mensagem::criar(
                    (int) $dados["id"],
                    $dados["nome"],
                    $dados["email"],
                    $dados["mensagem"],
                    $dados["data_envio"]
                );
            }
            return $mensagens;

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function excluir(int $id): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("DELETE FROM mensagens WHERE id = ?");
            $sql->execute([$id]);

            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao deletar mensagem");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
