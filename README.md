# Task Master

Aplicação de tarefas em PHP e SQLite. Cada tarefa tem título, descrição opcional, data de vencimento obrigatória e responsável obrigatório.

## Executar

Requer PHP com PDO_SQLite habilitado. Na pasta do projeto, inicie o servidor:

```sh
php -S 127.0.0.1:8000
```

Acesse `http://127.0.0.1:8000`. O arquivo `tasks.sqlite` é criado automaticamente.