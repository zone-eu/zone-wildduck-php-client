# Integration Tests Guide

This guide explains how to run integration tests against a real WildDuck test server.

## Prerequisites

- Docker installed and running
- PHP 8.3+
- Composer dependencies installed

## Quick Start

### 1. Start the Test Server

```bash
bin/start-test-server.sh start
```

This will:
- Pull the latest Redis, MongoDB, and WildDuck images
- Start all containers with proper networking
- Wait for services to be ready
- Configure WildDuck with test access token

The server will be available at:
- **API URL**: `http://localhost:9080`
- **Access Token**: `WDTESTSERVER`

### 2. Run Integration Tests

```bash
# Run all integration tests
vendor/bin/phpunit --testsuite=Integration

# Run with detailed output
vendor/bin/phpunit --testsuite=Integration --testdox

# Run specific test class
vendor/bin/phpunit tests/Integration/Service/UserServiceIntegrationTest.php
```

### 3. Stop the Test Server

```bash
bin/start-test-server.sh stop
```

This will remove all containers and clean up the Docker network.

## Test Server Details

### Containers

- **wdt_redis**: Redis 8.x (Alpine) on network `wdtest`
- **wdt_mongo**: MongoDB latest on network `wdtest`
- **wdt_wildduck**: WildDuck latest on network `wdtest`

### Ports

- **9080**: WildDuck API (HTTP)
- **9143**: IMAP
- **9110**: POP3
- **9993**: IMAPS (TLS)
- **9995**: POP3S (TLS)

### Configuration

- MongoDB: `mongodb://wdt_mongo:27017/wildduck`
- Redis: `redis://wdt_redis:6379/3`
- Access Token: `WDTESTSERVER`

## Writing Integration Tests

### Base Class

Extend `IntegrationTestCase` for your integration tests:

```php
use Zone\Wildduck\Tests\Integration\IntegrationTestCase;

class MyServiceIntegrationTest extends IntegrationTestCase
{
    public function testSomething(): void
    {
        // $this->client is already configured
        $result = $this->client->users()->all();

        $this->assertNotNull($result);
    }
}
```

### Helper Methods

The base class provides:

- `$this->client` - Configured WildduckClient instance
- `generateUsername()` - Generate unique test username
- `generateEmail()` - Generate unique test email
- `cleanupUser($userId)` - Clean up test user

### Example Test

```php
public function testUserCreation(): void
{
    $username = $this->generateUsername();
    $email = $this->generateEmail();

    $createDto = new CreateUserDto(
        username: $username,
        password: 'TestPass123!',
        address: $email
    );

    $result = $this->client->users()->create($createDto);

    $this->assertTrue($result->success);
    $this->assertNotEmpty($result->id);

    // Cleanup
    $this->cleanupUser($result->id);
}
```

### Best Practices

1. **Always clean up test data** in `tearDown()` or after test completion
2. **Use unique identifiers** with `generateUsername()` and `generateEmail()`
3. **Handle failures gracefully** - tests may fail if server is not running
4. **Don't rely on test order** - each test should be independent
5. **Use descriptive test names** - `testUserLifecycle` is better than `testUser`

## Troubleshooting

### Test Server Won't Start

```bash
# Check Docker is running
docker ps

# Clean up any stale containers
bin/start-test-server.sh stop
docker system prune -f

# Try starting again
bin/start-test-server.sh start
```

### Tests Are Skipped

If tests show as skipped, the server is not running or not responding:

```bash
# Check if containers are running
docker ps | grep wdt_

# Check WildDuck logs
docker logs wdt_wildduck

# Verify API is responding
curl http://localhost:9080/authenticate
```

### Containers Won't Stop

```bash
# Force remove all containers
docker rm -f wdt_wildduck wdt_mongo wdt_redis

# Remove network
docker network rm wdtest
```

### Port Conflicts

If port 9080 is already in use, modify the port mapping in `bin/start-test-server.sh`:

```bash
-p 9080:8080  # Change 9080 to another port
```

Also update the `API_URL` in `tests/Integration/IntegrationTestCase.php`.

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Integration Tests

on: [push, pull_request]

jobs:
  integration:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - name: Install dependencies
        run: composer install

      - name: Start test server
        run: bin/start-test-server.sh start

      - name: Run integration tests
        run: vendor/bin/phpunit --testsuite=Integration

      - name: Stop test server
        if: always()
        run: bin/start-test-server.sh stop
```

## Known Issues

### Original vendor/kurbar/wildduck-test-server Issues

The original script in `vendor/kurbar/wildduck-test-server/bin/wildduck-test-server` has bugs:

1. Missing `-d` flag on Redis and MongoDB containers (blocks execution)
2. Incorrect volume mounts (`-v /data` instead of `-v wdt_redis_data:/data`)
3. No health checks before starting WildDuck

Our wrapper script `bin/start-test-server.sh` fixes these issues.

## Resources

- [WildDuck Documentation](https://docs.wildduck.email/)
- [WildDuck API Reference](https://docs.wildduck.email/api/)
- [WildDuck GitHub](https://github.com/nodemailer/wildduck)
