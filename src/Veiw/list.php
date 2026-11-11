<!DOCTYPE html>
<html lang="pt-BR">
<!-- ... (Mantenham a tag <head> e o CSS antigo aqui) ... -->
<body>
<div class="container">
    <h1>Task Master (MVC Edition)</h1>
   
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- O formulário agora aponta para a action 'create' -->
    <form method="POST" action="index.php?action=create" class="form-group">
        <input type="text" name="title" placeholder="Título" required>
        <input type="text" name="description" placeholder="Descrição">
        <input type="date" name="due_date" required>
        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li class="<?php echo $task['done'] ? 'done' : ''; ?>">
                <div>
                    <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                    <small><?php echo htmlspecialchars($task['description']); ?> | Vence em: <?php echo $task['due_date']; ?></small>
                </div>
                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="index.php?action=complete&id=<?php echo $task['id']; ?>">✅</a>
                    <?php endif; ?>
                    <a href="index.php?action=delete&id=<?php echo $task['id']; ?>">❌</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>