# GitHub Copilot Instructions for laravel-table

## Project Overview

This is a powerful, eloquent-driven table package for Laravel with multi-framework frontend support. The package provides a flexible and extensible way to create data tables with features like pagination, sorting, filtering, and searching.

## Architecture

The project is organized into three main modules:

- **Core**: Contains the core business logic, abstractions, and contracts
  - `Table`: Main table abstraction with traits for state management and column interactions
  - `Columns`: Column definitions and registry
  - `Query`: Query building pipes (filters, sorting, search)
  - `Casting`: Type casting system with custom casters
  - `Serialization`: Row serialization logic
  - `DTO`: Data Transfer Objects for state and responses
  - `Enums`: Enumerations (SortDirection, FilterOperator)
  - `Contracts`: Interfaces and contracts

- **Engine**: Query execution and processing
  - `QueryBuilderEngine`: Eloquent query builder integration
  - `CastManager`: Manages type casting
  - `ColumnRegistry`: Column registration and management
  - `RowSerializer`: Serializes database rows

- **Laravel**: Laravel-specific integration
  - `Providers`: Service providers
  - `Facades`: Laravel facades
  - `Resolvers`: Dependency resolution
  - `Capabilities`: Laravel-specific features
  - `Query`: Laravel query builder integration

## Code Style and Conventions

### PHP Standards
- **PHP Version**: 8.5+ (strict typing required)
- **Coding Standard**: PSR-12 (enforced via PHP CS Fixer)
- **Type Declarations**: Always use strict types (`declare(strict_types=1);`)
- **Return Types**: Always declare return types for methods
- **Parameter Types**: Always type-hint parameters
- **Nullable Types**: Use nullable syntax (`?Type`) when appropriate

### Naming Conventions
- **Classes**: PascalCase (e.g., `TableContract`, `QueryBuilderEngine`)
- **Methods**: camelCase (e.g., `defineColumns()`, `applyFilters()`)
- **Variables**: camelCase (e.g., `$tableState`, `$columnRegistry`)
- **Constants**: UPPER_SNAKE_CASE (when used in classes)
- **Namespaces**: Follow PSR-4 autoloading standard

### Design Patterns
- **Contracts/Interfaces**: Use for all major abstractions (e.g., `TableContract`, `ColumnContract`)
- **Abstract Classes**: Use for base implementations (e.g., `Table`, `Column`)
- **Traits**: Use for reusable concerns (e.g., `HasResponse`, `InteractsWithColumns`)
- **DTOs**: Use for data transfer between layers
- **Pipes**: Use for query transformation steps
- **Macroable**: Apply where extensibility is needed

### Laravel Integration
- **Service Providers**: Register services and configurations
- **Facades**: Provide static access to services
- **Eloquent**: Use Eloquent Builder for query operations
- **Configuration**: Store in `config/laravel-table.php`

## Development Workflow

### Code Formatting
```bash
composer format
# or
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php
```

### Static Analysis
```bash
composer analyse
# or
vendor/bin/phpstan analyse src
```

Static analysis is configured to level 8 (strictest) in `phpstan.neon`.

### Testing
```bash
composer test
# or
vendor/bin/phpunit
```

Tests use Orchestra Testbench for Laravel package testing with SQLite in-memory database.

## Key Guidelines

### When Adding New Features
1. Define contracts/interfaces in `Core/Contracts`
2. Implement core logic in `Core` module (framework-agnostic)
3. Add Laravel-specific integration in `Laravel` module
4. Register services in the service provider
5. Update configuration if needed
6. Write tests following existing patterns
7. Run static analysis and ensure level 8 compliance
8. Format code with PHP CS Fixer

### When Working with Columns
- Extend from base column classes
- Register in `ColumnRegistry`
- Implement serialization logic if needed
- Add type casting if custom types are involved

### When Working with Queries
- Use pipe pattern for query transformations
- Keep pipes in `Core/Query/Pipes`
- Register pipes in configuration
- Ensure Eloquent Builder compatibility

### When Adding Casters
- Implement caster logic
- Register in `config/laravel-table.php`
- Support both single values and collections
- Handle null values gracefully

## Common Patterns

### Creating a New Column Type
```php
namespace LaravelTable\Core\Columns;

use LaravelTable\Core\Contracts\ColumnContract;

class CustomColumn implements ColumnContract
{
    // Implementation
}
```

### Adding a Query Pipe
```php
namespace LaravelTable\Core\Query\Pipes;

use Illuminate\Database\Eloquent\Builder;
use LaravelTable\Core\DTO\TableStateDTO;

class CustomPipe
{
    public function handle(Builder $query, TableStateDTO $state, \Closure $next): Builder
    {
        // Transform query
        return $next($query);
    }
}
```

### Creating a Caster
```php
namespace LaravelTable\Core\Casting\Casters;

use LaravelTable\Core\Contracts\CasterContract;

class CustomCaster implements CasterContract
{
    public function cast(mixed $value): mixed
    {
        // Type casting logic
        return $value;
    }
}
```

## Important Notes

- **No Test Directory**: Currently, the project structure references tests but the directory doesn't exist yet. When adding tests, create them in a `tests/` directory following Laravel package conventions.
- **Minimum Stability**: Set to "dev" with "prefer-stable: true" for flexibility during development.
- **Dependencies**: Support Laravel 10.x and 11.x, PHP 8.5+
- **Documentation**: README.md exists but is empty - should be populated with usage examples and documentation.

## Error Handling

- Use type hints and return types to catch errors at compile time
- Leverage PHPStan level 8 for maximum type safety
- Handle Eloquent Builder edge cases (note the ignored errors in phpstan.neon)
- Validate input data in DTOs and value objects

## Performance Considerations

- Use Eloquent query builder efficiently (avoid N+1 queries)
- Implement pagination for large datasets
- Cache column registrations when possible
- Keep serialization logic lightweight
