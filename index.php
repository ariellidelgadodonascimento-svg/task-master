<?php
// ==========================================
// AULA 01: O CÓDIGO SPAGHETTI (Tudo misturado)
// ==========================================

// 1. CONEXÃO COM O BANCO DE DADOS E CRIAÇÃO DA TABELA (Acoplamento de Infraestrutura)
$dbFile = __DIR__ . '/tasks.sqlite';
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    due_date TEXT NOT NULL DEFAULT '',
    responsible TEXT NOT NULL DEFAULT '',
    done INTEGER DEFAULT 0
)");

$columns = $pdo->query("PRAGMA table_info(tasks)")->fetchAll(PDO::FETCH_COLUMN, 1);
if (!in_array('description', $columns, true)) {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN description TEXT");
}
if (!in_array('due_date', $columns, true)) {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN due_date TEXT NOT NULL DEFAULT ''");
}
if (!in_array('responsible', $columns, true)) {
    $pdo->exec("ALTER TABLE tasks ADD COLUMN responsible TEXT NOT NULL DEFAULT ''");
}

// 2. LÓGICA DE NEGÓCIO E CONTROLE DE REQUISIÇÕES MISTURADOS
$error = '';

// Criar nova tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $dueDate = trim($_POST['due_date'] ?? '');
    $responsible = trim($_POST['responsible'] ?? '');
    $parsedDueDate = DateTime::createFromFormat('!Y-m-d', $dueDate);
    
    // Regra de negócio solta no meio do arquivo
    if (empty($title)) {
        $error = "O título da tarefa não pode estar vazio!";
    } elseif (!$parsedDueDate || $parsedDueDate->format('Y-m-d') !== $dueDate) {
        $error = "Informe uma data de vencimento válida!";
    } elseif (empty($responsible)) {
        $error = "Informe o responsável pela tarefa!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, responsible) VALUES (:title, :description, :due_date, :responsible)");
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':due_date', $dueDate);
        $stmt->bindValue(':responsible', $responsible);
        $stmt->execute();
        
        // Redirecionamento misturado com a lógica
        header("Location: index.php");
        exit;
    }
}

// Concluir ou excluir tarefa
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    
    if ($_GET['action'] === 'complete') {
        $pdo->exec("UPDATE tasks SET done = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'delete') {
        $pdo->exec("DELETE FROM tasks WHERE id = $id");
    }
    
    header("Location: index.php");
    exit;
}

// 3. BUSCA DE DADOS MISTURADA COM A VISUALIZAÇÃO
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master - Spaghetti</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; color: #333; display: flex; justify-content: center; padding-top: 50px; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { font-size: 1.5rem; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; font-size: 0.9rem; }
        .form-group { display: grid; gap: 12px; margin-top: 20px; margin-bottom: 20px; }
        .form-group label { display: grid; gap: 5px; font-size: 0.9rem; font-weight: 600; }
        .form-group input, .form-group textarea { width: 100%; box-sizing: border-box; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font: inherit; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        button { background: #2563eb; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        ul { list-style: none; padding: 0; }
        li { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 12px 0; border-bottom: 1px solid #eee; }
        .task-content { flex: 1; min-width: 0; }
        .task-title { font-weight: 600; }
        .task-meta { color: #6b7280; font-size: 0.85rem; margin-top: 5px; }
        .task-description { margin: 6px 0 0; color: #4b5563; overflow-wrap: anywhere; }
        li.done .task-title { text-decoration: line-through; color: #9ca3af; }
        .actions a { text-decoration: none; margin-left: 10px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master (Spaghetti Edition)</h1>

    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php" class="form-group">
        <label>Tarefa
            <input type="text" name="title" placeholder="O que precisa ser feito?" autocomplete="off" required>
        </label>
        <label>Descrição (opcional)
            <textarea name="description" placeholder="Detalhes da tarefa"></textarea>
        </label>
        <label>Data de vencimento
            <input type="date" name="due_date" required>
        </label>
        <label>Responsável
            <input type="text" name="responsible" placeholder="Nome da pessoa responsável" required>
        </label>
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div class="task-content">
                    <div class="task-title"><?php echo htmlspecialchars($task['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="task-meta">
                        Vencimento: <?php echo $task['due_date'] ? htmlspecialchars(date('d/m/Y', strtotime($task['due_date'])), ENT_QUOTES, 'UTF-8') : 'Não informado'; ?>
                        &middot; Responsável: <?php echo $task['responsible'] ? htmlspecialchars($task['responsible'], ENT_QUOTES, 'UTF-8') : 'Não informado'; ?>
                    </div>
                    <?php if (!empty($task['description'])): ?>
                        <p class="task-description"><?php echo nl2br(htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?php echo $task['id']; ?>" title="Concluir">✅</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>

// ==========================================
// feito por: [Felipe Silva e Arielli]
// ==========================================