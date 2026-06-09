<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Locacao;

use PDO;
use Exception;
use PDOException;

abstract class LocacaoDao{

    public static function cadastrar(Locacao $locacao): int {
        try{
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("INSERT INTO locacoes (usuario_id, filme_id) VALUES (:usuario, :filme)");
            $sql->bindValue(":usuario", $locacao->getUsuarioId(), PDO::PARAM_INT);
            $sql->bindValue(":filme", $locacao->getFilmeId(), PDO::PARAM_INT);
            $sql->execute();

            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    //Listagem para o admin: todas as locações com nome do usuário e título do filme (JOIN).
    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare(
                "SELECT l.id, l.usuario_id, l.filme_id, l.data_locacao, u.nome AS usuario_nome, f.titulo AS filme_titulo
                 FROM locacoes l
                 JOIN usuarios u ON u.id = l.usuario_id
                 JOIN filmes f ON f.id = l.filme_id
                 ORDER BY l.data_locacao DESC"
            );
            $sql->execute();
            return self::montarLista($sql->fetchAll(PDO::FETCH_ASSOC));

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    //Listagem das locações de um único usuário (as "minhas locações").
    public static function listarPorUsuario(int $usuarioId): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare(
                "SELECT l.id, l.usuario_id, l.filme_id, l.data_locacao, u.nome AS usuario_nome, f.titulo AS filme_titulo
                 FROM locacoes l
                 JOIN usuarios u ON u.id = l.usuario_id
                 JOIN filmes f ON f.id = l.filme_id
                 WHERE l.usuario_id = ?
                 ORDER BY l.data_locacao DESC"
            );
            $sql->execute([$usuarioId]);
            return self::montarLista($sql->fetchAll(PDO::FETCH_ASSOC));

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    //Transforma as linhas do banco em objetos Locacao.
    private static function montarLista(array $res): array {
        $locacoes = [];
        foreach($res as $dados){
            $locacoes[] = Locacao::criar(
                (int) $dados["id"],
                (int) $dados["usuario_id"],
                (int) $dados["filme_id"],
                $dados["data_locacao"],
                $dados["usuario_nome"],
                $dados["filme_titulo"]
            );
        }
        return $locacoes;
    }
}
