# ksf_ProjectManagement - Architecture

## Overview

`ksfraser/ksf-project-management` is a composer-installable PHP library providing enterprise project management capabilities. It is the core logic layer, designed to be wrapped by FA modules or consumed directly.

## Package Hierarchy

```
┌─────────────────────────────────────────────────────────────┐
│                  ksf_FA_ProjectManagement                   │
│              (FA module - UI, hooks, pages)                │
│                                                             │
│  hooks.php, pm.php, pages/, includes/, _init/              │
│  FA_PM_Module.php - module class, permissions, menu         │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ requires
                            ▼
┌─────────────────────────────────────────────────────────────┐
│               ksfraser/ksf-project-management               │
│                  (Composer package - logic)                │
│                                                             │
│  ProjectService, Entities, Events, Contracts, Exceptions   │
│  100% test coverage, PSR-4, PSR-11, PSR-14, PSR-3           │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ depends on
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    ksf Dependency Stack                     │
│                                                             │
│  ksf-exceptions  │ Base exception hierarchy                │
│  ksf-traits      │ Shared traits (DateTime, ArrayAccess)   │
│  ksf-schema-manager │ DB schema version control            │
│  ksf-validation  │ Input validation rules                  │
│  ksf-cache       │ (dev) Caching for tests                 │
│  html            │ (dev) HTML generation for docs           │
│  famock          │ (dev) FA mocking utilities               │
└─────────────────────────────────────────────────────────────┘
```

## Directory Structure (ksf_ProjectManagement)

```
ksf_ProjectManagement/
├── src/Ksfraser/ProjectManagement/
│   ├── ProjectService.php           # Main service (SERVICE LAYER)
│   ├── Contract/                    # INTERFACE SEGREGATION
│   │   ├── DatabaseAdapterInterface.php
│   │   ├── ProjectServiceInterface.php
│   │   └── EmployeeServiceInterface.php
│   ├── Entity/                      # DOMAIN MODEL
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── ProjectAssignment.php
│   ├── Event/                       # PSR-14 EVENT SYSTEM
│   │   ├── ProjectEvent.php (abstract)
│   │   ├── ProjectCreatedEvent.php
│   │   ├── ProjectUpdatedEvent.php
│   │   ├── TaskEvent.php (abstract)
│   │   ├── TaskCreatedEvent.php
│   │   ├── TaskProgressUpdatedEvent.php
│   │   └── EmployeeAssignedToProjectEvent.php
│   ├── Exception/                   # ERROR HANDLING
│   │   ├── ProjectException.php
│   │   ├── ProjectNotFoundException.php
│   │   ├── TaskNotFoundException.php
│   │   └── ValidationException.php
│   └── Repository/                  # DATA ACCESS (interfaces)
│       ├── ProjectRepositoryInterface.php
│       ├── TaskRepositoryInterface.php
│       └── AssignmentRepositoryInterface.php
├── tests/
│   ├── Unit/
│   │   ├── ProjectServiceTest.php   # Service tests (mocks)
│   │   └── Entity/
│   │       ├── ProjectTest.php
│   │       ├── TaskTest.php
│   │       └── ProjectAssignmentTest.php
│   │   └── Event/
│   │       ├── ProjectCreatedEventTest.php
│   │       ├── ProjectUpdatedEventTest.php
│   │       ├── TaskCreatedEventTest.php
│   │       ├── TaskProgressUpdatedEventTest.php
│   │       └── EmployeeAssignedToProjectEventTest.php
│   └── Integration/                 # Future: real DB tests
├── doc/ProjectDocuments/
│   ├── Requirements/requirements.md
│   ├── Architecture/architecture.md
│   ├── TestPlans/
│   └── Design/
├── composer.json
└── phpunit.xml
```

## Design Patterns

| Pattern | Implementation |
|---------|----------------|
| **Service Layer** | `ProjectService` orchestrates all PM business logic |
| **Domain Model** | `Entity/Project`, `Entity/Task`, `Entity/ProjectAssignment` |
| **Interface Segregation** | Separate contracts for each dependency |
| **Dependency Injection** | Constructor injection of all dependencies |
| **Event-Driven** | PSR-14 events on create/update operations |
| **Factory** | Entities via constructor (no static factories needed) |
| **Repository (planned)** | Interfaces defined for future persistence abstraction |
| **Fluent Setters** | All setters return `self` for method chaining |
| **Value Objects** | DateTime, floats with validation (progress clamped 0-100) |

