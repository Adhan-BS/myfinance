<?php
$host = '127.0.0.1';
$db   = 'MyFinance';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Falha na conexão com o banco: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'sucesso'  => false,
        'mensagem' => 'Erro interno. Tente novamente mais tarde.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}