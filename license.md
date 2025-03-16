# comphp/extensible

A **lightweight, modular extensibility framework** for PHP applications. It provides a structured way to register, manage, and instantiate **extensions**—using PHP attributes for metadata and enforcing dependency resolution, singleton behavior, and interface requirements where needed.

---

## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Getting Started](#getting-started)
  - [1. Define an Extension Type](#1-define-an-extension-type)
  - [2. Register and Use Extensions](#2-register-and-use-extensions)
- [Advanced Usage](#advanced-usage)
  - [Dependency Injection](#dependency-injection)
  - [Event-Driven Extension Instantiation](#event-driven-extension-instantiation)
  - [Custom Instantiation](#custom-instantiation)
- [Examples](#examples)
- [Testing](#testing)
- [Contributing](#contributing)
- [Code of Conduct](#code-of-conduct)
- [License](#license)

---

## Features
- **Attribute-Based Extension Types**  
  Register custom extension types using PHP attributes (e.g. `#[ExtensionType]`).
- **Singleton or Multi-Instance**  
  Enforce singleton behavior or allow multiple instances per extension type.
- **Dependency Checking**  
  Automatically detect missing dependencies and throw descriptive exceptions.
- **Interface Enforcement**  
  Optionally require an interface for all extensions of a certain type.
- **Event Integration**  
  Supports dispatching an event whenever an extension is instantiated.
- **PSR Friendly**  
  Uses `psr/log` for logging and `psr/event-dispatcher` for event handling.

---

## Installation
Install via [Composer](https://getcomposer.org/):

```bash
composer require comphp/extensible
```

> **Requirements**: PHP 8.4 or later, plus PSR-4 autoloading.

---

## Getting Started

### 1. Define an Extension Type
Create a class **extending** `AbstractExtension` and annotate it with `#[ExtensionType]`. This makes the class an **extension type**.
```php
use Neuron\Extensibility\AbstractExtension;
use Neuron\Extensibility\ExtensionType;

#[ExtensionType(singleton: false, allowPreloading: false)]
class MyExtensionType extends AbstractExtension
{
    // Extension metadata is set via the constructor if needed
}
```
**Parameters** for `#[ExtensionType]`:
- **singleton** (bool): Whether each extension of this type is a singleton.
- **allowPreloading** (bool): If true (and singleton is true), preloads extension upon registration.
- **requireInterface** (string|null): An optional interface that all extensions of this type must implement.

### 2. Register and Use Extensions
Use `ExtensionStore` to manage extension types and their concrete implementations:

```php
use Neuron\Extensibility\ExtensionStore;
use Neuron\Extensibility\InstantiatorInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

// Create an ExtensionStore
$instantiator = new MyInstantiator();
$logger = new MyLogger();
$eventDispatcher = new MyEventDispatcher();

$store = new ExtensionStore($instantiator, $logger, $eventDispatcher);

// Register the extension type
$store->typeRegistry->register(MyExtensionType::class);

// Register concrete classes that belong to MyExtensionType
$store->registry->register(MyExtensionType::class, ConcreteImplementationA::class);
$store->registry->register(MyExtensionType::class, ConcreteImplementationB::class);

// Retrieve a singleton or new instance
$instanceA = $store->get(ConcreteImplementationA::class);
$instanceB = $store->create(ConcreteImplementationB::class);
```

---

## Advanced Usage

### Dependency Injection
If an extension **requires** dependencies (e.g. a logger), specify them in the constructor.
```php
#[ExtensionType(singleton: false, allowPreloading: false, requireInterface: CacheDriverInterface::class)]
class CacheDriver extends AbstractExtension {}

interface CacheDriverInterface {
  public function get(string $key): mixed;
  public function set(string $key, mixed $value): void;
}

#[CacheDriver('redis')]
class RedisCache implements CacheDriverInterface {
  public function __construct(private LoggerInterface $logger) {}

  public function get(string $key): mixed {
    $this->logger->info("Fetching key: {$key}");
    return null;
  }

  public function set(string $key, mixed $value): void {
    $this->logger->info("Setting key: {$key}");
  }
}
```
> If the extension depends on another **extension class**, list it in the `$dependencies` array in your `AbstractExtension` constructor. The store will throw `MissingDependenciesException` if not all dependencies are registered.

### Event-Driven Extension Instantiation
You can respond to PSR-14 events to load or create extensions **only when needed**:
```php
$eventDispatcher->listen(UserRegisteredEvent::class, function ($event) use ($store) {
  $mailer = $store->get(SMTPMailer::class); // if it’s singleton
  $mailer->sendWelcomeEmail($event->username);
});
```

### Custom Instantiation
Implement `InstantiatorInterface` to customize how objects are created (e.g. using a DI container, reflection, etc.).
```php
class MyInstantiator implements InstantiatorInterface {
  public function instantiate(string $className, array $parameters): object {
    // Use reflection, a container, or direct calls
    return new $className(...$parameters);
  }
}
```

---

## Examples
Check the [`examples/`](examples) directory for detailed snippets:
1. **[`basic-usage.php`](examples/basic-usage.php)**
2. **[`dependency-injection.php`](examples/dependency-injection.php)**
3. **[`event-driven-usage.php`](examples/event-driven-usage.php)**
4. **[`payment-gateways.php`](examples/payment-gateways.php)**

Each demonstrates a different pattern or scenario, including **singleton** vs. **multi-instance**, **dependency injection**, and **event-driven** instantiation.

---

## Testing
This library includes a comprehensive test suite powered by [PHPUnit](https://phpunit.de/). To run the tests:
```bash
composer install
vendor/bin/phpunit
```
You’ll see coverage for:
- **`ExtensionTypeRegistry`** & **`ExtensionRegistry`**
- **`ExtensionStore`** (instantiation, dependencies, singletons)
- **`AbstractExtension`** & **`Extension`** wrapper logic

---

## Contributing
We welcome contributions and feature requests! Please see [CONTRIBUTING.md](contributing.md) for details on how to submit pull requests and the project’s code guidelines.

---

## Code of Conduct
This project follows a [Code of Conduct](code_of_conduct.md) to ensure a welcoming environment. Please review it before participating.

---

## License
This project is released under the [MIT License](license.md). See the `license.md` file for full details.