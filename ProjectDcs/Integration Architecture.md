# Architecture - ksf_Integration

## Overview
All HRM modules integrate through a central Task system. Tasks create Calendar entries and Timesheet records.

## Central Task System (ksf_ProjectManagement)

### Task States
- Pending → In Progress → Completed / Rejected

### Task Types
- **Project Task**: From project management
- **HRM Task**: From HR modules (created dynamically)
- **Approval Task**: Requires manager action
- **Review Task**: Performance/goals related
- **Training Task**: Learning/development
- **Document Task**: Read/sign requirements

## Integration Map

```
ksf_Recruitment ──→ Create Onboarding Project ──→ ksf_Onboarding
                    ↓
                ksf_ProjectManagement (tasks)
                    ↓
                ksf_OrgChart (assign to manager/HR)
                    ↓
                ksf_HRM (employee record)
                    ↓
                ksf_Timesheets (weekly)
                    ↓
                ksf_FA (GL entries)
```

## Task Creation Triggers

### From Leave Request (ksf_Leave)
1. Employee submits leave request
2. **Task created** for approving manager
3. **Calendar entries** "Leave" calendar
4. **Timesheet records** in pending
5. On approval → Timesheet finalized
6. On rejection → Task closed, employee notified

### From Performance (ksf_Performance)
1. Strategic goal cascades from above
2. **Goals Task** created for employee
3. Team goals push to team members → **Goals Tasks**
4. Training linked as training goal → **Training Tasks**
5. Review period → **Review Tasks**

### From Onboarding (ksf_Onboarding)
1. New hire creates **Onboarding Project**
2. Template tasks assigned:
   - HR tasks → HR team
   - IT tasks → IT team  
   - Manager tasks → Manager
   - Employee tasks → New employee
3. Document signing → **Document Tasks**
4. Completion marks task done → Creates timesheet entry

### From Timesheets
1. Employee records time on task
2. Activity code → GL mapping
3. **Weekly submission** → Manager approval task
4. Manager approves → **Billing records** + **Payroll records**

## Employee Task Dashboard

### Screen Features
- My Tasks (all assigned to employee)
- Filter by: Type, Project, Status, Due Date
- Filter by: HRM module (Recruitment, Onboarding, Leave, Performance)
- Quick actions: Start, Complete, Reject

### Sample Filters
```
- Type: Approval | Review | Document | Training
- From Project: Onboarding [Employee Name] | Performance Review
- Status: Pending | In Progress | Overdue
- Module: All | Recruitment | Leave | Timesheets
```

## Document Management Integration

### Document Tasks
- Read and acknowledge
- Fill out form
- Sign document
- Upload completed document

### Integration
- ksf_HRM: Employment contract, Tax forms
- ksf_Onboarding: Welcome package, Policies
- ksf_Performance: Review forms

## Job Descriptions (New Module)

### Links To
- ksf_Recruitment: Job postings
- ksf_Performance: Competencies required
- ksf_Teams: Role requirements
- ksf_HRM: Position grade

### Features
- Standard job description template
- Required competencies
- Responsibilities
- Salary grade alignment
- Team and reporting structure

## Data Flow Examples

### Leave Request Flow
```
Employee: Request Mar 15-17 (Vacation)
    ↓
ksf_Leave: Validate balance
    ↓
Create Task: "Approve leave - Employee Name"
    ↓
ksf_ProjectManagement: Task assigned to manager
    ↓
ksf_Calendar: Add "Leave" calendar entry Mar 15-17
    ↓
ksf_Timesheets: Create pending time records
    ↓
Manager: Approve
    ↓
Task: Complete → Generate payroll liability
```

### Onboarding Flow
```
Recruitment: Mark candidate "Hired"
    ↓
ksf_Onboarding: Create Onboarding Project for Employee
    ↓
ksf_ProjectManagement: 15 pre-defined tasks
    ↓
Task Assignment:
  - Day -7: Sign contract (Employee)
  - Day -5: Setup laptop (IT)
  - Day -5: Create email (IT)
  - Day -3: Benefits enrollment (HR)
  - Day 0: First day meeting (Manager)
    ↓
As tasks complete → Record time
    ↓
All tasks complete → Employee fully onboarded
```

### Performance Goals Flow
```
Manager: Set strategic goal for team
    ↓
ksf_Performance: Create team goal
    ↓
ksf_ProjectManagement: Push to team members
    ↓
Team Members: See goal as task
    ↓
Create sub-goals as tasks
    ↓
Progress tracked via task completion
    ↓
Review period → Review task
```