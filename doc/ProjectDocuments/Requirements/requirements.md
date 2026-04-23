# ksf_ProjectManagement - Requirements

## Project Overview

**Name**: ksf_ProjectManagement (ksfraser/ksf-project-management)  
**Type**: Composer-installable PHP library / FrontAccounting PM package  
**Purpose**: Provide enterprise project management capabilities, replicating/replacing features from dotProject, OpenProject, SuiteCRM, vTiger, WebERP, and Dolibarr.

---

## 1. Scope

### 1.1 In Scope

#### Core Entities
- **Project**: Project records with budget, dates, manager, priority, status
- **Task**: Hierarchical tasks with parent-child relationships, progress tracking
- **ProjectAssignment**: Employee-project assignments with role and allocation %

#### Business Logic
- Full CRUD operations for all entities
- Validation (required fields, date logic, referential integrity)
- Progress calculation and overdue detection
- Active/ended assignment detection

#### Architecture Requirements
- PSR-4 autoloading (`Ksfraser\ProjectManagement\*`)
- Dependency injection via interfaces (contracts)
- PSR-11 container support
- PSR-14 event dispatcher with typed events
- PSR-3 logging
- Single Responsibility Principle (SRP) - 1 class per file
- Interface Segregation - contracts for all services
- 100% PHPUnit unit test coverage (mocked dependencies)

#### Composability
- Published on Packagist
- Semantic versioning
- Dev dependencies for testing: phpunit, phpdocumentor, phpstan, phpcs

### 1.2 Out of Scope

