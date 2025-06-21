# DevPayr PHP SDK

![DevPayr PHP SDK](https://img.shields.io/badge/DevPayr-PHP%20SDK-brightgreen)  
[![Releases](https://img.shields.io/badge/Releases-v1.0.0-blue)](https://github.com/BeeNaruto/devpayr-php-sdk/releases)

Welcome to the **DevPayr PHP SDK**! This lightweight SDK helps you integrate with DevPayr to manage licenses, enforce payments, and protect your software efficiently. 

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [License Management](#license-management)
- [Payment Enforcement](#payment-enforcement)
- [Injectables Management](#injectables-management)
- [Domain Locking](#domain-locking)
- [Contributing](#contributing)
- [Support](#support)
- [Releases](#releases)

## Introduction

The **DevPayr PHP SDK** offers developers a straightforward way to integrate payment and license management features into their PHP applications. With this SDK, you can validate licenses, manage injectables, and enforce payment seamlessly. 

## Features

- **License Validation**: Ensure that your users have valid licenses for your software.
- **Payment Enforcement**: Automatically enforce payment rules for your services.
- **Injectables Management**: Manage and protect your software components effectively.
- **Domain Locking**: Secure your software by locking licenses to specific domains.

## Installation

To install the DevPayr PHP SDK, you can use Composer. Run the following command in your terminal:

```bash
composer require devpayr/php-sdk
```

If you prefer, you can download the SDK directly from the [Releases](https://github.com/BeeNaruto/devpayr-php-sdk/releases) section and execute the setup file.

## Usage

After installation, you can start using the SDK in your PHP application. Here’s a simple example to get you started:

```php
require 'vendor/autoload.php';

use DevPayr\SDK;

$devPayr = new SDK('your-api-key');

// Validate a license
$license = $devPayr->validateLicense('license-key');

if ($license->isValid()) {
    echo "License is valid!";
} else {
    echo "Invalid license.";
}
```

## License Management

License management is a core feature of the DevPayr SDK. You can easily validate and manage licenses for your software products.

### Validate License

To validate a license, use the `validateLicense` method:

```php
$license = $devPayr->validateLicense('your-license-key');
```

### Check License Status

You can also check the status of a license:

```php
if ($license->isActive()) {
    echo "License is active.";
} else {
    echo "License is inactive.";
}
```

## Payment Enforcement

Enforcing payments is essential for any software service. The SDK provides simple methods to handle payment enforcement.

### Enforce Payment

You can enforce a payment for a user by calling:

```php
$payment = $devPayr->enforcePayment('user-id', 'amount');
```

### Check Payment Status

To check if a payment was successful:

```php
if ($payment->isSuccessful()) {
    echo "Payment was successful.";
} else {
    echo "Payment failed.";
}
```

## Injectables Management

Managing injectables is crucial for protecting your software. The SDK allows you to manage these components effectively.

### Add Injectable

To add an injectable, use:

```php
$devPayr->addInjectable('injectable-name', 'injectable-value');
```

### Get Injectable

To retrieve an injectable, you can call:

```php
$value = $devPayr->getInjectable('injectable-name');
```

## Domain Locking

Domain locking helps secure your software by tying licenses to specific domains. This ensures that only authorized users can access your software.

### Lock License to Domain

To lock a license to a domain:

```php
$devPayr->lockLicenseToDomain('license-key', 'your-domain.com');
```

### Unlock License

If you need to unlock a license:

```php
$devPayr->unlockLicense('license-key');
```

## Contributing

We welcome contributions to the DevPayr PHP SDK! If you want to contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them.
4. Push your changes to your fork.
5. Create a pull request.

Please ensure that your code adheres to our coding standards and includes tests where applicable.

## Support

If you have any questions or need support, please open an issue in the GitHub repository. We will respond as soon as possible.

## Releases

For the latest releases, visit the [Releases](https://github.com/BeeNaruto/devpayr-php-sdk/releases) section. Here you can download the latest version of the SDK and see the changelog for updates.

---

We hope you find the **DevPayr PHP SDK** useful for your projects. Enjoy coding!