<?php
$host = '127.0.0.1';
$db   = 'MyFinance';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Registra o erro técnico no log do servidor e exibe mensagem genérica
    error_log("Erro crítico de conexão: " . $e->getMessage());
    die("Serviço temporariamente indisponível. Tente novamente em instantes.");
}
?>