# AGENTS.local.md — ksf_ProjectManagement package

Package-local overrides/handoffs. Shared AGENTS/AGENTS_ARCH files unchanged.

## PACKAGE PURPOSE (user directive 2026-09)
Standalone, transport-agnostic PM business logic — namespace
`Ksfraser\ProjectManagement\`, subdirs `Scheduling/`, `Gantt/`, `Dto/`,
`Repository/` (DAO). SRP classes + DI. The FA module
(`ksf_FA_ProjectManagement`) consumes this package via composer; the pure CPM
engine lives HERE, not in the FA module.

- Tasks value objects / DTOs
- CPM engine (forward+backward pass, FS/SS/FF/SF, lag, milestones, anchors)
- Gantt timeline builder (ES/EF→rows, critical highlight)
- DAOs accept `DbConnectionInterface` (FA `db_*` adapter or PDO adapter — DI)

## DELIVERY CHECK (move, not src):
- `src/Scheduling/CpmEngine.php` — engine (forward pass GREEN).
- `src/Gantt/GanttTimelineBuilder.php` — ES/EF→gantt rows.
- Composer: name `ksfraser/ksf-project-management`, PSR-4 `Ksfraser\ProjectManagement\`,
  php >=7.3, platform 7.3.33, require-dev phpunit ^9.6. `composer validate` clean.
- php -l: all `src/` + `tests/` green.

## KNOWN OPEN — backward pass slack (SHIPPING RED, user approved)
02 CpmEngineTest failures, INTENTIONALLY committed (user: "push even with the 2
RED, note incomplete + list known errors"). These are the only un-moved files
in `src/`:
- `testBackwardPassSlackCritical` — expects slack 0, engine gives -8
- `testNonCriticalSlack` — expects slack > 0, engine gives -2

Region: backward pass in `CpmEngine.php` (LF/LS init + successor anchors).
dotProject reference (user hint): its scheduler did correct forward/backward;
the LF/LS anchor for a successor with FS edge must be
`ls_succ - lag` NOT pulled to `projectEnd` for every node. Non-critical slack
must stay >= 0. Cycle detection + forward pass verified GREEN.

## NEXT SESSION — single task
Fix ONLY the backward-pass LF/LS computation in `src/Scheduling/CpmEngine.php`
(do not touch forward pass / Gantt / Dto). Re-run
`vendor/bin/phpunit --testsuite unit` → MUST be 0 failures. Then
`composer validate` + full php -l sweep + commit + tag v1.0.0 + push.
