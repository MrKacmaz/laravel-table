# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2026-02-13

### Added
- Core table lifecycle (`make`, `boot`, `getBuilder`, `response`) implementation.
- Query engine pipeline integration and Laravel bindings.
- State resolver, capability gate, DTO and serialization primitives.
- Relation column search/sort/filter behavior compatible with Eloquent relations.
- Package smoke tests and release-ready README documentation.

### Changed
- PSR-4 autoload namespace aligned to `LaravelTable\\`.
- Dev autoload namespace aligned to `LaravelTable\\Tests\\`.
- Composer `test` and `analyse` scripts configured with `XDEBUG_MODE=off` for stable local execution.
