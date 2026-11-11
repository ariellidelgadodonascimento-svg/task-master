<?php
class Task {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Busca todas as tarefas
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM tasks ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Salva uma nova tarefa (agora com os campos do desafio!)
    public function save($title, $description, $dueDate) {
        if (empty(trim($title)) || empty(trim($dueDate))) {
            throw new Exception("Título e Data são obrigatórios.");
        }
       
        $stmt = $this->pdo->prepare("INSERT INTO tasks (title, description, due_date) VALUES (:title, :description, :due_date)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $dueDate);
        return $stmt->execute();
    }

    public function complete($id) {
        return $this->pdo->exec("UPDATE tasks SET done = 1 WHERE id = " . (int)$id);
    }

    public function delete($id) {
        return $this->pdo->exec("DELETE FROM tasks WHERE id = " . (int)$id);
    }
}
?>