# Migration Guide: Resource-based to DTO-based System

## Overview

This library has been refactored from a dynamic Resource-based system to a type-safe DTO (Data Transfer Object) based system. This migration guide will help you update your code to work with the new architecture.

## Latest Changes (v3.0.0)

### DTO Trait Refactoring

Many DTOs now use shared traits for common fields. This is an implementation detail and doesn't affect usage, but constructor signatures may have additional parameters grouped together.

### Removed Unnecessary Parameters

The following service methods no longer accept unused `$params` parameters:

- `UserService::recalculateQuota(string $id, array|null $opts = null)` - removed `$params`
- `UserService::recalculateAllUserQuotas(array|null $opts = null)` - removed `$params`
- `AddressService::deleteForwarded(string $address, array|null $opts = null)` - removed `$params`
- `AddressService::getForwarded(string $address, array|null $opts = null)` - removed `$params`

**Before:**
```php
$client->users()->recalculateQuota('user_id', [], $opts);
$client->addresses()->deleteForwarded('address', [], $opts);
```

**After:**
```php
$client->users()->recalculateQuota('user_id', $opts);
$client->addresses()->deleteForwarded('address', $opts);
```

### New Services

The following services have been added:

- `HealthService` - Health check operations
- `SettingsService` - Server settings management
- `DomainAccessService` - Domain allow/blocklist management
- `ExportService` - Data export/import operations
- `CertsService` - TLS certificate management

**Usage:**
```php
// Health check
$health = $client->health()->check();

// Settings
$settings = $client->settings()->all();
$setting = $client->settings()->get('key');

// Domain access
$client->domainAccess()->createAllowedDomain('tag', $dto);
$allowed = $client->domainAccess()->getAllowedDomains('tag');

// Export/Import
$export = $client->export()->export($dto);
$import = $client->export()->import();

// Certificates
$certs = $client->certs()->all();
$cert = $client->certs()->get('cert_id');
```

### New Methods in Existing Services

**UserService:**
- `resolveUsername(string $username)` - Resolve username to user ID

**AuthenticationService:**
- `invalidateToken()` - New preferred method (replaces `invalidate()` which is now deprecated)

### DTO Type Changes

Some DTOs had their field types corrected for better type safety:

- Filter DTOs: `metaData` changed from `mixed` to `?array`
- Message DTOs: `metaData` changed from `mixed` to `?array` where applicable

## Breaking Changes Summary

### 1. Service Access Methods

**Before (magic properties):**
```php
$client = new WildduckClient(['access_token' => 'your_token']);
$users = $client->users->all(); // Magic property access
```

**After (explicit methods):**
```php
$client = new WildduckClient(['access_token' => 'your_token']);
$users = $client->users()->all(); // Method call
```

### 2. Return Types

**Before (dynamic Resource objects):**
```php
$user = $client->users->get('user_id');
echo $user->username; // Dynamic property access
echo $user['username']; // Array access
```

**After (strongly typed DTOs):**
```php
$user = $client->users()->get('user_id'); // Returns UserDto
echo $user->username; // Readonly property with IDE autocomplete
// Array access no longer supported
```

### 3. Creating/Updating Resources

**Before (associative arrays):**
```php
$user = $client->users->create([
    'username' => 'john@example.com',
    'password' => 'secret123',
    'name' => 'John Doe'
]);
```

**After (DTOs):**
```php
use Zone\Wildduck\Dto\User\CreateUserDto;

$user = $client->users()->create(new CreateUserDto(
    username: 'john@example.com',
    password: 'secret123',
    name: 'John Doe'
));
```

### 4. Pagination

**Before (Collection object):**
```php
$users = $client->users->all(['limit' => 20]);
foreach ($users->data as $user) {
    echo $user->username;
}
echo $users->total;
```

**After (PaginatedResultDto with generics):**
```php
$users = $client->users()->all(['limit' => 20]); // Returns PaginatedResultDto<UserDto>
foreach ($users->results as $user) {
    echo $user->username; // Full type safety
}
echo $users->total;
```

## Service-by-Service Migration

### UserService

