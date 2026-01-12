# Integration Tests

Integration tests for the WildDuck PHP Client using the `kurbar/wildduck-test-server` package.

## Overview

The integration tests use a real WildDuck server instance to test actual API interactions. The test server is automatically started before tests run and stopped after completion.

## Running Integration Tests

### Run All Tests (Unit + Integration)

```bash
vendor/bin/phpunit
```

### Run Only Integration Tests

```bash
vendor/bin/phpunit --testsuite Integration
```

### Run Only Unit Tests

```bash
vendor/bin/phpunit --testsuite Unit
```

### Run Specific Integration Test

```bash
vendor/bin/phpunit tests/Integration/Service/UserServiceIntegrationTest.php
```

### Run with Verbose Output

```bash
vendor/bin/phpunit --testsuite Integration --testdox
```

## Test Structure

```
tests/
├── Integration/
│   ├── IntegrationTestCase.php           # Base class for all integration tests
│   └── Service/
│       ├── UserServiceIntegrationTest.php
│       ├── AddressServiceIntegrationTest.php
│       ├── MailboxServiceIntegrationTest.php
│       ├── AuthenticationServiceIntegrationTest.php
│       └── FilterServiceIntegrationTest.php
└── Unit/
    └── Service/
        └── ServiceStructureTest.php
```

## How It Works

### Test Server Management

1. **Startup**: WildDuck test server starts automatically in `setUpBeforeClass()`
2. **Readiness**: Tests wait for server to be ready (max 15 seconds)
3. **Tests Run**: Each test creates/modifies/deletes resources
4. **Cleanup**: Resources are cleaned up in `tearDown()`
5. **Shutdown**: Server stops automatically in `tearDownAfterClass()`

### Base Test Class

`IntegrationTestCase` provides:

- **Automatic server management** - Start/stop test server
- **Client setup** - Pre-configured WildduckClient instance
- **Helper methods**:
  - `generateUniqueUsername()` - Creates unique usernames
  - `generateUniqueEmail()` - Creates unique email addresses
  - `cleanupUser($userId)` - Safely deletes test users

### Example Test

```php
use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\User\CreateUserDto;

class MyServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
        }
        parent::tearDown();
    }

    public function testCreateUser(): void
    {
        $dto = new CreateUserDto(
            username: $this->generateUniqueUsername(),
            password: 'TestPass123!',
            address: $this->generateUniqueEmail()
        );

        $result = $this->client->users()->create($dto);

        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->id);

        $this->createdUserId = $result->id;
    }
}
```

## Configuration

Test server configuration is in `phpunit.xml`:

```xml
<php>
    <env name="WILDDUCK_API_URL" value="http://localhost:8080"/>
    <env name="WILDDUCK_ACCESS_TOKEN" value="testtoken"/>
</php>
```

## Test Coverage

### Current Coverage

- ✅ **UserService** - User lifecycle (create, read, update, delete, list, resolve)
- ✅ **AddressService** - Address management (create, list, get, update, delete, resolve)
- ✅ **MailboxService** - Mailbox operations (create, list, get, update, delete)
- ✅ **AuthenticationService** - Authentication (success and failure cases)
- ✅ **FilterService** - Email filters (create, list, get, update, delete)

### Recommended Additional Tests

- [ ] MessageService - Message handling
- [ ] AutoreplyService - Auto-reply settings
- [ ] ApplicationPasswordService - App passwords
- [ ] TwoFactorAuthenticationService - 2FA
- [ ] StorageService - File storage
- [ ] AuditService - Audit logs
- [ ] WebhookService - Webhook management

## Best Practices

### 1. Always Clean Up

```php
protected function tearDown(): void
{
    if ($this->createdUserId !== null) {
        $this->cleanupUser($this->createdUserId);
        $this->createdUserId = null;
    }
    parent::tearDown();
}
```

### 2. Use Unique Identifiers

```php
$username = $this->generateUniqueUsername();  // testuser_abc123_1234567890
$email = $this->generateUniqueEmail();        // test_abc123@example.com
```

### 3. Test Complete Lifecycles

```php
public function testResourceLifecycle(): void
{
    // 1. Create
    $createResult = $this->client->service()->create($dto);
    $this->assertTrue($createResult->success);

    // 2. Read
    $resource = $this->client->service()->get($id);
    $this->assertEquals($expected, $resource->property);

    // 3. Update
    $updateResult = $this->client->service()->update($id, $updateDto);
    $this->assertTrue($updateResult->success);

    // 4. Delete
    $deleteResult = $this->client->service()->delete($id);
    $this->assertTrue($deleteResult->success);
}
```

### 4. Test Edge Cases

```php
public function testAuthenticationFailure(): void
{
    $result = $this->client->authentication()->authenticate(
        new AuthenticateDto(username: 'user', password: 'wrong')
    );

    $this->assertFalse($result->success);
}
```

## Troubleshooting

### Server Won't Start

```bash
# Check if port 8080 is available
lsof -i :8080

# Kill any process using the port
kill -9 <PID>
```

### Tests Timing Out

Increase the startup timeout in `IntegrationTestCase.php`:

```php
$maxAttempts = 60; // Increase from 30
```

### Tests Failing Randomly

Ensure proper cleanup in `tearDown()`:

```php
protected function tearDown(): void
{
    // Clean up all created resources
    if ($this->userId) $this->cleanupUser($this->userId);
    if ($this->mailboxId) $this->cleanupMailbox($this->mailboxId);

    parent::tearDown();
}
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: curl, json, mbstring

      - name: Install Dependencies
        run: composer install

      - name: Run Unit Tests
        run: vendor/bin/phpunit --testsuite Unit

      - name: Run Integration Tests
        run: vendor/bin/phpunit --testsuite Integration
```

## Performance

- **Server Startup**: ~3-5 seconds
- **Per Test**: ~100-500ms
- **Full Suite**: ~10-30 seconds (depending on number of tests)

## Contributing

When adding new integration tests:

1. Extend `IntegrationTestCase`
2. Use helper methods for unique identifiers
3. Clean up resources in `tearDown()`
4. Test complete CRUD lifecycles
5. Add edge case tests
6. Update this documentation

## Resources

- [WildDuck API Documentation](https://docs.wildduck.email/api/)
- [kurbar/wildduck-test-server](https://github.com/kurbar/wildduck-test-server)
- [PHPUnit Documentation](https://phpunit.de/)
