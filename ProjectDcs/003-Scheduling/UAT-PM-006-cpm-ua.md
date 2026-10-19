# UAT-PM-006 — User Acceptance — CPM schedule UI (module preview)

> **COPY — do not edit here.**
> The canonical copy of this document is `ksf_FA_ProjectManagement/ProjectDcs/003-Scheduling/UAT-PM-006-cpm-ua.md` in the **FA wrapper repo**
> (`ksf_FA_ProjectManagement`). This file is a verbatim copy kept in the **business
> logic repo** (`ksf_ProjectManagement`) so the standalone Packagist package can be
> developed, reviewed and released against the same requirements without pulling in
> the FA module.
>
> **Edit the canonical copy only, then re-copy it here.** The two are not linked and
> will drift if both are edited. Corrections made in this copy alone will be lost the
> next time the canonical file is re-synced.

## Repository note (this copy)
The `Env` row below names the FA wrapper, because a UAT pass is exercised through
the shipped FA module and its UI. The canonical copy lives in the FA wrapper repo
(see the header note); the corrections applicable here are that this repo supplies
no UI and no environment of its own, and the module under test in that environment
is built on the `ksfraser/ksf-project-management` package from THIS repo.

| | |
|---|---|
| ID | UAT-PM-006 |
| Related | BR-PM-006, UT-PM-006 |
| Env | UAT-latest (ksf_FA_ProjectManagement) — FA 2.4.19, shipped module, real MySQL |

## UAT-006-001 — create schedule from project
- Steps: Open Project → Schedule tab → "Generate" (durations + dependencies).
- Pass: forward/backward passes give ES/EF/LS/LF/slack/critical columns; the
  critical path is highlighted (≤0.5d slack); project_duration shown.
- Verify a milestone row shows duration 0 and ef==es.

## UAT-006-002 — edit milestone/constraint
- Set finish_no_earlier_than on a task and re-Generate: EF reflects anchor;
  critical set recomputes.

## UAT-006-003 — cycle rejection UX
- Steps: Add dependency that would close a cycle; save.
- Pass: no write; UI shows cycle path message (ok=false) without exception.

## UAT-006-004 — data isolation
- Confirm no FA `db_*` calls, no hooks fired from CpmEngine itself; transport
  and hooks only via SchedulingService/Repository at the FA boundary.
