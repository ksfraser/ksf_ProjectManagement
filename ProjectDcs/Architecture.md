# Architecture - ksf_ProjectManagement

## Document Information
- **Module**: ksf_ProjectManagement
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Module Overview

ksf_ProjectManagement is a business logic layer that provides enterprise project management capabilities including project creation, task management, resource allocation, and progress tracking.

### 1.1 Namespace
```php
Ksfraser\ProjectManagement\
```

### 1.2 Layer Pattern
```
ksf_ProjectManagement/     → Business Logic
    ├── Entity/            → Domain entities
    ├── Service/           → Business services
    ├── Repository/        → Data access interfaces
    ├── Contract/          → Interfaces for adapters
    ├── Exception/         → Domain exceptions
    └── Event/             → PSR-14 events
```

---

## 2. Architecture Layers

### 2.1 Entity Layer (`Entity/`)

| Entity | Purpose |
|--------|---------|
| `Project` | Project aggregate root |
| `Task` | Task with parent-child hierarchy |
| `ProjectAssignment` | Employee allocation to project |

**Entity Relationships**:
```
Project (1) ──────< Task (many)
Project (1) ──────< ProjectAssignment (many)
Task (1) ─────────< Task (self-ref, parent-child)
```

### 2.2 Service Layer (`Service/`)

| Service | Purpose |
|---------|---------|
| `ProjectService` | Project CRUD, lifecycle management |
| `TaskService` | Task CRUD, hierarchy, progress |
| `ResourceService` | Allocation management |
| `ReportService` | Project analytics |

### 2.3 Contract Layer (`Contract/`)

| Interface | Purpose |
|-----------|---------|
| `ProjectServiceInterface` | Dependency injection for adapters |
| `ProjectRepositoryInterface` | Data access abstraction |
| `EmployeeServiceInterface` | HRM integration |
| `DatabaseAdapterInterface` | Database abstraction |

### 2.4 Event Layer (`Event/`)

| Event | Payload |
|-------|---------|
| `ProjectCreatedEvent` | Project entity |
| `ProjectUpdatedEvent` | Project entity, changes |
| `ProjectCompletedEvent` | Project entity |
| `TaskCreatedEvent` | Task entity |
| `TaskProgressUpdatedEvent` | Task entity, old/new progress |
| `EmployeeAssignedToProjectEvent` | ProjectAssignment |

---

## 3. Domain Model

### 3.1 Project Aggregate

```php
class Project {
    private string $id;
    private string $name;
    private string $customerId;      // FK to CRM
    private string $projectManagerId;
    private \DateTime $startDate;
    private ?\DateTime $endDate;
    private float $budget;
    private Priority $priority;
    private ProjectStatus $status;
    
    // Value Objects
    private Duration $duration;
    private Budget $budgetRemaining;
    
    // Entity Methods
    public function calculateProgress(): int;
    public function isOverdue(): bool;
    public function addTask(Task $task): self;
    public function assignEmployee(EmployeeAssignment $assignment): self;
}
```

### 3.2 Task Aggregate

```php
class Task {
    private string $id;
    private string $projectId;
    private ?string $parentTaskId;
    private string $name;
    private ?string $assignedTo;
    private int $progress;          // 0-100
    private TaskStatus $status;
    private Priority $priority;
    private ?\DateTime $dueDate;
    private float $estimatedHours;
    private float $actualHours;
    
    // Business Logic
    public function updateProgress(int $progress): self;
    public function complete(): self;
    public function isOverdue(): bool;
    public function getChildren(): array;
    public function calculateRollupProgress(): int;
}
```

### 3.3 ProjectAssignment Value Object

```php
class ProjectAssignment {
    private string $id;
    private string $projectId;
    private string $employeeId;
    private ?string $role;
    private int $allocationPercent;  // 0-100
    private ?\DateTime $startDate;
    private ?\DateTime $endDate;
    private bool $isActive;
}
```

---

## 4. State Machines

### 4.1 Project Status

```
Planning ──> Active ──> On Hold ──> Completed
    │           │            │
    └───────────┴────────────┴──> Cancelled
```

### 4.2 Task Status

```
Not Started ──> In Progress ──> Completed
     │               │
     └───────────────┴──> Cancelled
```

---

## 5. Integration Architecture

### 5.1 Provided Services

| Consumer | Interface | Data |
|----------|-----------|------|
| ksf_FA_ProjectManagement | ProjectServiceInterface | Projects, Tasks |
| ksf_CRM | via Events | Customer projects |
| ksf_Timesheets | via Events | Time entries |
| ksf_Calendar | via Events | Due dates |

### 5.2 Consumed Services

| Provider | Interface | Data |
|----------|-----------|------|
| ksf_HRM | EmployeeServiceInterface | Employees |
| ksf_CRM | CustomerServiceInterface | Customer links |
| ksf_Timesheets | TimeEntryServiceInterface | Actual hours |

### 5.3 Event Flow

```
User Action → ProjectService → Domain Event → EventDispatcher → 
    → ksf_CRM (project.created) →
    → ksf_Calendar (milestone.created) →
    → ksf_Timesheets (time entry linked)
```

---

## 6. Adapter Pattern

### 6.1 Database Adapter

```php
interface DatabaseAdapterInterface {
    public function find(string $id): ?array;
    public function findBy(string $field, $value): array;
    public function save(array $data): bool;
    public function delete(string $id): bool;
}
```

