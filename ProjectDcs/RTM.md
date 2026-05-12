# Requirements Traceability Matrix - ksf_ProjectManagement

## Document Information
- **Module**: ksf_ProjectManagement
- **Version**: 1.0.0
- **Date**: 2026-05-11
- **Status**: Implemented
- **Author**: KSFII Development Team

---

## 1. Overview

This RTM maps Business Requirements → Functional Requirements → Test Cases for traceability.

---

## 2. Requirement Mapping

### BR: Project Management
| BR ID | Description | FR | Test Cases |
|-------|-------------|-----|------------|
| BR-PM-001 | Create/edit/delete projects | FR-PM-001 | PM-PROJ-001, PM-PROJ-002, PM-SVC-PROJ-001, PM-SVC-PROJ-004 |
| BR-PM-002 | Project templates | FR-PM-001 | PM-SVC-PROJ-008 |
| BR-PM-003 | Link to CRM customer | FR-PM-001 | PM-SVC-PROJ-002, PM-INT-CRM-001 |
| BR-PM-004 | Assign project manager | FR-PM-001 | PM-PROJ-001 |
| BR-PM-005 | Status lifecycle | FR-PM-001 | PM-PROJ-003, PM-PROJ-004, PM-SVC-PROJ-003 |

### BR: Task Management
| BR ID | Description | FR | Test Cases |
|-------|-------------|-----|------------|
| BR-TASK-001 | Hierarchical tasks | FR-PM-002 | PM-TASK-001, PM-TASK-003 |
| BR-TASK-002 | Task assignments | FR-PM-002 | PM-TASK-011, PM-TASK-012, PM-SVC-TASK-005 |
| BR-TASK-003 | Progress tracking | FR-PM-002 | PM-TASK-004, PM-TASK-005, PM-SVC-TASK-003 |
| BR-TASK-004 | Estimates vs actuals | FR-PM-002 | PM-TASK-006, PM-INT-TIME-001 |
| BR-TASK-005 | Dependencies | FR-PM-002 | PM-TASK-009, PM-SVC-TASK-008 |
| BR-TASK-006 | Priority assignment | FR-PM-002 | PM-TASK-001 |

### BR: Resource Management
| BR ID | Description | FR | Test Cases |
|-------|-------------|-----|------------|
| BR-RES-001 | Employee allocation | FR-PM-003 | PM-ASSIGN-001, PM-SVC-RES-001 |
| BR-RES-002 | Capacity planning | FR-PM-003 | PM-SVC-RES-003, PM-SVC-RES-004 |
| BR-RES-003 | Over-allocation warnings | FR-PM-003 | PM-ASSIGN-002, PM-SVC-RES-002 |

### BR: Progress & Tracking
| BR ID | Description | FR | Test Cases |
|-------|-------------|-----|------------|
| BR-PROG-001 | Project progress calculation | FR-PM-004 | PM-PROJ-011, PM-PROJ-012 |
| BR-PROG-002 | Task rollup | FR-PM-004 | PM-TASK-010 |
| BR-PROG-003 | Overdue detection | FR-PM-005 | PM-PROJ-006, PM-PROJ-007, PM-PROJ-008, PM-TASK-008 |

### BR: Integration
| BR ID | Description | FR | Test Cases |
|-------|-------------|-----|------------|
| BR-INT-001 | CRM integration | FR-PM-006 | PM-INT-CRM-001, PM-INT-CRM-002, PM-INT-CRM-003 |
| BR-INT-002 | Timesheet integration | FR-PM-006 | PM-INT-TIME-001, PM-INT-TIME-002, PM-INT-TIME-003 |
| BR-INT-003 | HRM integration | FR-PM-006 | PM-INT-HRM-001, PM-INT-HRM-002, PM-INT-HRM-003 |

---

## 3. Functional Requirements Detail

| FR ID | Requirement | Priority | Status | Test Coverage |
|-------|-------------|----------|--------|---------------|
| FR-PM-001 | Project management (CRUD) | High | ✓ | PM-PROJ-001, PM-PROJ-002, PM-SVC-PROJ-001, PM-SVC-PROJ-004 |
| FR-PM-002 | Task management (hierarchical) | High | ✓ | PM-TASK-001-012, PM-SVC-TASK-001-008 |
| FR-PM-003 | Resource allocation | High | ✓ | PM-ASSIGN-001-005, PM-SVC-RES-001-006 |
| FR-PM-004 | Progress calculation | High | ✓ | PM-PROJ-011, PM-PROJ-012, PM-TASK-010 |
| FR-PM-005 | Overdue detection | Medium | ✓ | PM-PROJ-006-008, PM-TASK-008 |
| FR-PM-006 | Time integration | Medium | ✓ | PM-INT-TIME-001-003 |

---

## 4. Event Coverage

| Event | Business Trigger | Test Cases | Status |
|-------|------------------|------------|--------|
| project.created | New project | PM-EVENT-001 | ✓ |
| project.updated | Project update | PM-EVENT-002 | ✓ |
| project.completed | Mark complete | PM-EVENT-003 | ✓ |
| task.created | New task | PM-EVENT-004 | ✓ |
| task.progress_updated | Progress change | PM-EVENT-005 | ✓ |
| employee.assigned_to_project | Resource assign | PM-EVENT-006 | ✓ |

---

## 5. Integration Dependencies

### Provided To
| Module | Data | Events |
|--------|------|--------|
| ksf_FA_ProjectManagement | Projects, Tasks | project.*, task.* |
| ksf_CRM | Customer projects | project.created, project.completed |
| ksf_Timesheets | Task time entries | task.progress_updated |
| ksf_Calendar | Due dates | task.created, task.progress_updated |

### Consumed From
| Module | Data | Interface |
|--------|------|-----------|
| ksf_HRM | Employees, Skills | EmployeeServiceInterface |
| ksf_CRM | Customers | CustomerServiceInterface |
| ksf_Timesheets | Actual hours | TimeEntryServiceInterface |

---

## 6. Test Status Summary

| Category | Total | Passed | Failed | Coverage |
|----------|-------|--------|--------|----------|
| Entity Tests | 28 | - | - | 100% |
| Service Tests | 22 | - | - | 90% |
| Event Tests | 6 | - | - | 100% |
| Integration Tests | 9 | - | - | 80% |
| **Total** | **65** | - | - | **~90%** |

---

## 7. Defects Linked to Requirements

| Defect ID | Requirement | Severity | Status |
|-----------|-------------|----------|--------|
| - | - | - | - |

*No open defects*

---

## 8. Sign-off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Business Analyst | | | |
| Technical Lead | | | |
| QA Lead | | | |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*