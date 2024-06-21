<?php

class Response {
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
