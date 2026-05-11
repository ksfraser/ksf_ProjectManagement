# Functional Requirements - ksf_ProjectManagement

## Document Information
- **Module**: ksf_ProjectManagement
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

## 1. Overview

### 1.1 Purpose
ksf_ProjectManagement provides enterprise project management with tasks, resources, and time tracking.

### 1.2 Scope
- Project creation and templates
- Hierarchical task management
- Resource allocation
- Progress tracking
- Budget monitoring

## 2. Core Entities

### 2.1 Project

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| name | string | Yes | Project name |
| description | string | No | Description |
| customer_id | string | No | FK to CRM Customer |
| project_manager | string | Yes | User ID |
| start_date | Date | Yes | Start date |
| end_date | Date | No | End date |
| budget | float | No | Budget amount |
| priority | string | Yes | Low/Medium/High/Critical |
| status | string | Yes | Planning/Active/On Hold/Completed/Cancelled |
| created_at | DateTime | Yes | Auto |
| updated_at | DateTime | Yes | Auto |

### 2.2 Task

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| project_id | string | Yes | FK to Project |
| parent_task_id | string | No | FK to parent Task |
| name | string | Yes | Task name |
| description | string | No | Description |
| assigned_to | string | No | User ID |
| start_date | Date | No | Start date |
| due_date | Date | No | Due date |
| estimated_hours | float | No | Estimated effort |
| actual_hours | float | No | Actual hours |
| progress | int | Yes | 0-100% |
| priority | string | Yes | Low/Medium/High/Critical |
| status | string | Yes | Not Started/In Progress/Completed/Cancelled |
| created_at | DateTime | Yes | Auto |
| updated_at | DateTime | Yes | Auto |

### 2.3 ProjectAssignment

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| id | string | Yes | UUID |
| project_id | string | Yes | FK to Project |
| employee_id | string | Yes | Employee ID (from HRM) |
| role | string | No | Role on project |
| allocation_percent | int | Yes | 0-100% |
| start_date | Date | No | Assignment start |
| end_date | Date | No | Assignment end |
| is_active | bool | Yes | Default true |

## 3. Functional Requirements

### FR-PM-001: Project Management
**Requirement**: System shall create and manage projects.

**Features**:
- Create/edit/delete projects
- Project templates
- Link to CRM customer
- Assign project manager
- Set dates, budget, priority
- Status lifecycle

### FR-PM-002: Task Management
**Requirement**: System shall manage hierarchical tasks.

**Features**:
- Create tasks with parent-child relationships
- Assign tasks to employees
- Track progress (0-100%)
- Set estimates and actuals
- Dependencies between tasks
- Priority assignment

### FR-PM-003: Resource Allocation
**Requirement**: System shall allocate employees to projects.

**Features**:
- Assign employees with allocation %
- Track active/ended assignments
- Calculate workload from allocation
- Over-allocation warnings

### FR-PM-004: Progress Calculation
**Requirement**: System shall calculate project progress.

**Features**:
- Aggregate task progress to project
- Roll-up parent task progress from children
- Visual progress indicators

### FR-PM-005: Overdue Detection
**Requirement**: System shall identify overdue tasks.

**Features**:
- Compare due dates to current date
- Mark tasks past due date as overdue
- Notification on overdue

### FR-PM-006: Time Integration
**Requirement**: System shall integrate with timesheets.

**Features**:
- Link time entries to tasks
- Update actual hours from timesheets
- Calculate labor cost

## 4. Business Logic

### 4.1 Duration Calculation
```php
public function getDuration(): int|null
// Returns days between start_date and end_date
```

### 4.2 Overdue Detection
```php
public function isOverdue(): bool
// Returns true if past end_date and not completed
```

### 4.3 Active Status
```php
public function isActive(): bool
// Returns true if within date range and not completed/cancelled
```

## 5. Integration Events (PSR-14)

| Event | Trigger |
|-------|---------|
| `project.created` | New project |
| `project.updated` | Project updated |
| `project.completed` | Project marked complete |
| `task.created` | New task |
| `task.progress_updated` | Task progress changed |
| `employee.assigned_to_project` | Resource assigned |

## 6. Composer Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| ksfraser/exceptions | ^1.3 | Exception hierarchy |
| psr/event-dispatcher | ^2.0 | PSR-14 events |
| psr/log | ^3.0 | Logging |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*