# Test Plan - ksf_ProjectManagement

## Document Information
- **Module**: ksf_ProjectManagement
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Overview

### 1.1 Purpose
This test plan defines the testing strategy for ksf_ProjectManagement module, covering unit tests, integration tests, and acceptance criteria.

### 1.2 Scope
- Entity behavior testing
- Service layer testing
- Event dispatching
- Integration points with ksf_HRM, ksf_CRM, ksf_Timesheets

---

## 2. Test Strategy

### 2.1 Test Pyramid
```
        ┌─────────────┐
        │  Acceptance │  ← UAT scenarios
        ├─────────────┤
        │ Integration │  ← Service + DB adapter
        ├─────────────┤
        │    Unit     │  ← Entity, Service isolated
        └─────────────┘
```

### 2.2 Coverage Targets
| Layer | Target |
|-------|--------|
| Entity | 100% |
| Service | 90% |
| Events | 100% |
| Integration | 80% |

---

## 3. Unit Tests

### 3.1 Project Entity Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-PROJ-001 | Create project with valid data | Project created with ID |
| PM-PROJ-002 | Create project without required fields | ValidationException |
| PM-PROJ-003 | Set project status to Active | Status updated, dates validated |
| PM-PROJ-004 | Set project status to Completed | End date set, progress = 100 |
| PM-PROJ-005 | Calculate duration | Returns days between dates |
| PM-PROJ-006 | Check overdue on past end date | Returns true |
| PM-PROJ-007 | Check overdue on future date | Returns false |
| PM-PROJ-008 | Check overdue on completed project | Returns false |
| PM-PROJ-009 | Add task to project | Task linked, children updated |
| PM-PROJ-010 | Assign employee | Assignment created |
| PM-PROJ-011 | Calculate progress with no tasks | Returns 0 |
| PM-PROJ-012 | Calculate progress with mixed tasks | Returns weighted average |

### 3.2 Task Entity Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-TASK-001 | Create task with valid data | Task created |
| PM-TASK-002 | Create task without project | ValidationException |
| PM-TASK-003 | Set parent task | Hierarchy established |
| PM-TASK-004 | Update progress 0-100 | Progress updated, event dispatched |
| PM-TASK-005 | Update progress > 100 | ValidationException |
| PM-TASK-006 | Complete task | Status = Completed |
| PM-TASK-007 | Complete task with incomplete children | ValidationException |
| PM-TASK-008 | Check overdue with past due date | Returns true |
| PM-TASK-009 | Get children tasks | Returns array of children |
| PM-TASK-010 | Calculate rollup progress | Returns parent from children |
| PM-TASK-011 | Assign employee | AssignedTo set |
| PM-TASK-012 | Clear assignment | AssignedTo = null |

### 3.3 ProjectAssignment Entity Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-ASSIGN-001 | Create assignment valid data | Assignment created |
| PM-ASSIGN-002 | Create assignment > 100% | ValidationException |
| PM-ASSIGN-003 | Set inactive assignment | isActive = false |
| PM-ASSIGN-004 | Check active status within dates | Returns true |
| PM-ASSIGN-005 | Check active status past end date | Returns false |

---

## 4. Service Layer Tests

### 4.1 ProjectService Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-SVC-PROJ-001 | Create project with all fields | Project persisted, event dispatched |
| PM-SVC-PROJ-002 | Create project linked to customer | CustomerId set |
| PM-SVC-PROJ-003 | Update project status | Status changed, side effects |
| PM-SVC-PROJ-004 | Delete project with tasks | Cascade delete or error |
| PM-SVC-PROJ-005 | Get project by ID | Returns Project or null |
| PM-SVC-PROJ-006 | List projects by status | Returns filtered array |
| PM-SVC-PROJ-007 | List projects by manager | Returns filtered array |
| PM-SVC-PROJ-008 | Duplicate project template | New project with tasks |

### 4.2 TaskService Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-SVC-TASK-001 | Create task linked to project | Task created |
| PM-SVC-TASK-002 | Create subtask with parent | Hierarchy linked |
| PM-SVC-TASK-003 | Update task progress | Progress updated, rollup calculated |
| PM-SVC-TASK-004 | Reorder tasks | Sort order updated |
| PM-SVC-TASK-005 | Assign task to employee | AssignedTo set, notification |
| PM-SVC-TASK-006 | Remove task assignment | AssignedTo cleared |
| PM-SVC-TASK-007 | Delete task with children | Error (has dependencies) |
| PM-SVC-TASK-008 | Get task tree for project | Returns nested structure |

