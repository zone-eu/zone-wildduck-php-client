<?php

require_once __DIR__ . '/vendor/autoload.php';

use Zone\Wildduck\WildduckClient;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\Authentication\AuthenticateRequestDto;

$client = new WildduckClient([
    'api_base' => 'http://localhost:8080',
    'access_token' => 'secret-token',
]);

// Create a test user
$username = 'testuser_' . uniqid();
$password = 'TestPassword123!';
$email = 'test_' . uniqid() . '@example.com';

$createUserDto = new CreateUserRequestDto(
    username: $username,
    password: $password,
    address: $email
);

try {
    $userResult = $client->users()->create($createUserDto);
    echo "User created: {$userResult->id}\n";
    
    // Authenticate
    $authDto = new AuthenticateRequestDto(
        username: $username,
        password: $password,
        scope: 'master'
    );
    
    $authResult = $client->authentication()->authenticate($authDto);
    echo "Token: {$authResult->token}\n";
    
    // Create client with user token
    $userClient = new WildduckClient([
        'api_base' => 'http://localhost:8080',
        'access_token' => $authResult->token,
    ]);
    
    // Invalidate token
    echo "Invalidating token...\n";
    $result = $userClient->authentication()->invalidateToken();
    
    echo "Result: ";
    var_dump($result);
    
    // Cleanup
    $client->users()->delete($userResult->id);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