FA Implementation: `Ksfraser\FA\ProjectManagement\Database\ProjectDatabaseAdapter`

### 6.2 Service Adapter

```php
interface ProjectServiceInterface {
    public function createProject(array $data): Project;
    public function getProject(string $id): ?Project;
    public function updateProject(string $id, array $data): Project;
    public function deleteProject(string $id): bool;
    public function getProjectTasks(string $projectId): array;
    public function assignEmployee(string $projectId, array $data): ProjectAssignment;
}
```

---

## 7. RBAC Integration

### 7.1 Module Registration

ksf_ProjectManagement registers with ksfraser/rbac:
- record_types: 'project', 'task'
- projections: 'public' (name, status, dates), 'full' (all fields including budget, costs, contracts)
- allow_invite: false
- children: task (child of project)

### 7.2 Entity Projections

| Entity | PUBLIC Fields | FULL Fields |
|--------|---------------|-------------|
| Project | name, description, status, start_date, end_date, priority | + budget, costs, contracts, risks, customer data |
| Task | name, status, progress, assigned_to, due_date, estimated_hours | + actual_hours, dependencies, internal_notes |

### 7.3 Access Model

- **Project Manager**: FULL access to assigned projects (via {pmUserId}_individual team grant)
- **Team Lead**: PUBLIC to project + FULL to assigned tasks + ability to edit team tasks
- **Team Member**: PUBLIC to project + FULL to own tasks only (via task-level xref)
- **Executive/Sponsor**: PUBLIC to portfolio projects (view-only)
- **Finance**: FULL to budget/cost fields of active projects

### 7.4 SQL Enforcement

All project-fetching queries MUST include the RBAC standard JOIN:

```sql
JOIN 0_rbac_record_access ra
  ON ra.record_id    = p.id
 AND ra.record_type  = 'project'
 AND ra.module       = 'project_management'
 AND ra.inactive     = 0
 AND ra.can_view     = 1
JOIN 0_rbac_team_members tm
  ON tm.team_id  = ra.team_id
 AND tm.user_id  = :currentUserId
 AND tm.inactive = 0
```

### 7.5 Access Inheritance (Parent → Child)

When a team is granted access to a project, the grant MAY optionally cascade to child tasks via the InheritanceMap:
- Task status 'not_started' / 'in_progress': inherit parent capabilities
- Task status 'completed': reduce to view-only (can_view = 1, can_edit = 0)
- Task status 'cancelled': exclude from inheritance

### 7.6 Switch-Role Elevation

Users with multiple project roles can elevate per-record. Example: a Team Lead who is also a Project Manager on a different project cannot automatically see the PM-level data — they must hold a grant for that specific project.

### 7.7 Soft Delete

Projects and tasks use soft delete: `deleted = 1`, `deleted_by`, `deleted_at`. Hard delete is super-admin only.

### 7.8 Audit Logging

- Project/task access grants logged to RBAC audit log
- Permission denials for failed access attempts optionally logged
- Role elevation events logged

---

## 8. Performance Considerations

### 8.1 Caching Strategy
- Project progress: Cache with 5-min TTL
- Resource allocation: Real-time (small dataset)

### 8.2 Query Optimization
- Task hierarchy: Recursive CTE or nested set
- Project dashboard: Aggregation queries with indexes

### 8.3 Async Operations
- Progress rollup: Queue for large projects
- Event dispatch: Async for external integrations

---

## 9. Error Handling

### 9.1 Exception Hierarchy

```
\Exception
└── KsfProjectManagementException (base)
    ├── ProjectNotFoundException
    ├── TaskNotFoundException
    ├── ValidationException
    ├── ResourceOverAllocationException
    └── DependencyCycleException
```

### 9.2 Error Responses

| Exception | HTTP Code | Message |
|-----------|-----------|---------|
| ProjectNotFoundException | 404 | Project not found |
| ValidationException | 400 | Invalid input |
| ResourceOverAllocationException | 409 | Over 100% allocation |

---

## 10. File Structure

```
ksf_ProjectManagement/
├── composer.json
├── AGENTS.md
├── ProjectDcs/
│   ├── Business Requirements.md
│   ├── Architecture.md      ← THIS FILE
│   ├── Functional Requirements.md
│   ├── Use Case.md
│   ├── Test Plan.md
│   ├── UAT Plan.md
│   └── RTM.md
└── src/Ksfraser/ProjectManagement/
    ├── Entity/
    │   ├── Project.php
    │   ├── Task.php
    │   └── ProjectAssignment.php
    ├── Service/
    │   ├── ProjectService.php
    │   ├── TaskService.php
    │   └── ResourceService.php
    ├── Repository/
    │   └── ProjectRepositoryInterface.php
    ├── Contract/
    │   ├── ProjectServiceInterface.php
    │   ├── EmployeeServiceInterface.php
    │   └── DatabaseAdapterInterface.php
    ├── Exception/
    │   ├── ProjectException.php
    │   ├── ProjectNotFoundException.php
    │   ├── TaskNotFoundException.php
    │   └── ValidationException.php
    └── Event/
        ├── ProjectEvent.php
        ├── ProjectCreatedEvent.php
        ├── ProjectUpdatedEvent.php
        ├── TaskEvent.php
        ├── TaskCreatedEvent.php
        └── EmployeeAssignedToProjectEvent.php
```

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-24*