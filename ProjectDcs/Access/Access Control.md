# Project Management Module - Access Control Specification

## Document Information

| Field | Value |
|-------|-------|
| Document Title | Access Control Specification |
| Module | ksf_ProjectManagement |
| Version | 1.0.0 |
| Author | KSF Development Team |
| Last Updated | 2026-05-24 |

### RBAC Model Alignment

This document describes the conceptual access control model. Implementation uses ksfraser/rbac for enforcement:

- **Teams**: Each role below maps to one or more RBAC teams. Team membership determines record-level access.
- **SQL Enforcement**: All access checks use the RBAC standard JOIN pattern against 0_rbac_record_access.
- **Projections**: Field visibility is controlled by DTO projections (PUBLIC vs FULL per entity).
- **Default Deny**: Absence of an RBAC xref grant = no access, regardless of role definition.

---

## 1. Access Control Overview

### 1.1 Purpose

Access control for ksf_ProjectManagement:
- **Project Managers** fully manage assigned projects
- **Team Members** see and update assigned tasks
- **Stakeholders** view project progress
- **Executives** see portfolio overview

### 1.2 Key Principles

| Principle | Description |
|-----------|-------------|
| Project-Based | Access tied to project membership |
| Role Hierarchy | PM > Team Lead > Team Member > Stakeholder |
| Contract Linkage | Projects associated to contracts affect visibility |
| Time-Based | Access based on project phase |

---

## 2. Role Definitions

| Role | Access Level |
|------|--------------|
| Executive Sponsor | View assigned projects |
| Project Sponsor | View + approve milestones |
| Project Manager | Full project management |
| Team Lead | Manage team tasks + own tasks |
| Team Member | Own tasks only |
| Stakeholder | Read-only project view |
| Resource Manager | All project resources |

---

## 3. Project-Level Access

### 3.1 Project Fields

| Field | Team Member | Team Lead | PM | Sponsor | Executive |
|-------|-------------|-----------|-----|---------|-----------|
| Name | Read | Read | Read/Write | Read | Read |
| Description | Read | Read | Read/Write | Read | Read |
| Status | Read | Read | Read/Write | Read | Read |
| Budget | Hidden | Read | Read/Write | Read | Read |
| Timeline | Read | Read | Read/Write | Read | Read |
| Risks | Hidden | Read | Read/Write | Read | Read |
| Contracts | Hidden | Read | Read | Read | Read |

### 3.2 Task Fields

| Field | Team Member | Team Lead | PM |
|-------|-------------|-----------|-----|
| Task Name | Read (own) | Read (team) | Read/Write |
| Description | Read (own) | Read (team) | Read/Write |
| Status | Read/Write (own) | Read/Write (team) | Read/Write |
| Hours | Read/Write (own) | Read/Write (team) | Read/Write |
| Dependencies | Hidden | Read | Read/Write |

---

## 4. Project Manager Visibility

### 4.1 Scope

Project Managers access:
1. Projects assigned to them
2. All tasks within those projects
3. Team resource allocation
4. Project reports and analytics

### 4.2 Cross-Project View

Resource Managers see:
- All projects' resource allocation
- Team member availability
- Cross-project dependencies

---

## 5. Contract Linkage

### 5.1 Project-Contract Relationship

Projects are linked to Contracts (ksf_WarrantyManagement):
- Contract visibility rules apply to project visibility
- Certain users see projects based on contract access
- Project billing tied to contract terms

### 5.2 Visibility Rules

| If User Has Access To | They Can See |
|------------------------|--------------|
| Contract | Associated project metadata |
| Contract + Project Role | Full project access |
| Project Only | Project details, no billing |
| Executive Role | Project portfolio summary |

---

## 6. Team Member Access

### 6.1 Task Assignment

Team Members see:
- Tasks assigned to them
- Dependencies (read-only)
- Project timeline (read-only)
- Cannot: Create tasks, modify project settings

### 6.2 Time Tracking

| Field | Team Member | Team Lead | PM |
|-------|-------------|-----------|-----|
| Log Hours | Read/Write (own) | Read/Write (team) | Read/Write |
| Timesheet | Read (own) | Read (team) | Read/Write |
| Billable Rate | Hidden | Read | Read |
| Overtime | Hidden | Read | Read/Write |

---

## 7. Family Company Considerations

### 7.1 Parent-Child Visibility

For family companies with parent/child structure:
- Parent company executives see child company projects
- Unless `gift_flag` on sensitive projects

### 7.2 Gift Flag

Project-related gifts, bonuses, or confidential expenses:
- Normal project access by default
- With `gift_flag=true`: Only PM + Project Sponsor可见

---

## 8. WordPress Integration (WP_OrgChart)

### 8.1 Project Views

Via ksf_WP_OrgChart, users access:
- Assigned projects (all roles)
- Team member project lists (PM+)
- Cannot access billing or sensitive data

---

## 9. FA Integration

### 9.1 FrontAccounting Project Module

Access to ksf_FA_ProjectManagement:
- Linked to FA GL for project accounting
- Billing visible to finance roles
- Project costs visible to PM + Finance

---

## 11. Inheritance and Cascade

### 11.1 Parent-Child Grant Inheritance

When a team is granted access to a project:
- All child tasks NOT YET completed inherit the same capability set
- Completed tasks become view-only (can_view=1, can_edit=0)
- Cancelled tasks are excluded from inheritance
- Deep inheritance capped at 5 levels (configurable)

### 11.2 Org Chart Integration

When ksf_OrgChart is active:
- Project Manager's org_direct team members get PUBLIC access to the PM's projects
- Org cascade depth is configurable (default: 5 levels via indirect_lX teams)
- Resource Managers see cross-project resource allocation

---

## 12. Revision History

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 1.0.0 | May 2026 | KSF Development Team | Initial specification |