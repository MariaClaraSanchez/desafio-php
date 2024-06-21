<?php

include('./src/Model/User.php'); 
include('./config/Response.php');
include('./vendor/autoload.php');

use Firebase\JWT\JWT;

/**
 * Classe responsável por realizar controle de usuários
 */
class UserController {
    private $user;
    private $secretKey;

    public function __construct() {
        $this->user = new User();
        $this->secretKey = $_ENV['SECRET_KEY'];
    }

    /**
     * Cadastrar um usuário novo
     * 
     * @param array $data array cntendo o paylaod recebido, que vai conter os dados da transação
     */
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

    /**
     *  Realizar login do usuário, retorna o token de acesso JWT
     * 
     * @param array $data array cntendo o paylaod recebido, que vai conter os dados da transação
     * 
     */
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
