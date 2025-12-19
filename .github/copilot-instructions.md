# Copilot Instructions — PHP & Laravel Best Practices

These instructions guide code generation and suggestions for this project.
Follow modern PHP and Laravel best practices at all times.

## General PHP Guidelines
- Target **PHP 8.5+** features and syntax.
- Use **strict typing** where applicable.
- Favor **readability and maintainability** over clever or compact code.
- Follow **PSR-12** coding standards.
- Use meaningful variable, method, and class names.
- Avoid global state and side effects.
- Prefer dependency injection over static or facades when practical.
- Write code that is easy to test.

## Laravel Framework Conventions
- Follow **Laravel conventions over configuration**.
- Use Laravel’s built-in features instead of reinventing solutions.
- Keep controllers thin; move logic to:
  - Services
  - Actions
  - Jobs
  - Model scopes
- Use **Form Requests** for validation.
- Use **Eloquent relationships** instead of manual joins when possible.
- Avoid business logic inside Blade views.

## Controllers
- Controllers should:
  - Handle request/response logic only
  - Delegate business logic to services or actions
- Use route model binding.
- Return proper HTTP responses and status codes.
- Prefer resource controllers when appropriate.

## Models & Eloquent
- Keep models focused on data and relationships.
- Use:
  - Accessors & mutators for data transformation
  - Query scopes for reusable query logic
- Avoid putting large business logic in models.
- Use casts and enums where applicable.
- Prevent mass assignment issues with `$fillable` or `$guarded`.

## Validation
- Use **Form Request classes** for validation.
- Keep validation rules explicit and readable.
- Use custom validation messages when helpful.

## Blade Templates
- Keep Blade views simple and presentational.
- Avoid complex logic in views.
- Use components and slots for reusable UI.
- Escape output by default; only use `{!! !!}` when absolutely safe.
- Follow a consistent layout and naming convention.

## Routing
- Use RESTful routing conventions.
- Group routes logically.
- Use named routes.
- Apply middleware at the route or group level.
- Keep route files clean and readable.

## Configuration & Environment
- Use `.env` variables for environment-specific settings.
- Never hardcode secrets.
- Access configuration via `config()` instead of `env()` outside config files.

## Security Best Practices
- Always validate and sanitize user input.
- Protect routes using authentication and authorization.
- Use Laravel policies and gates for authorization.
- Protect against:
  - SQL injection (use Eloquent / query builder)
  - XSS (escape output)
  - CSRF (use Laravel’s CSRF protection)

## Testing
- Prefer feature and unit tests using PHPUnit or Pest.
- Write tests for critical business logic.
- Use model factories for test data.
- Avoid relying on production services in tests.

## Performance & Maintenance
- Avoid N+1 queries; use eager loading.
- Cache expensive operations where appropriate.
- Keep files and classes small and focused.
- Follow Laravel’s directory structure and naming conventions.

## Code Style Expectations
- Favor expressive, self-documenting code.
- Avoid unnecessary comments; write clear code instead.
- When comments are used, explain *why*, not *what*.