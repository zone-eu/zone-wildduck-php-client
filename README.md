# Wildduck API PHP Client

A PHP client for the [Wildduck email server](https://github.com/nodemailer/wildduck)
API to simplify communicating with the Wildduck e-mail server on PHP applications.

Heavily inspired by [stripe/stripe-php](https://github.com/stripe/stripe-php).

## Requirements

* PHP 8.3 or newer

## Installation

Require Wildduck PHP client via Composer
```bash
composer require zone-eu/wildduck-php-client
```


## Configuration options

**ENV variables:**
- WDPC_REQUEST_LOGGING (true/false) - Enable/Disable logging of request params
- WDPC_REQUEST_LOGGING_FOLDER_PERMISSIONS (0755) - The permissions given to the generated folders
- WDPC_REQUEST_LOGGING_PATTERN - Absolute url RegEx to match requests that need logging
- WDPC_REQUEST_LOGGING_DIRECTORY - The directory in which to save the logs.
  - This is the base directory absolute path and under it extra folders will be created
  - Subdirectory created will be "YYYY-MM-DD-HH"
  - And under it every request will create a file
    - File naming scheme "the request type (GET, POST, etc.)"-"the users WD id"-"a random generated string"

## Usage
WIP

## Contributing
WIP

## License
[EUPL-1.2](https://github.com/zone-eu/zone-wildduck-php-client/blob/master/LICENCE)

