<?php
namespace App\Dal;

use App\Dal\Conn;
use App\Model\Usuario;

use PDO;
use Exception;
use PDOException;

abstract class UsuarioDao{

    public static function cadastrar(Usuario $usuario): int {
        try{
            $pdo = Conn::getConn();
            //A senha já chega como hash (password_hash) vinda do controller.
            $sql = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, cpf, nascimento, tipo) VALUES (:nome, :email, :senha, :cpf, :nascimento, :tipo)");
            $sql->bindValue(":nome", $usuario->getNome(), PDO::PARAM_STR);
            $sql->bindValue(":email", $usuario->getEmail(), PDO::PARAM_STR);
            $sql->bindValue(":senha", $usuario->getSenha(), PDO::PARAM_STR);
            $sql->bindValue(":cpf", $usuario->getCpf(), PDO::PARAM_STR);
            $sql->bindValue(":nascimento", $usuario->getNascimento(), PDO::PARAM_STR);
            $sql->bindValue(":tipo", $usuario->getTipo(), PDO::PARAM_STR);
            $sql->execute();

            return (int) $pdo->lastInsertId();

        }catch(PDOException $e){
            //Código 23000 = violação de chave única (e-mail ou CPF já cadastrado).
            if ($e->getCode() === "23000") {
                throw new Exception("Este e-mail ou CPF já está cadastrado");
            }
            throw new PDOException($e->getMessage());
        }
    }

    //Recupera todos os usuários e retorna uma lista de objetos Usuario.
    public static function listar(): array {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM usuarios ORDER BY nome");
            $sql->execute();
            $res = $sql->fetchAll(PDO::FETCH_ASSOC);

            $usuarios = [];
            foreach($res as $dados){
                $usuarios[] = self::montar($dados);
            }
            return $usuarios;

        }catch(PDOException $e){
            throw new PDOException($e->getMessage());
        }
    }

    public static function buscarPorId(int $id): ?Usuario {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
            $sql->execute([$id]);

            $dados = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            return self::montar($dados);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //Busca um usuário pelo e-mail. Usado no login. Retorna null se não encontrar.
    public static function buscarPorEmail(string $email): ?Usuario {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
            $sql->execute([$email]);

            $dados = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            return self::montar($dados);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //Busca um usuário pelo par CPF + data de nascimento. Usado na recuperação de senha.
    public static function buscarPorCpfNascimento(string $cpf, string $nascimento): ?Usuario {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("SELECT * FROM usuarios WHERE cpf = ? AND nascimento = ?");
            $sql->execute([$cpf, $nascimento]);

            $dados = $sql->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            return self::montar($dados);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function editar(Usuario $usuario): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, cpf = ?, nascimento = ?, tipo = ? WHERE id = ?");
            $sql->execute([
                $usuario->getNome(),
                $usuario->getEmail(),
                $usuario->getCpf(),
                $usuario->getNascimento(),
                $usuario->getTipo(),
                $usuario->getId()
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                throw new Exception("Este e-mail ou CPF já está cadastrado");
            }
            throw $e;
        }
    }

    //Atualiza apenas a senha (já como hash). Usado na recuperação de senha.
    public static function redefinirSenha(int $id, string $hash): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $sql->execute([$hash, $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public static function excluir(int $id): void {
        try {
            $pdo = Conn::getConn();
            $sql = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $sql->execute([$id]);

            if ($sql->rowCount() !== 1) {
                throw new Exception("Erro ao deletar usuário");
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    //Monta um objeto Usuario a partir de uma linha do banco (evita repetir o Usuario::criar).
    private static function montar(array $dados): Usuario {
        return Usuario::criar(
            (int) $dados["id"],
            $dados["nome"],
            $dados["email"],
            $dados["senha"],
            $dados["cpf"],
            $dados["nascimento"],
            $dados["tipo"]
        );
    }
}