```php
// Before
$users = $client->users->all();
$user = $client->users->get('user_id');
$newUser = $client->users->create(['username' => 'test@example.com', 'password' => 'pass']);
$client->users->update('user_id', ['name' => 'New Name']);
$client->users->delete('user_id');

// After
use Zone\Wildduck\Dto\User\{CreateUserDto, UpdateUserDto};

$users = $client->users()->all(); // PaginatedResultDto<UserDto>
$user = $client->users()->get('user_id'); // UserDto
$newUser = $client->users()->create(new CreateUserDto(
    username: 'test@example.com',
    password: 'pass'
));
$client->users()->update('user_id', new UpdateUserDto(name: 'New Name'));
$client->users()->delete('user_id'); // DeleteUserResponseDto
```

### MailboxService

```php
// Before
$mailboxes = $client->users->get('user_id')->mailboxes(['specialUse' => true]);
$mailbox = $client->users->mailbox('user_id', 'mailbox_id');

// After
use Zone\Wildduck\Dto\Mailbox\CreateMailboxDto;

$mailboxes = $client->mailboxes()->all('user_id', ['specialUse' => true]); // PaginatedResultDto<MailboxDto>
$mailbox = $client->mailboxes()->get('user_id', 'mailbox_id'); // MailboxDto
$client->mailboxes()->create('user_id', new CreateMailboxDto(path: 'Custom Folder'));
```

### MessageService

```php
// Before
$messages = $client->users->mailbox('user_id', 'mailbox_id')->messages(['limit' => 50]);
$message = $client->messages->get('user_id', 'message_id');

// After
use Zone\Wildduck\Dto\Message\{SearchMessagesDto, SendMessageDto};

$messages = $client->messages()->search('user_id', new SearchMessagesDto(
    mailbox: 'mailbox_id',
    limit: 50
)); // PaginatedResultDto<MessageDto>

$message = $client->messages()->get('user_id', 'message_id'); // MessageDto

// Sending messages
$client->messages()->send('user_id', new SendMessageDto(
    to: [['address' => 'recipient@example.com', 'name' => 'Recipient']],
    subject: 'Test Email',
    text: 'Hello World'
));
```

### AddressService

```php
// Before
$addresses = $client->users->get('user_id')->addresses();
$address = $client->addresses->create('user_id', [
    'address' => 'alias@example.com',
    'main' => false
]);

// After
use Zone\Wildduck\Dto\Address\CreateAddressDto;

$addresses = $client->addresses()->all('user_id'); // PaginatedResultDto<AddressDto>
$address = $client->addresses()->create('user_id', new CreateAddressDto(
    address: 'alias@example.com',
    main: false
)); // AddressInfoDto
```

### FilterService

```php
// Before
$filters = $client->users->get('user_id')->filters();
$filter = $client->filters->create('user_id', [
    'name' => 'Spam Filter',
    'query' => ['from' => 'spam@example.com'],
    'action' => ['delete' => true]
]);

// After
use Zone\Wildduck\Dto\Filter\{CreateFilterDto, FilterQueryDto, FilterActionDto};

$filters = $client->filters()->all('user_id'); // array<FilterDto>

$filter = $client->filters()->create('user_id', new CreateFilterDto(
    name: 'Spam Filter',
    query: new FilterQueryDto(from: 'spam@example.com'),
    action: new FilterActionDto(delete: true)
)); // FilterInfoDto
```

### AuthenticationService

```php
// Before
$auth = $client->authentication->authenticate([
    'username' => 'user@example.com',
    'password' => 'password123',
    'scope' => 'master'
]);

// After
use Zone\Wildduck\Dto\Authentication\AuthenticateDto;

$auth = $client->authentication()->authenticate(new AuthenticateDto(
    username: 'user@example.com',
    password: 'password123',
    scope: 'master'
)); // AuthenticationResultDto
```

## Type Safety Benefits

### 1. IDE Autocomplete

DTOs provide full IDE autocomplete support:
```php
$user = $client->users()->get('user_id');
$user-> // IDE shows: id, username, name, address, enabled, disabled, suspended, etc.
```

### 2. PHPStan/Psalm Static Analysis

```php
/** @var UserDto $user */
$user = $client->users()->get('user_id');
// PHPStan knows the exact type and can catch errors at analysis time
```

