<?php
require_once __DIR__ . '/../config/db.php';

class AuthController {

    public static function login() {
        global $pdo;

        $input = json_decode(file_get_contents("php://input"), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if ($email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(["error" => "Email y contraseña son obligatorios"]);
            return;
        }

        try {
            $stmt = $pdo->prepare("SELECT id, email, password, role FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(401);
                echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
                return;
            }

            $isHashed = str_starts_with($user['password'], '$2y$') || str_starts_with($user['password'], '$argon2');
            $valid = $isHashed ? password_verify($password, $user['password']) : ($password === $user['password']);

            if (!$valid) {
                http_response_code(401);
                echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
                return;
            }

            echo json_encode([
                "user" => [
                    "id" => $user['id'],
                    "email" => $user['email'],
                    "role" => $user['role']
                ]
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Error interno", "details" => $e->getMessage()]);
        }
    }
}