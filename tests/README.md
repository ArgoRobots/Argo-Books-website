# Argo Books Tests

PHPUnit-based test suite covering high-stakes pure logic and the financial / licensing flows.

## One-time setup

1. **Add PHP to your system PATH.** The `vendor/bin/phpunit` shim is a `.bat` file that invokes `php`, so PHP must be resolvable from any shell. Laragon doesn't add its bundled PHP to PATH by default.

   - Win+R → `sysdm.cpl` → Advanced → Environment Variables
   - Under **User variables** (or **System variables**), edit **Path** and add the full path to your active PHP install — e.g. `C:\laragon\bin\php\php-8.3.26-Win32-vs16-x64`
   - Open a fresh shell and verify with `php --version` (should print 8.3+)

   If you'd rather not touch global PATH, run tests from **Laragon's bundled terminal** (right-click the tray icon → Terminal) — it sets PATH up automatically.

2. **Create the test database** in MySQL/HeidiSQL:
   ```sql
   CREATE DATABASE argo_books_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema** (re-run any time `mysql_schema.sql` changes):
   ```
   mysql -u root argo_books_test < ../mysql_schema.sql
   ```

4. **Install dev dependencies**:
   ```
   composer install
   ```

5. **Verify `.env.testing` exists** at the project root with `DB_NAME=argo_books_test`. The bootstrap aborts if `DB_NAME` is anything else, so production data is safe.

## Running tests

```
vendor/bin/phpunit                          # all tests
vendor/bin/phpunit --testsuite=Unit         # fast, no DB
vendor/bin/phpunit --testsuite=Integration  # DB-touching
vendor/bin/phpunit --filter CalculateProcessingFee
vendor/bin/phpunit --testdox                # readable output
```

## Layout

- `tests/Unit/` — pure functions, no DB, no network. Should run in seconds.
- `tests/Integration/` — touches `argo_books_test`. Each test wraps in a transaction and rolls back at teardown (via `DatabaseTestCase`), or does manual cleanup (via `IntegrationTestCase` for functions that open their own transactions).
- `tests/Helpers/` — base classes and fixture helpers.

## Adding tests

- Extend `Tests\Helpers\DatabaseTestCase` for tests that don't call `beginTransaction()` themselves — fast isolation via auto-rollback.
- Extend `Tests\Helpers\IntegrationTestCase` for tests against functions that manage their own transactions (e.g. `redeem_premium_key`). These do manual cleanup of seed rows in `tearDown()`.
- Schema changes: re-import `mysql_schema.sql` into `argo_books_test`, then update fixture helpers in `tests/Helpers/` if column names changed.
