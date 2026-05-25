<?php
// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// Base de datos: gbardi_db1
// ============================================

// Configuración de la conexión
define('DB_HOST', 'localhost');
define('DB_NAME', 'gbardi_db1');
define('DB_USER', 'gbardi');
define('DB_PASS', 'GbX91mQp#');
define('DB_CHARSET', 'utf8mb4');

// Clase Database con patrón Singleton
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            // Error detallado para desarrollo
            die("❌ Error de conexión a la base de datos:<br>" . 
                "Mensaje: " . $e->getMessage() . "<br>" .
                "Base de datos: " . DB_NAME . "<br>" .
                "Host: " . DB_HOST . "<br><br>" .
                "⚠️ Verifica que:<br>" .
                "1. MySQL esté iniciado en XAMPP/WAMP<br>" .
                "2. La base de datos '" . DB_NAME . "' exista<br>" .
                "3. Importes el archivo SQL en phpMyAdmin");
        }
    }
    
    // Obtener la instancia única (Singleton)
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Obtener la conexión PDO
    public function getConnection() {
        return $this->connection;
    }
    
    // Probar conexión
    public function testConnection() {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}

// ============================================
// FUNCIONES AUXILIARES GLOBALES
// ============================================

// Función rápida para obtener la conexión
function getDB() {
    return Database::getInstance()->getConnection();
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevenir XSS
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Generar token CSRF
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitizar inputs
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

// Validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Redireccionar
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Mostrar mensaje flash
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ============================================
// VERIFICACIÓN RÁPIDA DE CONEXIÓN
// (Opcional: descomentar para probar)
// ============================================

/*
try {
    $testDB = getDB();
    echo "✅ Conexión exitosa a la base de datos '" . DB_NAME . "'";
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
*/
?>