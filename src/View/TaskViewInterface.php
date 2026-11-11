<?php
interface TaskViewInterface {
    // A View DEVE saber exibir uma lista de tarefas
    public function displayTasks(array $tasks);
   
    // A View DEVE saber exibir uma mensagem de erro
    public function showError(string $message);
}
?>
