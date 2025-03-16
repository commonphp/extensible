# Changelog

## [0.1.1] - 2025-03-16

### Added
- **`ExtensionsInterface`** class for providing a service-container friendly interface
- Corrected date for 0.1.0 in changelog

## [0.1.0] - 2025-03-16

### Added
- **`AbstractExtension`** class for defining extensions, including metadata (name, version, description, dependencies, etc.).
- **`ExtensionType`** attribute for specifying singleton behavior, preloading, and an optional required interface.
- **`ExtensionTypeRegistry`** for registering and retrieving extension types via attributes.
- **`ExtensionRegistry`** for registering individual extensions, mapping them to their extension type, and validating required interfaces.
- **`ExtensionStore`** for unified extension lifecycle management:
    - Enforces **singleton** vs. **multi-instance** behavior.
    - Checks **missing dependencies** and throws descriptive exceptions.
    - Dispatches **`ExtensionInstantiatedEvent`** upon creation.
- **PSR-14 Event Dispatcher Integration**: Optionally dispatch events whenever an extension is instantiated.
- **PSR-3 Logging**: Uses a PSR-3 `LoggerInterface` to log extension registration details and errors.
- **Custom Instantiator**: Plug in any `InstantiatorInterface` to control how extensions are created (e.g., via a DI container).
