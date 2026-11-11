<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/Model/Task.php';
require_once __DIR__ . '/../src/View/TaskViewInterface.php';
require_once __DIR__ . '/../src/Presenter/TaskPresenter.php';

class TaskPresenterTest extends TestCase {
    public function testIndexFormataTitulosParaMaiusculo() {
        // 1. Stub do Model (Fingimos que o banco retornou uma tarefa minúscula)
        $modelMock = $this->createStub(Task::class);
        $modelMock->method('getAll')->willReturn([
            ['id' => 1, 'title' => 'estudar php', 'description' => '', 'due_date' => '', 'done' => 0]
        ]);

        // 2. Mock da View Interface [cite: 216]
        $viewMock = $this->createMock(TaskViewInterface::class);

        // 3. A EXPECTATIVA: Garantimos que o Presenter chamou 'displayTasks'
        // passando o título formatado em MAIÚSCULO.
        $viewMock->expects($this->once())
                ->method('displayTasks')
                ->with($this->callback(function($tasks) {
                    return $tasks[0]['title'] === 'ESTUDAR PHP';
                }));

        // 4. Executamos o teste injetando os Mocks
        $presenter = new TaskPresenter($modelMock, $viewMock);
        $presenter->index();
    }
}
?>