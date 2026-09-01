<!-- Repo-specific appendix to the shared AGENTS.md. Generic conventions live in AGENTS_ARCH.md (hardlinked). -->

# AGENTS.local.md — ksf_ProjectManagement
> Repo-specific overrides for `ksfraser/ksf_ProjectManagement`. Core principles (SOLID, DRY, TDD) cannot be overridden.
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
