<?php
namespace App\Dal;

use PDO;
use PDOException;
use Exception;
//Classe abstrata para gerenciar a conexão com o banco usando PDO.
abstract class Conn{
    private static ?PDO $conn = null;
    private static string $host = "localhost:3306";
    private static string $dbname = "locadora";
    private static string $user = "root";
    private static string $password = "";

    //Singleton: garante uma única conexão durante a execução do programa.
    public static function getConn() : PDO{
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbname,
                    self::$user,
                    self::$password
                );
            } catch (PDOException $e) {
                throw new Exception("Erro ao conectar ao banco" . $e->getMessage(), 1);
            }
        }
        return self::$conn;
    }
}