## Dependency Injection

```
ProjectService (constructor)
├── DatabaseAdapterInterface    → FADatabaseAdapter / Mock
├── EventDispatcherInterface     → FAEventDispatcher / Mock
├── LoggerInterface              → NullLogger / Mock
└── EmployeeServiceInterface     → FAEmployeeService / Mock
```

## Database Schema (fa_pm_ prefix)

```
fa_pm_projects
  - project_id (PK)
  - name, description
  - start_date, end_date
  - budget, customer_id
  - project_manager
  - priority, status
  - created_at, updated_at

fa_pm_tasks
  - task_id (PK)
  - project_id (FK)
  - parent_task_id (self-ref)
  - name, description
  - assigned_to
  - start_date, end_date
  - estimated_hours, actual_hours
  - progress (0-100)
  - priority, status

fa_pm_assignments
  - project_id (PK,FK)
  - employee_id (PK)
  - role
  - start_date, end_date
  - allocation_percentage

fa_pm_project_types
  - id (PK)
  - name, description
  - inactive, sort_order

fa_pm_activity_log
  - id (PK)
  - entity_type, entity_id
  - user_id, action
  - details, old_values, new_values
  - ip_address, created_at
```

## SOLID Compliance

| Principle | Compliance |
|-----------|------------|
| **S**ingle Responsibility | Each class has one job. Entities have business logic. Service orchestrates. Events are separate. Exceptions are separate. |
| **O**pen/Closed | Open for extension via event system and repository interfaces. Closed for modification. |
| **L**iskov Substitution | All contracts (interfaces) allow substitution. Tests use mocks. |
| **I**nterface Segregation | Multiple small interfaces: `ProjectServiceInterface`, `DatabaseAdapterInterface`, `EmployeeServiceInterface`, repository interfaces. |
| **D**ependency Inversion | High-level `ProjectService` depends on abstractions (`DatabaseAdapterInterface`), not concretions. |

## Comparison to Reference CRMs

| Feature | ksf-PM | dotProject | OpenProject | SuiteCRM | Dolibarr |
|---------|--------|------------|-------------|----------|----------|
| Projects | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| Tasks | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| Hierarchy | :white_check_mark: | :white_check_mark: | :white_check_mark: | Limited | :white_check_mark: |
| Progress | :white_check_mark: | :white_check_mark: | :white_check_mark: | Basic | :white_check_mark: |
| Budget | :white_check_mark: | :x: | :white_check_mark: | :x: | :white_check_mark: |
| Gantt | Planned | :white_check_mark: | :white_check_mark: | :white_check_mark: | Planned |
| Time Tracking | Planned | :white_check_mark: | :white_check_mark: | Limited | :white_check_mark: |
| Resources | Basic | :white_check_mark: | :white_check_mark: | :white_check_mark: | Basic |
| Composer | :white_check_mark: | :x: | :x: | :x: | :x: |
| PSR-4/11/14 | :white_check_mark: | :x: | :x: | :x: | :x: |
| 100% Coverage | :white_check_mark: | :x: | :x: | :x: | :x: |
| PHP 8+ | :white_check_mark: | Old | Modern | Old | Old |

## FAQ

**Q: Why separate ksf_ProjectManagement from ksf_FA_ProjectManagement?**  
A: Allows the PM logic to be consumed by any PHP application, not just FrontAccounting. The FA module is just a thin wrapper.

**Q: Why use events?**  
A: Loose coupling. The FA module can listen to events without the service knowing about FA. Tests can verify events are dispatched.

**Q: Why mock everything in tests?**  
A: 100% coverage without needing a test database. Fast tests. Isolated unit tests.

**Q: Why fa_pm_ prefix?**  
A: Consistent with fa_crm_ in the CRM module. Clear namespace separation from other modules.