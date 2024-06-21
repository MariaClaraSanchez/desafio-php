<?php

include_once __DIR__ . '/../Model/Transaction.php';
include_once __DIR__ . '/../Model/User.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TransactionController {
    private $transaction;
    private $user;
    private $conn;
    private $secretKey;

    public function __construct() {
        $this->transaction = new Transaction();
        $this->user = new User();
        $this->secretKey = $_ENV['SECRET_KEY'];
        $this->conn = Database::getInstance()->getConnection();
    }

    private function authenticate($token, $secretKey) {
        try {
            return JWT::decode($token, new Key($secretKey, 'HS256'));
        } catch (Exception $e) {
            Response::json(['error' => 'Usuário Não autorizado'], 401);
            exit;
        }
    }

    public function transfer($data) {
        if (empty($data['token']) || empty($data['id_recebedor']) || empty($data['valor'])) {
            Response::json(['error' => 'Token, id_recebedor e valor são obrigatórios'], 400);
            return;
        }

        $auth = $this->authenticate($data['token'], $this->secretKey);
        $id_pagador = $auth->id;
        $id_recebedor = $data['id_recebedor'];
        $valor = $data['valor'];

        if ($this->user->getSaldo($id_pagador) < $valor) {
            Response::json(['error' => 'Saldo Insuficiente'], 400);
            return;
        }

        try {
            $this->conn->beginTransaction();

            $transactionId = $this->transaction->create($id_pagador, $id_recebedor, $valor);
            $this->user->updateSaldo($id_pagador, $this->user->getSaldo($id_pagador) - $valor);
            $this->user->updateSaldo($id_recebedor, $this->user->getSaldo($id_recebedor) + $valor);
            $this->transaction->updateStatus($transactionId, 'SUCESSO');
            $this->conn->commit();
            Response::json(['message' => 'Transferência bem sucedida'],200);
        } catch (Exception $e) {
            $this->conn->rollBack();
            if (isset($transactionId)) {
                $this->transaction->updateStatus($transactionId, 'ROLLBACK');
            }
            Response::json(['error' => 'Falha na transferência, rollback realizado'], 500);
        }
    }
}
?>
