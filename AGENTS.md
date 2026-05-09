# AGENTS.md - ksf_ProjectManagement

> **DO NOT MODIFY THIS FILE.** Create `AGENTS.local.md` for project-specific overrides.

## Core Philosophy

This project follows enterprise-grade software engineering principles. Every decision should align with: **SOLID**, **DRY**, **SRP**, **DI**, and **TDD**.

---

## Repository Architecture

### Business Logic Module
```
ksf_ProjectManagement/           # Business logic (framework-agnostic)
├── src/Ksfraser/ProjectManagement/
│   ├── Exception/              # Module exceptions (using shared library)
│   ├── Service/               # Business logic services
│   ├── Entity/                # Domain entities
│   └── Repository/            # Data access abstraction
├── tests/                      # Unit tests
└── doc/                        # Project documents
```

### Platform Adapters (separate repos)
- `ksf_FA_ProjectManagement/` → FrontAccounting adapter
- `ksf_UI_ProjectManagement/` → UI components

---

## Dependency Management

### Required Libraries
```json
{
    "require": {
        "ksfraser/exceptions": "^1.3",
        "ksfraser/traits": "^1.0",
        "ksfraser/validation": "^1.0"
    }
}
```

### Repositories
```json
{
    "repositories": [
        {"type": "vcs", "url": "https://github.com/ksfraser/Exceptions"},
        {"type": "vcs", "url": "https://github.com/ksfraser/Traits"},
        {"type": "vcs", "url": "https://github.com/ksfraser/Validation"}
    ]
}
```

---

## Exception Handling

### Use Shared Library
```php
use Ksfraser\Exceptions\ProjectManagement\ProjectException;
use Ksfraser\Exceptions\ProjectManagement\ProjectNotFoundException;
use Ksfraser\Exceptions\Domain\EntityNotFoundException;
```

### Module-Specific Exceptions
Local exceptions in `Exception/` extend library classes:
```php
use Ksfraser\Exceptions\ProjectManagement\ProjectException as BaseProjectException;

class ProjectException extends BaseProjectException
{
    // Module-specific extension
}
```

---

## Coding Standards

### PHP Compatibility
- **Target**: PHP 8.0+
- Always use `declare(strict_types=1);`

### DocBlock Standards
```php
/**
 * Create a new project.
 *
 * @param array $data Project data
 * @return Project The created project
 * @throws ProjectValidationException If data is invalid
 *
 * @since 1.0.0
 * @see ProjectRepository::update()
 */
public function create(array $data): Project
```

### Required tags: `@param`, `@return`, `@throws`, `@since`

---

## Testing Standards

### TDD Workflow
1. **RED**: Write failing test
2. **GREEN**: Write minimal code to pass
3. **REFACTOR**: Improve while keeping tests green

### Coverage Requirements
- **Target**: 100% code coverage
- Skipped tests = failed tests (treat as incomplete)

---

## .gitignore

```
/vendor/
/composer.lock
.phpunit.cache/
.phpunit.result.cache
.idea/
.vscode/
```

**Never track vendor/ or composer.lock** - each developer runs `composer install`.

---

## Documentation

### Project Documents (`doc/ProjectDocuments/`)
```
doc/ProjectDocuments/
├── ProjectDcs/
│   ├── Architecture.md
│   ├── Functional Requirements.md
│   ├── Test Plan.md
│   └── UAT Plan.md
├── BABOK/
├── UML/
└── RTM/
```

### Code Documentation
- Include `@UML` reference for architecture diagrams
- Include `@BABOK` reference for requirements alignment

---

## SOLID Principles Checklist

| Principle | Description |
|-----------|-------------|
| **S**ingle Responsibility | One class, one purpose |
| **O**pen/Closed | Open for extension, closed for modification |
| **L**iskov Substitution | Subtypes substitutable for base types |
| **I**nterface Segregation | Small, focused interfaces |
| **D**ependency Inversion | Depend on abstractions |

---

## Code Review Checklist

- [ ] All new code has tests (100% coverage target)
- [ ] PHPDoc complete with `@param`, `@return`, `@throws`, `@since`
- [ ] No hardcoded values (use constants/config)
- [ ] No duplicate code (use shared libraries)
- [ ] Dependencies injected, not instantiated
- [ ] `.gitignore` excludes vendor/ and composer.lock

---

## Local Overrides

Create `AGENTS.local.md` for project-specific overrides:

```markdown
# AGENTS.local.md
# Project-specific overrides

[Your overrides here]
```

**Note**: Core principles (SOLID, DRY, TDD) cannot be overridden.