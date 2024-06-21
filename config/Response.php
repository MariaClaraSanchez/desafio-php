<?php

/**
 * Retornar um response padronizado para todas as requisições.
 */
class Response {
    /**
    * Retorna o response
    *
    * @param int $status Status da requisição
    * @param string $messsage Mensagem que deseja retornar
    *
    */
    public static function json($message, $status = 200) {
        header("Content-Type: application/json");
        http_response_code($status);
        
        $response = [
            'data' => [
                'status' => $status,
                'message' => $message
            ]
        ];
        
        echo json_encode($response);
        exit;
    }
}
?>
