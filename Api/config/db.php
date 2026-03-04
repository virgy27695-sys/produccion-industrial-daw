<?php
class Database {
    public static function connect() {
        $host = "localhost";
        $db   = "fabrica_faros";
        $user = "root";
        $pass = "root";
        $charset = "utf8mb4";

        try {
            return new PDO(
                "mysql:host=$host;dbname=$db;charset=$charset",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error de conexión",
                "details" => $e->getMessage()
            ]);
            exit;
        }
    }
}