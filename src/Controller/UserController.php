<?php

include('./src/Model/User.php'); 
include('./config/Response.php');
include('./vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController {
    private $user;
    private $secretKey;

    public function __construct() {
        $this->user = new User();
        $this->secretKey = $_ENV['SECRET_KEY'];
    }

    public function register($data) {        
        if (empty($data['name']) || empty($data['password']) || empty($data['email'])) {
            Response::json(['error' => 'Nome, senha e email são obrigatórios'], 400);
        }

        if ($this->user->create($data)) {
            Response::json(['message' => 'Usuário(a) ' . $data['name'] . ' cadastrado com sucesso']);
        } else {
            Response::json(['error' => 'Falha ao cadastrar usuário'], 500);
        }
    }

    public function login($data) {
        
        if ((empty($data['name']) && empty($data['email'])) || empty($data['password'])) {
            Response::json(['error' => 'Nome ou e-mail, e senha são obrigatórios'], 400);
        }
        
        $user = $this->user->getByUsername($data);
        if ($user && password_verify($data['password'], $user['password'])) {
            $payload = [
                'id' => $user['id'],
                'name' => $user['name'],
                'exp' => time() + (60 * 60)
            ];

            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            Response::json(['token' => $jwt]);
        } else {
            Response::json(['error' => 'Nome ou senha inválidos'], 401);
        }
    }
}

?>
