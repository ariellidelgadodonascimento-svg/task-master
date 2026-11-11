<?php
class TaskHtmlView implements TaskViewInterface {
    public function displayTasks(array $tasks) {
        // A View simplesmente pega os dados e inclui o template HTML
        require __DIR__ . '/list.php';
    }

    public function showError(string $message) {
        $error = $message;
        $tasks = []; // Para evitar erro no foreach do HTML
        require __DIR__ . '/list.php';
    }
}
?>