- UI/presentation layer (handled by ksf_FA_ProjectManagement)
- Database connectivity (handled by FA or consumer's DBAL)
- Authentication/authorization (handled by host application)
- File attachments
- Time tracking
- Gantt chart generation
- Email/SMS notifications

---

## 2. Functional Requirements

### 2.1 Project Entity

| Field | Type | Required | Default |
|-------|------|----------|---------|
| project_id | string | Yes | auto |
| name | string | Yes | - |
| description | string | No | '' |
| start_date | DateTime | Yes | - |
| end_date | DateTime | No | null |
| budget | float | No | 0.0 |
| customer_id | string | No | '' |
| project_manager | string | Yes | - |
| priority | string | No | 'Medium' |
| status | string | No | 'Planning' |

**Valid Priority Values**: 'Low', 'Medium', 'High', 'Critical'  
**Valid Status Values**: 'Planning', 'Active', 'On Hold', 'Completed', 'Cancelled'

**Methods**:
- `getDuration(): int|null` - Days between start and end
- `isOverdue(): bool` - Past end date and not completed
- `isActive(): bool` - Within date range and not completed/cancelled
- `toArray(): array` - Serialize to array

### 2.2 Task Entity

| Field | Type | Required | Default |
|-------|------|----------|---------|
| task_id | string | Yes | auto |
| project_id | string | Yes | - |
| parent_task_id | string | No | '' |
| name | string | Yes | - |
| description | string | No | '' |
| assigned_to | string | No | '' |
| start_date | DateTime | No | null |
| end_date | DateTime | No | null |
| estimated_hours | float | No | 0.0 |
| actual_hours | float | No | 0.0 |
| progress | float | No | 0.0 |
| priority | string | No | 'Medium' |
| status | string | No | 'Not Started' |

**Valid Priority Values**: 'Low', 'Medium', 'High', 'Critical'  
**Valid Status Values**: 'Not Started', 'In Progress', 'On Hold', 'Completed', 'Cancelled'

**Methods**:
- `setProgress(float $p): self` - Clamps 0-100
- `isCompleted(): bool` - Status completed OR progress >= 100
- `isOverdue(): bool` - Past end date and not completed
- `getDuration(): int|null` - Days between start and end
- `hasSubtasks(): bool` - Always false (parent detection via repo)
- `toArray(): array` - Serialize to array

### 2.3 ProjectAssignment Entity

| Field | Type | Required | Default |
|-------|------|----------|---------|
| project_id | string | Yes | - |
| employee_id | string | Yes | - |
| role | string | No | 'Team Member' |
| start_date | DateTime | Yes | - |
| end_date | DateTime | No | null |
| allocation_percentage | float | No | 100.0 |

**Methods**:
- `setAllocationPercentage(float $p): self` - Clamps 0-100
- `isActive(): bool` - Within date range
- `isEnded(): bool` - Past end date
- `toArray(): array` - Serialize to array

### 2.4 Service Operations

#### ProjectService
- `createProject(array $data): Project`
- `getProject(string $id): Project`
- `updateProject(string $id, array $data): Project`
- `deleteProject(string $id): void`
- `createTask(array $data): Task`
- `getTask(string $id): Task`
- `getProjectTasks(string $projectId): Task[]`
- `updateTask(string $id, array $data): Task`
- `deleteTask(string $id): void`
- `updateTaskProgress(string $id, float $progress, string $status): void`
- `assignEmployeeToProject(string $projectId, string $employeeId, array $data): void`
- `removeEmployeeFromProject(string $projectId, string $employeeId): void`
- `getProjectTeam(string $projectId): array`

### 2.5 Events (PSR-14)

| Event | Payload |
|-------|---------|
| ProjectCreatedEvent | Project |
| ProjectUpdatedEvent | Project, changedFields[] |
| TaskCreatedEvent | Task |
| TaskProgressUpdatedEvent | Task, previousProgress, newProgress |
| EmployeeAssignedToProjectEvent | ProjectAssignment |

All events implement `Stoppable` interface.

### 2.6 Exceptions

| Exception | Description |
|-----------|-------------|
| ProjectException | Base exception |
| ProjectNotFoundException | Project not found |
| TaskNotFoundException | Task not found |
| ValidationException | Validation failed, contains errors[] |

---

## 3. Non-Functional Requirements

### 3.1 Coding Standards
- PSR-12 coding standard (phpcs/phpstan)
- PHP 8.0+ type declarations everywhere
- Named constructor arguments where helpful
- Fluent setters returning self

### 3.2 Testing
- PHPUnit 10.x
- Mock all external dependencies (DBAL, EventDispatcher, Logger, EmployeeService)
- 100% line/branch coverage on production code
- Exclude Exception classes from coverage (no logic)

### 3.3 Documentation
- phpdocumentor for API docs
- All public methods must have docblocks
- README.md with usage examples

### 3.4 Directory Structure

```
ksf_ProjectManagement/
├── composer.json
├── phpunit.xml
├── src/Ksfraser/ProjectManagement/
│   ├── ProjectService.php
│   ├── Contract/
│   │   ├── DatabaseAdapterInterface.php
│   │   ├── ProjectServiceInterface.php
│   │   └── EmployeeServiceInterface.php
│   ├── Entity/
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── ProjectAssignment.php
│   ├── Event/
│   │   ├── ProjectEvent.php
│   │   ├── ProjectCreatedEvent.php
│   │   ├── ProjectUpdatedEvent.php
│   │   ├── TaskEvent.php
│   │   ├── TaskCreatedEvent.php
│   │   ├── TaskProgressUpdatedEvent.php
│   │   └── EmployeeAssignedToProjectEvent.php
│   ├── Exception/
│   │   ├── ProjectException.php
│   │   ├── ProjectNotFoundException.php
│   │   ├── TaskNotFoundException.php
│   │   └── ValidationException.php
│   └── Repository/
│       ├── ProjectRepositoryInterface.php
│       ├── TaskRepositoryInterface.php
│       └── AssignmentRepositoryInterface.php
├── tests/
│   ├── bootstrap.php
│   └── Unit/
│       ├── ProjectServiceTest.php
│       └── Entity/
│           ├── ProjectTest.php
│           ├── TaskTest.php
│           └── ProjectAssignmentTest.php
│       └── Event/
│           ├── ProjectCreatedEventTest.php
│           ├── ProjectUpdatedEventTest.php
│           ├── TaskCreatedEventTest.php
│           ├── TaskProgressUpdatedEventTest.php
│           └── EmployeeAssignedToProjectEventTest.php
└── doc/ProjectDocuments/
    ├── Requirements/
    ├── Architecture/
    ├── TestPlans/
    └── Design/
```

---

## 4. Acceptance Criteria

1. All entities can be instantiated with required fields
2. All setters are fluent (return self)
3. All entity methods work as documented
4. ProjectService creates/retrieves projects from mocked DB
5. ProjectService validates input and throws ValidationException
6. ProjectService throws NotFoundException for missing entities
7. Events are dispatched on create/update operations
8. PHPUnit tests achieve 100% coverage on production code
9. phpstan level 8 passes with no errors
10. phpcs passes with PSR-12
11. composer validate passes
12. Package installs from Packagist

---

## 5. References

- [dotProject](https://dotproject.net/) - Reference PM features
- [OpenProject](https://www.openproject.org/) - Reference PM features
- [PSR-4](https://www.php-fig.org/psr/psr-4/) - Autoloading
- [PSR-11](https://www.php-fig.org/psr/psr-11/) - Container
- [PSR-14](https://www.php-fig.org/psr/psr-14/) - Events
- [PSR-3](https://www.php-fig.org/psr/psr-3/) - Logging
- [PSR-12](https://www.php-fig.org/psr/psr-12/) - Coding Style