<?php

/**
 * 
 */

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

include('./src/Controller/UserController.php');
include('./src/Controller/TransactionController.php');

$requestMethod = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER['REQUEST_URI'];

$userController = new UserController();
$transactionController = new TransactionController();

$data = json_decode(file_get_contents('php://input'), true);
switch ($path) {
    case '/register':
        if ($requestMethod == 'POST') {
            if (is_array($data)) {
                $userController->register($data);
            } else {
                Response::json(['error' => 'Dados inválidos'], 400);
            }
        }
        break;
    case '/login':
        if ($requestMethod == 'POST') {
            if (is_array($data)) {
                $userController->login($data);
            } else {
                Response::json(['error' => 'Dados inválidos'], 400);
            }
        }
        break;
    case '/transfer':
        if ($requestMethod == 'POST') {
            if (is_array($data)) {
                $transactionController->transfer($data);
            } else {
                Response::json(['error' => 'Dados inválidos'], 400);
            }
        }
        break;
    default:
        Response::json(['error' => 'Endpoint não encontrado'], 404);
}

?>