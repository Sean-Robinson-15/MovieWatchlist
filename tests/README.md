# Test Structure

Tests are organized strictly by Epic and User Story:

```text
tests/
├── epic01/us01..us03/
├── epic02/us04..us09/
├── epic03/us10..us14-delete/
├── epic03/us14-vote/
└── epic04/us15..us18/
```

Each story contains `unit`, `integration`, and `acceptance` directories. All database tests extend `Tests\\Support\\IsolatedDatabaseTestCase`, which creates a fresh in-memory SQLite database, enables foreign keys, applies all migrations, and releases the connection after every test.
