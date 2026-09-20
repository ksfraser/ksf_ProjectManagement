# UAT Plan - ksf_ProjectManagement

## Document Information
- **Module**: ksf_ProjectManagement
- **Version**: 1.0.0
- **Date**: 2026-05-11

## 1. UAT Overview

### 1.1 Purpose
Validate PM functionality: project creation, task management, resource allocation.

### 1.2 Modules Integrated
- ksf_CRM
- ksf_Timesheets
- ksf_Calendar

## 2. UAT Scenarios

### UAT-PM-001: Create Project from CRM Opportunity
**Scenario**: Convert won opportunity to project

**Prerequisites**: CRM opportunity at "Closed Won" stage

**Steps**:
1. Opportunity marked as Closed Won in CRM
2. System creates project automatically
3. Project pre-filled with:
   - Customer from opportunity
   - Project manager assigned
   - Template tasks created
4. Verify project in PM list

**Expected Results**:
- [ ] Project created
- [ ] Customer linked
- [ ] Template applied
- [ ] Manager notified

**Status**: ☐ Pass  ☐ Fail  ☐ N/A

---

### UAT-PM-002: Task Progress Update
**Scenario**: Employee updates task progress

**Steps**:
1. Open assigned task
2. Update progress to 50%
3. Log hours worked (4 hours)
4. Add notes
5. Save
6. Verify:
   - Task progress updated
   - Parent task progress recalculated
   - Project progress updated
   - Time entry created in Timesheets

**Expected Results**:
- [ ] Progress = 50%
- [ ] Parent task shows rolled-up progress
- [ ] Time entry logged
- [ ] Project % updated

**Status**: ☐ Pass  ☐ Fail  ☐ N/A

---

### UAT-PM-003: Resource Allocation
**Scenario**: Allocate employee to project

**Steps**:
1. Navigate to Resource Management
2. Select employee
3. Allocate to project at 50% for 3 months
4. Save
5. Verify employee sees project in task list
6. Verify calendar shows availability

**Expected Results**:
- [ ] Assignment created
- [ ] Employee has capacity for other work
- [ ] Tasks visible to employee

**Status**: ☐ Pass  ☐ Fail  ☐ N/A

---

### UAT-PM-004: Budget Tracking
**Scenario**: Monitor project budget

**Steps**:
1. Open project with budget set
2. Submit timesheets against project tasks
3. View budget tracking:
   - Budget vs Actual
   - Projected cost at completion
   - Variance

**Expected Results**:
- [ ] Actual hours/costs calculated
- [ ] Labor cost computed
- [ ] Budget warnings shown at 75%, 90%
- [ ] Variance displayed

**Status**: ☐ Pass  ☐ Fail  ☐ N/A

---

### UAT-PM-005: Project Closure
**Scenario**: Close completed project

**Steps**:
1. Verify all tasks 100% complete
2. Mark project as Completed
3. System prompts for:
   - Final report
   - Lessons learned
4. Complete closure
5. Verify:
   - Project status = Completed
   - Team members released
   - Project archived

**Expected Results**:
- [ ] Status = Completed
- [ ] Archive created
- [ ] Team released

**Status**: ☐ Pass  ☐ Fail  ☐ N/A

---

## 3. Sign-Off

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Business Owner | | | |
| UAT Lead | | | |
| Technical Lead | | | |

---

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*