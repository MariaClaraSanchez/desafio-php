<?php

/**
 * Classe que faz as transações e aplicação dos scripts sql no banco de dados
 */
class Transaction {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /**
     * Insere na tabela uma nova transação
     * 
     * @param int $id_pagador o id do usuário que vai realizar a transferencia
     * @param int $id_recebedor o id do usuário que vai receber a transferencia
     * @param float $valor valor que será transferido
     * 
     */
    public function create($id_pagador, $id_recebedor, $valor) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO " . $_ENV['TABLE_NAME_TRANSF'] . " (id_pagador, id_recebedor, valor) VALUES (:id_pagador, :id_recebedor, :valor)");
            $stmt->bindParam(':id_pagador', $id_pagador);
            $stmt->bindParam(':id_recebedor', $id_recebedor);
            $stmt->bindParam(':valor', $valor);
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao executar consulta'], $e->getCode());
        }
    }    

    /**
     * Após a transferência realiza update na tabela de transferência com status da requisição
     * 
     * @param int $id_transacao id único de cada transação
     * @param string $status status de cada transação realizada
     */
    public function updateStatus($id_transacao, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE " . $_ENV['TABLE_NAME_TRANSF'] . " SET status = :status WHERE id = :id");
            $stmt->bindParam(':id', $id_transacao);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch (PDOException $e) {
            Response::json(['error' => 'Erro ao executar update status'], $e->getCode());
        }
    }
}
?>



