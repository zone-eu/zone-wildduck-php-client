# Wildduck API PHP Client

A modern PHP client for the [Wildduck email server](https://github.com/nodemailer/wildduck) API with full type safety and DTO support.

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue)](https://php.net)
[![License](https://img.shields.io/badge/License-EUPL--1.2-green)](LICENSE)

## Features

- 🔒 **Type-Safe**: Full PHP 8.3+ type hints with strict types
- 📦 **DTOs**: Request and response Data Transfer Objects for IDE autocomplete
- 🎯 **Modern PHP**: Readonly properties, named parameters, union types
- 🔄 **EventSource**: Real-time updates via server-sent events
- 📚 **Comprehensive**: All 17 WildDuck API services covered

## Requirements

* PHP 8.3 or newer
* Composer

## Installation

```bash
composer require zone-eu/wildduck-php-client
```

## Quick Start

```php
use Zone\Wildduck\WildduckClient;
use Zone\Wildduck\Dto\User\CreateUserDto;

// Initialize client
$client = new WildduckClient([
    'accessToken' => 'your-api-token',
    'apiUrl' => 'https://api.wildduck.email',
]);

// Create a user
$createDto = new CreateUserDto(
    username: 'john.doe',
    password: 'SecurePass123!',
    address: 'john.doe@example.com',
    name: 'John Doe'
);

$result = $client->users()->create($createDto);
echo "User created with ID: {$result->id}\n";

// Get user details
$user = $client->users()->get($result->id);
echo "Username: {$user->username}\n";
echo "Email: {$user->address}\n";

// Update user
$updateDto = new UpdateUserDto(
    name: 'John Updated Doe'
);
$client->users()->update($result->id, $updateDto);

// Delete user
$client->users()->delete($result->id);
```

## Available Services

All services are accessed via the `WildduckClient` instance:

- `$client->users()` - User management
- `$client->addresses()` - Email address management
- `$client->mailboxes()` - Mailbox operations
- `$client->messages()` - Message handling
- `$client->filters()` - Email filters
- `$client->autoreply()` - Auto-reply settings
- `$client->applicationPasswords()` - App-specific passwords
- `$client->authentication()` - Authentication endpoints
- `$client->twoFactorAuthentication()` - 2FA management
- `$client->archive()` - Message archiving
- `$client->audit()` - Audit logs
- `$client->dkim()` - DKIM key management
- `$client->domainAliases()` - Domain alias operations
- `$client->events()` - Event streaming
- `$client->storage()` - File storage
- `$client->submission()` - Message submission
- `$client->webhooks()` - Webhook management

See [MIGRATION.md](MIGRATION.md) for complete migration guide from v1.x.

## Examples

### Managing Messages

```php
use Zone\Wildduck\Dto\Message\UploadMessageDto;

// Upload a message
$uploadDto = new UploadMessageDto(
    raw: base64_encode($emailSource),
    mailbox: $mailboxId
);

$result = $client->messages()->upload($userId, $mailboxId, $uploadDto);

// Search messages
$messages = $client->messages()->search($userId, [
    'q' => 'from:sender@example.com',
    'limit' => 10
]);

foreach ($messages->results as $message) {
    echo "Subject: {$message->subject}\n";
}
```

### Working with Filters

```php
use Zone\Wildduck\Dto\Filter\CreateFilterDto;
use Zone\Wildduck\Dto\Shared\FilterQueryDto;
use Zone\Wildduck\Dto\Shared\FilterActionDto;

$createDto = new CreateFilterDto(
    name: 'Spam Filter',
    query: new FilterQueryDto(from: 'spam@example.com'),
    action: new FilterActionDto(delete: true)
);

$client->filters()->create($userId, $createDto);
```

### Event Streaming

```php
// Stream user mailbox updates
$response = $client->events()->forUser($userId);
// Returns StreamedResponse that can be processed with EventSource
```

## Testing

The library includes comprehensive structure tests. See [TESTING.md](TESTING.md) for:
- Running tests
- Writing integration tests
- Test coverage information

```bash
# Run structure tests
vendor/bin/phpunit tests/Unit/Service/ServiceStructureTest.php
```

## Configuration

### Environment Variables

- `WDPC_REQUEST_LOGGING` (true/false) - Enable request logging
- `WDPC_REQUEST_LOGGING_FOLDER_PERMISSIONS` (0755) - Log folder permissions
- `WDPC_REQUEST_LOGGING_PATTERN` - RegEx for requests to log
- `WDPC_REQUEST_LOGGING_DIRECTORY` - Base directory for logs

### Client Options

```php
$client = new WildduckClient([
    'accessToken' => 'your-token',       // Required
    'apiUrl' => 'https://api.example.com', // Optional
    'apiVersion' => 'v1',                 // Optional
    'httpClient' => $customClient,        // Optional custom HTTP client
]);
```

## Architecture

### DTOs (Data Transfer Objects)

All requests and responses use strongly-typed DTOs:

```php
// Request DTOs
use Zone\Wildduck\Dto\User\CreateUserDto;
use Zone\Wildduck\Dto\User\UpdateUserDto;

// Response DTOs
use Zone\Wildduck\Dto\User\UserDto;
use Zone\Wildduck\Dto\User\UserInfoDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
```

DTOs provide:
- ✅ IDE autocomplete
- ✅ Type safety at compile time
- ✅ Clear API contracts
- ✅ Validation support

### Service Layer

Services extend `AbstractService` and provide typed methods:

```php
class UserService extends AbstractService
{
    public function create(CreateUserDto|null $params = null): UserInfoDto
    {
        return $this->requestDto('post', '/users', $params, UserInfoDto::class);
    }

    public function get(string $id): UserDto
    {
        return $this->requestDto('get', "/users/{$id}", null, UserDto::class);
    }
}
```

## Upgrading from v1.x

See [MIGRATION.md](MIGRATION.md) for detailed upgrade instructions. Key changes:

1. **DTOs instead of arrays**: All requests now use DTOs
2. **Service access**: `$client->users()` instead of `$client->users`
3. **Return types**: Typed DTOs instead of generic arrays
4. **PHP 8.3+**: Modern PHP features throughout

## Contributing

Contributions are welcome! Please see our contributing guidelines:

1. Fork the repository
2. Create a feature branch
3. Add tests for new features
4. Ensure all tests pass
5. Submit a pull request

## License

[EUPL-1.2](LICENSE) - European Union Public License

## Resources

- [WildDuck API Documentation](https://docs.wildduck.email/api/)
- [Migration Guide](MIGRATION.md)
- [Testing Guide](TESTING.md)
- [Changelog](CHANGELOG.md)

## Credits

Heavily inspired by [stripe/stripe-php](https://github.com/stripe/stripe-php).

Made with ❤️ by [Zone Media OÜ](https://github.com/zone-eu)

