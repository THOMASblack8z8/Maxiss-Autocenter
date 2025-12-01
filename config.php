<?php
// config.php

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'maxiss_autocenter');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do Sistema
define('SITE_URL', 'http://localhost/maxiss');
define('UPLOAD_DIR', 'uploads/');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função de conexão com o banco
function getConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}

// Função para sanitizar dados
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Função para verificar login
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

// Função para redirecionar
function redirect($url) {
    header("Location: $url");
    exit;
}
?>