### 4.3 ResourceService Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-SVC-RES-001 | Assign employee to project | ProjectAssignment created |
| PM-SVC-RES-002 | Assign employee exceeding 100% | ValidationException |
| PM-SVC-RES-003 | Calculate total allocation | Returns sum of % |
| PM-SVC-RES-004 | Get resource workload | Returns allocation data |
| PM-SVC-RES-005 | Reassign employee | Assignment updated |
| PM-SVC-RES-006 | End assignment | isActive = false, endDate set |

---

## 5. Event Tests

### 5.1 Event Dispatching Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-EVENT-001 | Create project dispatches event | ProjectCreatedEvent fired |
| PM-EVENT-002 | Update project dispatches event | ProjectUpdatedEvent fired |
| PM-EVENT-003 | Complete project dispatches event | ProjectCompletedEvent fired |
| PM-EVENT-004 | Create task dispatches event | TaskCreatedEvent fired |
| PM-EVENT-005 | Update progress dispatches event | TaskProgressUpdatedEvent fired |
| PM-EVENT-006 | Assign employee dispatches event | EmployeeAssignedToProjectEvent fired |

### 5.2 Event Payload Tests

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-EVENT-101 | ProjectCreatedEvent has project data | Event contains Project |
| PM-EVENT-102 | TaskProgressUpdatedEvent has old/new progress | Event contains both values |
| PM-EVENT-103 | EmployeeAssignedToProjectEvent has assignment | Event contains ProjectAssignment |

---

## 6. Integration Tests

### 6.1 ksf_HRM Integration

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-INT-HRM-001 | Get employee for assignment | Returns Employee entity |
| PM-INT-HRM-002 | Validate employee exists | Returns true/false |
| PM-INT-HRM-003 | Get employee skills | Returns skill array |

### 6.2 ksf_CRM Integration

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-INT-CRM-001 | Link project to customer | CustomerId set |
| PM-INT-CRM-002 | Get customer projects | Returns project list |
| PM-INT-CRM-003 | Project status change notification | Customer notified |

### 6.3 ksf_Timesheets Integration

| Test ID | Description | Expected Result |
|---------|-------------|-----------------|
| PM-INT-TIME-001 | Link time entry to task | Task.actualHours updated |
| PM-INT-TIME-002 | Get task time entries | Returns time entries |
| PM-INT-TIME-003 | Calculate task labor cost | Returns cost from rate |

---

## 7. Test Data

### 7.1 Fixtures

```php
$projectData = [
    'id' => 'proj-001',
    'name' => 'Test Project',
    'project_manager_id' => 'emp-001',
    'start_date' => '2026-01-01',
    'end_date' => '2026-03-31',
    'budget' => 50000.00,
    'priority' => 'high',
    'status' => 'active'
];

$taskData = [
    'id' => 'task-001',
    'project_id' => 'proj-001',
    'name' => 'Phase 1 Task',
    'assigned_to' => 'emp-002',
    'due_date' => '2026-02-15',
    'estimated_hours' => 40,
    'progress' => 50
];
```

### 7.2 Test Employees

| ID | Name | Role |
|----|------|------|
| emp-001 | John Manager | Project Manager |
| emp-002 | Jane Developer | Developer |
| emp-003 | Bob Designer | Designer |

---

## 8. Test Execution

### 8.1 Commands

```bash
# Run all tests
composer test

# Run unit tests only
./vendor/bin/phpunit tests/Unit

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

### 8.2 CI/CD Integration

```yaml
# .github/workflows/test.yml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Run PHPUnit
        run: ./vendor/bin/phpunit --coverage-text
```

---

## 9. Defect Management

### 9.1 Severity Levels

| Level | Definition | SLA |
|-------|------------|-----|
| Critical | System unusable | 24h |
| High | Core feature broken | 48h |
| Medium | Feature impaired | 1 week |
| Low | Cosmetic/minor | 2 weeks |

### 9.2 Bug Template

```markdown
**ID**: PM-BUG-XXX
**Title**: 
**Steps to Reproduce**:
1.
2.
3.
**Expected**:
**Actual**:
**Severity**:
**Assignee**:
```

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*