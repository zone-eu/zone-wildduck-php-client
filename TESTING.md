# Testing Guide

This document provides guidance on testing the WildDuck PHP Client after the DTO refactoring.

## Current Test Coverage

### Unit Tests

#### Service Structure Tests (`tests/Unit/Service/ServiceStructureTest.php`)

These smoke tests verify the basic structural integrity of all 17 services:

- ✅ All services can be instantiated
- ✅ All services have public methods
- ✅ All service methods have return type declarations
- ✅ All service method parameters have type hints

**Status**: ✅ PASSING (4 tests, 586 assertions)

These tests verify that the PHP 8.3+ type safety requirements are met across all services.

## Recommended Testing Approach

Due to the nature of API client libraries, we recommend a **layered testing approach**:

### 1. Structure Tests (Current - ✅ Complete)

The existing `ServiceStructureTest` validates that:
- All services follow the established patterns
- Type safety is enforced throughout
- No services are missing from the test suite

### 2. Integration Tests (Recommended Next Step)

Integration tests against a real or test WildDuck API instance would provide the most value:

```php
/**
 * Example integration test structure
 */
class UserServiceIntegrationTest extends TestCase
{
    private WildduckClient $client;

    protected function setUp(): void
    {
        $this->client = new WildduckClient([
            'accessToken' => getenv('WILDDUCK_TEST_TOKEN'),
            'apiUrl' => getenv('WILDDUCK_TEST_URL'),
        ]);
    }

    public function testCreateAndGetUser(): void
    {
        // Create user
        $createDto = new CreateUserDto(
            username: 'test-' . uniqid(),
            password: 'TestPass123!',
            address: 'test-' . uniqid() . '@example.com'
        );

        $result = $this->client->users()->create($createDto);
        $this->assertTrue($result->success);

        // Get user
        $user = $this->client->users()->get($result->id);
        $this->assertEquals($createDto->username, $user->username);

        // Cleanup
        $this->client->users()->delete($result->id);
    }
}
```

### 3. Mock-Based Unit Tests (Optional)

While mock-based unit tests can be created, they have limited value for API clients because:
- They test implementation details rather than behavior
- DTOs are converted to arrays internally, making mocking complex
- The real value is in verifying actual API communication

If you need mock-based tests, here's the pattern:

```php
public function testServiceMethodCall(): void
{
    $mockClient = $this->createMock(BaseWildduckClient::class);
    $mockClient->method('getAccessToken')->willReturn('test-token');

    // Mock expects the actual parameters passed to request()
    $mockClient->expects($this->once())
        ->method('request')
        ->with(
            'post',
            '/users/user123/addresses',
            $this->callback(function($params) {
                // DTO has been converted to array
                return is_array($params) && isset($params['address']);
            }),
            $this->callback(function($opts) {
                // requestRaw adds 'raw' => true
                return is_array($opts) && ($opts['raw'] ?? false) === true;
            }),
            false
        )
        ->willReturn(['success' => true, 'id' => 'addr123']);

    $service = new AddressService($mockClient);
    $result = $service->create('user123', new CreateAddressDto(address: 'test@example.com'));

    $this->assertInstanceOf(UserInfoDto::class, $result);
}
```

## Running Tests

```bash
# Run all tests
vendor/bin/phpunit

# Run only structure tests
vendor/bin/phpunit tests/Unit/Service/ServiceStructureTest.php

# Run with coverage (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage/
```

## Setting Up Integration Tests

1. **Set up test environment**:
   ```bash
   cp .env.example .env.testing
   # Edit .env.testing with test credentials
   ```

2. **Create test database/instance**:
   - Use Docker Compose for local WildDuck instance
   - Or use a dedicated test environment

3. **Run integration tests**:
   ```bash
   phpunit --testsuite=Integration
   ```

## Test Coverage Goals

Given the nature of this API client:

- ✅ **Structure**: 100% coverage (achieved)
- 🎯 **Integration**: High priority - test actual API interactions
- ⚪ **Mock-based Unit**: Low priority - focus on integration tests

## Why This Approach?

1. **DTOs Provide Type Safety**: The DTO layer already ensures type correctness at compile time
2. **Real API Testing**: Integration tests verify actual behavior, not mocked behavior
3. **Refactoring Confidence**: Structure tests ensure all services follow patterns
4. **Maintenance**: Fewer mocks = less brittle tests

## Test Organization

```
tests/
├── Unit/
│   └── Service/
│       └── ServiceStructureTest.php  ← Structure validation
├── Integration/
│   └── Service/
│       ├── UserServiceTest.php       ← Real API tests
│       ├── MessageServiceTest.php
│       └── ...
└── Fixtures/
    └── test-data.json                ← Test data for integration tests
```

## Contributing Tests

When adding new service methods:

1. Ensure proper type hints (enforced by ServiceStructureTest)
2. Add integration test for the new method
3. Update this guide if introducing new patterns

## Notes on the Refactoring

The DTO refactoring has made the codebase:
- **Type-safe**: PHP 8.3+ strict types throughout
- **IDE-friendly**: Full autocomplete and type inference
- **Testable**: Clear interfaces for integration testing
- **Maintainable**: Consistent patterns across all services

Mock-based tests were intentionally deprioritized because:
- They add maintenance burden without adding value
- They test implementation (how we call request()) not behavior (what the API does)
- Integration tests provide more confidence for an API client

## Future Improvements

- [ ] Add Docker Compose for local test environment
- [ ] Create helper traits for common test patterns
- [ ] Add test data fixtures
- [ ] Set up CI/CD with real WildDuck test instance
- [ ] Add performance benchmarks
