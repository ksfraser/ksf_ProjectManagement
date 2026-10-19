# ARCH-PM-006 — Standalone CPM/Scheduling Package (design)

> **COPY — do not edit here.**
> The canonical copy of this document is `ksf_FA_ProjectManagement/ProjectDcs/003-Scheduling/ARCH-PM-006-standalone-cpm-package.md` in the **FA wrapper repo**
> (`ksf_FA_ProjectManagement`). This file is a verbatim copy kept in the **business
> logic repo** (`ksf_ProjectManagement`) so the standalone Packagist package can be
> developed, reviewed and released against the same requirements without pulling in
> the FA module.
>
> **Edit the canonical copy only, then re-copy it here.** The two are not linked and
> will drift if both are edited. Corrections made in this copy alone will be lost the
> next time the canonical file is re-synced.

## Repository note (this copy)
This document describes the **standalone package**, which is THIS repo
(`ksf_ProjectManagement`). The two path references in *Consumed by* below were
written in the FA wrapper and are corrected here:

| Documented (FA wrapper) | In this repo |
|---|---|
| FA module `ksf_FA_ProjectManagement` (composer require; DI at boot) | consumer of the released `ksfraser/ksf-project-management` package |
| `ksf_FA_ProjectManagement/tests` (pure engine tests, no FA harness) | `tests/` here (`tests/Unit/Scheduling/CpmEngineTest.php` etc.) |

Note the namespace drift too: this document proposes `Ksfraser\CommonPm\`, but
the package as actually built and shipped uses `Ksfraser\ProjectManagement\`
mapped to `src/`. The shipped namespace is what `composer.json` declares.

## Context
`ksfraser/ksf-common-pm` (type `library`, Packagist). Pure, framework-neutral
CPM business logic extracted from the FA module. Mirrors the proven
`ksf-common-db` playbook: Composer package, PSR-4, PHP 7.3 floor, zero FA
runtime dependency; transport injected at the FA boundary.

## Package structure (PSR-4 `Ksfraser\CommonPm\` → `src/`)
```
src/
  Cpm/
    CpmEngine.php          # pure forward/backward CPM engine (return-array)
    EdgeType.php           # constants FS/SS/FF/SF
    ScheduleResult.php     # value object wrapping engine result
    TaskDependency.php     # predicate-safe DTO (pred/succ/type/lag)
    ScheduleRepositoryInterface.php   # persist read/write contract (DI)
  Service/
    SchedulingService.php  # orchestrates engine + repo + optional hooks
  Calendar/
    WorkingCalendar.php    # (future) working-day union for duration mapping
  Contract/
    WorkflowHooksTrait.php # trait: optional hook firing (no FA coupling)
```

## Key design decisions (AD)
- **AD-006-1** Engine is a *pure function* `run(): array` — no I/O, no state.
  Deterministic topological order (stable sort by id) → reproducible ES/EF.
- **AD-006-2** Transport isolation: FA module composes `SchedulingService`
  with a `ScheduleRepositoryInterface` backed by FA `db_*`
  (see ARCH-BR× cycle-guard lives in the service/guard layer, not the engine.
- **AD-006-3** Constraints are transport-agnostic arrays
  `{type:start_no_earlier_than|…, date:YYYY-MM-DD}`; engine maps date→duration
  int via a pure `toDuration()` helper (strtotime date floor to days).
- **AD-006-4** Criticality tolerance default `0.5` (day) — configured,
  injectable; keeps float drift off slack arithmetic.
- **AD-006-5** Cycle detection: Kahn topo sort; if order count < node count,
  remaining nodes + a reconstructed path are returned as `cycle`.

## Conformance
- PHP 7.3 floor; `declare(strict_types=1)`; no `?->`, no typed props, no
  arrow fns, no constructor promotion.
- Composer `"autoload": {"psr-4": {"Ksfraser\CommonPm\": "src/"}}`;
  `"require": {"php": ">=7.3"}`; `"suggest"` for optional FA-hooks bridge.
- Zero FA functions called inside package (rule for standalone packages).
- Unit tests: PHPUnit ^9.6 (or ^10 when PHP allows), `tests/Unit`, stubs
  file `tests/stubs.php` free of FA references.

## Consumed by
<!-- Paths below are written from the FA wrapper's point of view; see
     "Repository note (this copy)" at the top of this file for this repo's
     equivalents. -->
- FA module `ksf_FA_ProjectManagement` (composer require; DI at boot).
- `ksf_FA_ProjectManagement/tests` (pure engine tests, no FA harness).
- Future: CLI batch re-scheduler, other ERPs embedding the same CPM.
