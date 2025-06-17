# DevPayr PHP SDK

A lightweight and developer-friendly PHP SDK for integrating the [DevPayr](https://devpayr.com) platform — allowing you to validate license keys, fetch injectables, manage projects, control domains, enforce payments, and more.

Designed for software creators, SaaS founders, and digital product developers who want to secure and monetize their code with minimal effort.

## 🔰 Introduction

DevPayr is a modern software licensing and payment enforcement platform built to help developers protect their code, enforce payment, and control how their software is used — across PHP, JavaScript, desktop, and SaaS environments.

This SDK enables seamless integration with DevPayr directly from your PHP application. Whether you're selling a script, distributing software, or building a SaaS, this SDK gives you the tools to:

- Validate license keys and enforce runtime restrictions
- Download and decrypt injectables (e.g., encrypted config or code blobs)
- Interact with your DevPayr projects, licenses, and domains
- Enforce project payment before allowing usage

### 🚀 Why Use DevPayr SDK?

Manually enforcing license checks, domain locks, and payment validation can be error-prone, time-consuming, and easily bypassed.

The DevPayr SDK solves this with:

✅ **One-Line Bootstrapping** – Validate license, check payment, auto-handle injectables in a single call  
✅ **Secure Runtime Validation** – Block unauthorized use and unpaid projects at runtime  
✅ **Customizable Failure Modes** – Redirect, log, silently fail, or show a branded modal on license failure  
✅ **Injectable Decryption** – Distribute sensitive data like config, templates, or code snippets securely  
✅ **Developer-Friendly API** – Create, revoke, validate licenses and domains easily from your app  
✅ **Built-in Caching** – Avoid repeated license checks with intelligent cache

Whether you're distributing self-hosted scripts, WordPress plugins, SaaS dashboards, or internal tools — DevPayr SDK brings enforcement, control, and peace of mind.

### 🔧 Features

- **License Verification**  
  Securely validate license keys issued via DevPayr API.

- **Payment Enforcement**  
  Block access to features or full app when payment is not completed.

- **Injectables Support**  
  Automatically download, decrypt, and store encrypted files (templates, configs, snippets) tied to the license.

- **Domain Restriction**  
  Verify if a request or instance is coming from an approved domain or subdomain.

- **Runtime Enforcement**  
  Stop bootstrapping or execution on invalid/unpaid licenses with flexible fallback strategies (modal, redirect, log, silent).

- **Extensible Processor Hooks**  
  Customize how injectables are handled via your own class (e.g., save to S3, memory, etc).

- **Lightweight & Framework-Agnostic**  
  Built in pure PHP without framework dependencies. Works in Laravel, WordPress, or any PHP app.

- **Fully Typed & Exception-Driven**  
  Catch invalid responses, network failures, and API violations with structured exceptions.

- **Built-in Caching**  
  Automatically caches daily validation results in the OS temp folder to improve performance.

- **Custom Messaging & Views**  
  Show your own branded modal or custom message when a license is invalid.

### 📦 Installation

#### Install via Composer

To install the DevPayr PHP SDK in your project, run the following command:

```bash
composer require xultech/devpayr-php-sdk
```
This will pull in the latest version of the SDK from Packagist and make it available for use in your PHP application.
#### 📋 Minimum Requirements

Before installing the SDK, ensure your environment meets the following requirements:

- PHP **8.1** or higher
- Composer **2.0+**
- **OpenSSL** extension enabled (used for secure decryption)
- Either `cURL` **or** `allow_url_fopen` (for HTTP requests)

## ⚡ Quick Start

The DevPayr PHP SDK is designed for simple drop-in usage.

Below is a minimal working example that:

1. Validates the license
2. Checks if the project is paid
3. Fetches and handles any associated injectables

```php
<?php

require_once 'vendor/autoload.php';

use DevPayr\DevPayr;
use DevPayr\Exceptions\DevPayrException;

try {
    DevPayr::bootstrap([
        'license'        => 'YOUR-LICENSE-KEY',
        'injectables'    => true,
        'invalidBehavior'=> 'modal', // Options: modal, redirect, silent, log
    ]);
    
    // Your protected logic continues here...
    echo "License valid. Project is paid.";
} catch (DevPayrException $e) {
    // Handle critical exceptions
    echo " DevPayr error: " . $e->getMessage();
}
```

> This will automatically:
> - Validate the license
> - Check for active payment
> - Download and decrypt injectables (if enabled)
> - Handle failures based on your configuration

## 🛠 Configuration Options

The `DevPayr::bootstrap()` method accepts a flexible configuration array that tailors how validation, payment enforcement, and injectables are handled.

### ✅ Required Keys

| Key        | Type   | Description                                                                             |
|------------|--------|-----------------------------------------------------------------------------------------|
| `base_url` | string | The base API URL (defaults to `https://api.devpayr.com/api/v1/`)                        |
| `secret`   | string | The secret key used to decrypt injectables (this is created when you add an injectable) |

---

### 🧰 Optional Keys (with Defaults)

| Key                      | Type     | Default       | Description |
|---------------------------|----------|---------------|-------------|
| `recheck`                 | bool     | `true`        | Revalidate license on every load (false enables caching) |
| `injectables`            | bool     | `true`        | Whether to fetch injectables from the server |
| `injectablesVerify`      | bool     | `true`        | Verify HMAC signature on injectables |
| `injectablesPath`        | string\|null | `null`     | Directory where injectables are saved (default: system temp path) |
| `invalidBehavior`        | string   | `'modal'`     | Behavior on invalid license: `modal`, `redirect`, `log`, `silent` |
| `redirectUrl`            | string\|null | `null`     | Redirect URL on invalid license (used when `invalidBehavior = redirect`) |
| `timeout`                | int      | `1000`        | Request timeout in milliseconds |
| `action`                 | string   | `'check_project'` | Action passed to DevPayr (for tracking/analytics) |
| `onReady`                | callable\|null | `null`   | Callback function executed on successful license validation |
| `handleInjectables`      | bool     | `false`       | If `true`, SDK will auto-process injectables into your file system |
| `injectablesProcessor`   | string\|null | `null`     | Fully qualified class name to handle injectable processing manually |
| `customInvalidView`      | string\|null | `null`     | Absolute path to your custom HTML view to show on license failure |
| `customInvalidMessage`   | string   | `'This copy is not licensed for production use.'` | Message displayed on failure when no view is provided |
| `license`                | string\|null | `null`     | License key for project-scoped validation |
| `api_key`                | string\|null | `null`     | API key (global or project scoped) for backend operations |
| `per_page`               | int\|null | `null`       | Number of items to return for paginated results (used in services) |

---

> 🔒 You only need to set what's relevant to your use-case. Defaults will handle most basic setups.

