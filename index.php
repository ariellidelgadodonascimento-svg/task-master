<?php
// Sistema de Autoloading Nativo (PSR-0 simplificado)
spl_autoload_register(function ($class) {
    $dirs = ['Model', 'Controller', 'View'];
    foreach ($dirs as $dir) {
        $file = __DIR__ . "/src/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// 1. Conexão com o banco (Único lugar no sistema inteiro!)
$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Roteamento básico
$controller = new TaskController($pdo);
$action = $_GET['action'] ?? 'index'; // Se não vier action, usa 'index'

if (method_exists($controller, $action)) {
    $controller->$action(); // Executa o método correspondente
} else {
    echo "Página não encontrada 404";
}
?>