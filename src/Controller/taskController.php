<?php
class TaskController {
    private $model;

    public function __construct(PDO $pdo) {
        $this->model = new Task($pdo); // Controller instancia o Model
    }

    public function index($error = null) {
        $tasks = $this->model->getAll();
        require __DIR__ . '/../View/list.php'; // Envia para a View
    }

    public function create() {
        try {
            $this->model->save($_POST['title'], $_POST['description'], $_POST['due_date']);
            header("Location: index.php");
        } catch (Exception $e) {
            $this->index($e->getMessage()); // Mostra o erro na tela
        }
    }

    public function complete() {
        $this->model->complete($_GET['id']);
        header("Location: index.php");
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php");
    }
}
?>