### 3. Named Constructor Parameters

```php
// Clear and self-documenting
$user = new CreateUserDto(
    username: 'john@example.com',
    password: 'secret',
    name: 'John Doe',
    language: 'en',
    retention: 30
);

// PHP will error on typos or missing required parameters
```

## Handling Optional Parameters

### Before:
```php
$params = [];
if ($name) $params['name'] = $name;
if ($language) $params['language'] = $language;
$user = $client->users->update('user_id', $params);
```

### After:
```php
$user = $client->users()->update('user_id', new UpdateUserDto(
    name: $name,        // null is allowed for optional fields
    language: $language
));
```

## Error Handling

Error handling remains the same:
```php
use Zone\Wildduck\Exception\{
    RequestFailedException,
    ValidationException,
    AuthenticationFailedException
};

try {
    $user = $client->users()->get('invalid_id');
} catch (RequestFailedException $e) {
    echo "Request failed: " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage();
}
```

## Deleted Classes

The following classes have been removed:

### Resource Classes (src/Resource/)
- `ApiResource`
- `User`
- `Message`
- `Mailbox`
- `Address`
- `Filter`
- `Autoreply`
- `Webhook`
- `ApplicationPassword`
- `Dkim`
- `DomainAlias`
- `ForwardedAddress`
- `File`
- `Attachment`
- `AuthenticationResult`

### Base Classes
- `WildduckObject` - Base dynamic object class
- `Collection` / `Collection2` - Old pagination classes
- `ErrorObject` - Old error representation

### Entity Classes (moved to DTOs)
- `Audit`
- `Event`
- `Quota`
- `UserLimits`
- `ApplicationPasswordLastUse`
- `FilterAction`
- `FilterQuery`
- `ForwardedAddressLimits`
- `KeyInfo`
- `MailingList`
- `Outbound`
- `OutboundQueueEntry`
- `Recipient`

## DTO Namespace Structure

All DTOs are organized by feature:

```
src/Dto/
├── ResponseDtoInterface.php
├── RequestDtoInterface.php
├── PaginatedResultDto.php
├── User/
│   ├── UserDto.php
│   ├── UserInfoDto.php
│   ├── CreateUserDto.php
│   ├── UpdateUserDto.php
│   ├── DeleteUserResponseDto.php
│   └── ...
├── Message/
│   ├── MessageDto.php
│   ├── SearchMessagesDto.php
│   ├── SendMessageDto.php
│   └── ...
├── Mailbox/
│   ├── MailboxDto.php
│   ├── CreateMailboxDto.php
│   └── ...
├── Address/
│   ├── AddressDto.php
│   ├── CreateAddressDto.php
│   └── ...
├── Filter/
│   ├── FilterDto.php
│   ├── FilterQueryDto.php
│   ├── FilterActionDto.php
│   └── ...
└── ... (other features)
```

## Testing Your Migration

1. **Update service calls to use methods instead of properties:**
   ```bash
   # Find all usages
   grep -r '\$client->[a-z]*->' your-code/
   # Replace with method calls
   sed -i 's/\$client->\([a-z]*\)->/\$client->\1()->/g' your-code/*.php
   ```

2. **Update array parameters to DTOs:**
   - Find all `create()`, `update()`, etc. calls
   - Replace arrays with corresponding DTO objects
   - Use named parameters for clarity

3. **Update property access:**
   - Replace `$obj['key']` with `$obj->key`
   - Use actual property names (check DTO class for available properties)

4. **Run static analysis:**
   ```bash
   vendor/bin/phpstan analyze
   ```

5. **Test your application thoroughly** - the type system will catch many errors at runtime

## Need Help?

- Check the DTO class files in `src/Dto/` for available properties and types
- All DTOs use readonly properties with type hints
- Use your IDE's autocomplete to discover available fields
- See `tests/` directory for usage examples

## Benefits of This Migration

1. **Type Safety**: Catch errors at development time, not runtime
2. **IDE Support**: Full autocomplete and inline documentation
3. **Performance**: No more dynamic property resolution
4. **Maintainability**: Clear contracts between client and API
5. **Documentation**: DTOs serve as living documentation of the API
6. **Refactoring**: Safe refactoring with IDE support
