# Use Cases - ksf_ProjectManagement

## UC-PM-001: Create New Project
**Actor**: Project Manager

**Preconditions**: User has project create permission

**Flow**:
1. Navigate to Projects > New Project
2. Enter project details:
   - Project name
   - Customer (from ksf_CRM, optional)
   - Start date, end date
   - Project manager
   - Priority
   - Budget
3. Add team members from ksf_HRM employee list
4. Create initial milestones
5. Optionally create from template
6. Save project
7. System triggers workflow if required (ksf_Workflow)
8. Team members notified

**Alternate Flow - From CRM Opportunity**:
1. Sales rep marks opportunity as 'Closed Won'
2. System creates project automatically
3. Pre-filled with customer data
4. Assigned to delivery manager
5. Project template applied

---

## UC-PM-002: Add Tasks to Project
**Actor**: Project Manager, Team Lead

**Flow**:
1. Navigate to project > Tasks
2. Create task:
   - Task name
   - Description
   - Assigned to (from ksf_HRM employees)
   - Start/due dates
   - Estimated hours
   - Priority
   - Dependencies (other tasks)
3. Set parent task if subtask
4. Save task
5. System creates calendar event (ksf_Calendar)
6. Assigned employee notified

---

## UC-PM-003: Task Progress Update
**Actor**: Assigned Employee, Team Lead

**Flow**:
1. Employee navigates to assigned tasks
2. Updates task progress:
   - Percentage complete (0-100%)
   - Hours worked today
   - Status change
   - Notes/comments
3. Save update
4. System:
   - Updates parent task progress (roll-up)
   - Updates project overall progress
   - Logs to timesheet if hour logged (ksf_Timesheets)
   - Notifies manager if milestone reached

---

## UC-PM-004: Resource Allocation
**Actor**: Resource Manager, Project Manager

**Flow**:
1. Navigate to Resource Management
2. View all projects and resource needs
3. Allocate employee to project:
   - Select employee (ksf_HRM)
   - Set allocation % (e.g., 50% = 4 hours/day)
   - Set start/end date
   - Define role on project
4. System checks:
   - Employee capacity (ksf_HRM)
   - Existing allocations
   - Over-allocation warnings
5. Save allocation
6. Employee sees project in their task list
7. Calendar updated with project commitment

---

## UC-PM-005: Project Budget Tracking
**Actor**: Project Manager, Finance

**Flow**:
1. Project budget set at creation
2. As time is logged (ksf_Timesheets):
   - System calculates labor cost
   - Labor cost = hours × employee rate (from ksf_HRM)
3. Project manager sees:
   - Budget vs actual (labor + expenses)
   - Projected cost at completion
   - Budget variance
4. Alerts when approaching/exceeding budget:
   - 75% budget warning
   - 90% critical
   - 100% over-budget notification
5. Finance sees profitability per project

---

## UC-PM-006: Milestone Management
**Actor**: Project Manager, Customer (via portal)

**Flow**:
1. Project manager defines milestones:
   - Milestone name
   - Target date
   - Deliverables
   - Status
2. When tasks under milestone complete:
   - Milestone status → 'Ready for Review'
3. Project manager marks milestone complete
4. System:
   - Updates project progress
   - Notifies stakeholders (ksf_EmailManager)
   - Creates calendar event for review meeting
5. Customer sees milestone completion (via ksf_WP_CustomerPortal)

---

## UC-PM-007: Project Status Reporting
**Actor**: Project Manager, Management

**Trigger**: Weekly status report, on-demand

**Flow**:
1. Navigate to project > Reports
2. Select report type:
   - Status summary
   - Task completion chart
   - Resource utilization
   - Budget tracking
   - Gantt view
3. Set date range
4. Generate report
5. Export options: PDF, Excel
6. Share with stakeholders

**Scheduled Report**:
1. Project manager sets up weekly reports
2. System auto-generates and emails every Monday
3. Recipients include project sponsor, management

---

## UC-PM-008: Task Dependency Management
**Actor**: Project Manager

**Flow**:
1. When creating/editing task:
   - Add dependency to another task
   - Dependency types:
     - Finish to Start (default)
     - Start to Start
     - Finish to Finish
2. System enforces:
   - Dependent task can't start until predecessor done
   - If predecessor delayed → successor auto-shifted
3. Gantt chart visualizes dependencies
4. Critical path calculated and highlighted

---

## UC-PM-009: Employee Workload View
**Actor**: Resource Manager, Project Manager

**Flow**:
1. Navigate to Resource Management > Workload
2. Select employee or team
3. View workload calendar:
   - Day/week/month view
   - Shows all task assignments
   - Shows capacity vs allocated
4. Identify over-allocation:
   - Tasks highlighted red
   - System suggests reallocation
5. Drag-drop tasks to reschedule
6. System updates all affected tasks

---

## UC-PM-010: Project Closure
**Actor**: Project Manager, Customer (approval)

**Trigger**: All tasks complete, deliverables delivered

**Flow**:
1. Project manager initiates closure
2. System checks:
   - All tasks 100% complete
   - All milestones achieved
   - Budget reconciled
3. Project manager completes:
   - Final project report
   - Lessons learned (ksf_Notes)
   - Handoff documentation
4. Customer approval (ksf_Workflow)
5. On approval:
   - Project status → 'Completed'
   - Team members released
   - Project archived
   - Customer satisfaction survey triggered

---

## UC-PM-011: Project Template Usage
**Actor**: Project Manager, Admin

**Admin Flow (Create Template)**:
1. Create project with standard structure
2. Save as template:
   - Include tasks, milestones
   - Include default team roles
   - Include timelines
3. Template saved for reuse

**User Flow (Create from Template)**:
1. New Project > From Template
2. Select template
3. Pre-filled project created
4. Modify dates, customer, team
5. Create project

---

## UC-PM-012: Time Entry from Project Task
**Actor**: Employee

**Trigger**: Employee working on project task

**Flow**:
1. Employee sees assigned tasks (from ksf_HRM allocation)
2. Employee logs time:
   - Select project, task
   - Date
   - Hours worked
   - Description
3. Save time entry (ksf_Timesheets)
4. System:
   - Updates task actual hours
   - Updates project budget consumed
   - Calculates labor cost
5. Manager reviews and approves timesheet

*Document Version: 1.0.0*
*Last Updated: 2026-05-11*