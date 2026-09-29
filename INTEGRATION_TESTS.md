# Integration Tests Guide

This guide explains how to run integration tests against a real WildDuck test server.

## Prerequisites

- Docker installed and running
- PHP 8.3+
- Composer dependencies installed (`composer install` — the test server ships as the dev dependency `kurbar/wildduck-test-server`)

## Quick Start

### 1. Start the Test Server

```bash
vendor/bin/wildduck-test-server start 30
```

The optional second argument is the number of seconds to wait for the server
to become responsive (default 15; use a higher value on slow machines).

This will:
- Pull the WildDuck, Redis, and MongoDB images
- Create the `wdtest` Docker network
- Start all containers with proper networking
- Wait for the API to respond

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
vendor/bin/wildduck-test-server stop
```

This will remove all containers and clean up the Docker network.

## Test Server Details

### Containers

- **wdt_redis**: `redis:alpine` on network `wdtest`
- **wdt_mongo**: `mongo` (unpinned) on network `wdtest`
- **wdt_wildduck**: `ghcr.io/zone-eu/wildduck:latest` on network `wdtest`

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
vendor/bin/wildduck-test-server stop
docker system prune -f

# Try starting again
vendor/bin/wildduck-test-server start 30
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

Port mappings (9080, 9143, 9110, 9993, 9995) are fixed by the
`kurbar/wildduck-test-server` package. If 9080 is already in use, stop the
other service or file an issue against the package; after updating the
package, also update the `API_URL` in `tests/Integration/IntegrationTestCase.php`.

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
        run: vendor/bin/wildduck-test-server start 30

      - name: Run integration tests
        run: vendor/bin/phpunit --testsuite=Integration

      - name: Stop test server
        if: always()
        run: vendor/bin/wildduck-test-server stop
```

## Known Issues

### Vendored `kurbar/wildduck-test-server` (0.8.x)

The currently released helper has a few rough edges:

1. Images are pulled unpinned (`mongo`, `ghcr.io/zone-eu/wildduck:latest`), so behaviour can drift over time.
2. Redis and MongoDB use host bind mounts (`-v /data`, `-v /data/db`) instead of named volumes.

A fixed release (pinned MongoDB image, named volumes) is pending on Packagist.
Until it lands, bump `kurbar/wildduck-test-server` in `composer.json` and
re-run `composer update kurbar/wildduck-test-server` after the release.
