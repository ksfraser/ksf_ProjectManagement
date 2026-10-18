# Consolidation — ksf_ProjectManagement ⇄ ksf_FA_ProjectManagement

**Date:** 2026-10-17
**Scope:** class-by-class reconciliation of the business-logic package
(`~/Documents/ksf_ProjectManagement`, Packagist `ksfraser/ksf-project-management`)
against the FrontAccounting wrapper (`~/Documents/ksf_FA_ProjectManagement`,
`ksfraser/ksf-fa-projectmanagement`).

> Do not repeat this analysis from scratch. If you are here to change PM code,
> read §1 (the architecture rule) and §2 (the classification table) first, then
> the merge decision that touches your class.

---

## 1. The rule this consolidation had to honour

| Repo | Role | Constraint |
|---|---|---|
| `ksf_ProjectManagement` | business logic; becomes a Packagist library | **must not** ship `hooks.php`, must not call native `db_*`, must not hard-depend on FA. DAO layer codes against `ksfraser\CommonDb\Contract\DbConnectionInterface` and the shared `QueryBuilder`/`TableDefinition`. |
| `ksf_FA_ProjectManagement` | FA wrapper; supplies hooks + the DB transport | runtime deployable code goes through `FaDbAdapter`, which calls native `db_*`. `PdoDbAdapter` is for unit tests only. |

`ksf_FA_ProjectManagement` is the **only consumer** of the logic package and is
authoritative for FA runtime. The logic package is the canonical home for
anything transport-agnostic. Where a class is in both trees, the wrapper still
holds its own copy until it `composer require`s the package — **that rewire is
the outstanding next step and was deliberately not done here** (it changes what
loads during FA bootstrap).

---

## 2. Classification table

| Bucket | Count | Classes |
|---|---|---|
| **IDENTICAL** | 0 | — |
| **SUPERSEDED** (logic side older; FA side wins) | 1 | `Scheduling\CpmEngine` |
| **UNIQUE-TO-LOGIC** (preserved untouched) | 27 | see §2.1 |
| **OBSOLETE** | 1 | `Scheduling\CpmEngine` (old revision, replaced in place) |
| **MERGE-CANDIDATE** (not yet merged — owner decision) | 12 | see §2.2 |

### 2.1 UNIQUE-TO-LOGIC — preserved, and what a blind overwrite would have destroyed

Nothing in this list exists anywhere in the FA tree. Every one of these was
**left byte-for-byte untouched** apart from the PSR-4 directory move in §4.

| Class | Namespace |
|---|---|
| `EmployeeServiceInterface` | `…\Contract` |
| `FileServiceInterface` | `…\Contract` |
| `ProjectServiceInterface` | `…\Contract` |
| `DatabaseAssignmentRepository` | `…\DAO` |
| `DatabaseProjectRepository` | `…\DAO` |
| `DatabaseTaskRepository` | `…\DAO` |
| `AssignmentRepositoryInterface` | `…\Repository` |
| `ProjectRepositoryInterface` | `…\Repository` |
| `TaskRepositoryInterface` | `…\Repository` |
| `AssignmentDTO`, `FileDTO`, `ProjectDTO`, `TaskDTO` | `…\DTO\{Assignment,File,Project,Task}` |
| `Project`, `Task`, `ProjectAssignment` | `…\Entity` |
| `ProjectEvent`, `ProjectCreatedEvent`, `ProjectUpdatedEvent`, `ProjectFileUploadedEvent` | `…\Event` |
| `TaskEvent`, `TaskCreatedEvent`, `TaskProgressUpdatedEvent`, `EmployeeAssignedToProjectEvent` | `…\Event` |
| `ProjectException`, `ProjectNotFoundException`, `TaskNotFoundException`, `ValidationException` | `…\Exception` |
| `ProjectService`, `ProjectFileService` | `Ksfraser\ProjectManagement` |
| `DependencyDto`, `GanttRowDto`, `ScheduleDto`, `TaskDto` | `…\Dto` |
| `GanttTimelineBuilder` | `…\Gantt` |

**Genuinely valuable, no FA counterpart, must not be lost:**

- **`GanttTimelineBuilder` + the four `Dto` classes.** Presentation-agnostic
  Gantt rows (day-offset `startDay`/`endDay`, grouping, critical flag). This is
  the *only* Gantt implementation in either tree; the FA module renders its
  chart from `TaskRepository` rows directly. It is also the intended consumer of
  `CpmEngine::run()` output and was the reason `run()`'s return shape matters.
- **The whole PSR-14 `Event` hierarchy.** 8 classes implementing
  `Psr\EventDispatcher\StoppableEventInterface` — a real, dispatched event
  model. The FA tree's inter-module communication is `hook_invoke_all`, which is
  a different mechanism with no overlap. Neither replaces the other.
- **`Contract\{EmployeeServiceInterface,FileServiceInterface}`.** The seams that
  keep `ProjectService`/`ProjectFileService` free of any HR or storage
  dependency. No FA counterpart.
- **`Exception\*` (4).** Extend `ksfraser\exceptions`'s
  `ProjectManagement\*` hierarchy. The FA tree throws bare
  `\InvalidArgumentException` instead — this package's typed-exception contract
  is a real behavioural difference, not a duplicate.
- **`Entity\{Project,Task,ProjectAssignment}` + the `DTO\*` set.** Rich,
  mutable, domain-entity style with validation. The FA `Entity\*` are dumb
  array-row value objects for three *different* tables (project types, sales
  orders, revenue). No overlap.
- **`DAO\Database*Repository` + `Repository\*Interface`.** The entity-hydrating
  DAO pattern. See the merge candidate note in §2.2.

### 2.2 MERGE-CANDIDATE — deliberately NOT actioned, needs an owner decision

These are in the FA tree, are portable in substance, but were **not** copied
because each pulls in a design decision that is not this task's to make.

| Class (FA tree) | Why not moved |
|---|---|
| `Repository\{ProjectRepository,TaskRepository,AssignmentRepository,ProjectTypeRepository,TaskDependencyRepository,ProjectOrderRepository,ActivityLogRepository,ReferenceRepository}` (8) | **Two incompatible DAO styles are now live in the package.** `…\DAO\Database*Repository` hydrate rich Entities and satisfy `…\Repository\*Interface`; the FA repositories return plain row arrays and use no interface. Merging means choosing one style, or writing adapters. Also: `ReferenceRepository` reads FA's own `debtors_master`/`users` tables, so it is FA-coupled by table, not by transport. |
| `Service\ProjectService`, `Service\TaskService`, `Service\AssignmentService` | `use ksfraser\FrontAccounting\Common\Traits\WorkflowHooksTrait` — a hard import of the FA package, which fires `hook_invoke_all`. Hook dispatch is the wrapper's job. |
| `Service\SchedulingService`, `Service\TaskDependencyService` | Same `WorkflowHooksTrait` coupling. Note the wrapper's `TaskDependencyService::add()` (renamed from `createDependency`, record-type key `task_dependency`) and its `pm_task_dependency_before_save` hooks are the live contract; a copy here would immediately drift. |
| `Service\ProjectTypeService` | Imports `Ksfraser\HTML\Elements\HtmlSelect` and `Ksfraser\HTML\Traits\DdlCacheTrait` — HTML rendering, not business logic. |
| `Service\ReportService` | Depends on four repositories (see above) and builds its own `QueryBuilder` aggregates; blocked on the DAO decision. |
| `Service\ProjectOrderService` | Depends on `ProjectOrderRepository`; blocked on the DAO decision. |
| `Service\EventMembershipResponder` | Reads FA's `users` table to bridge attendee email → `user_id`. FA-specific by table. Stays in the wrapper. |

---

## 3. Merge decisions, one line each

1. **`Scheduling\CpmEngine` — FA version wins, merged in place.** Same namespace, so Packagist consumers and `GanttTimelineBuilder` need no change; the FA side keeps `ksfraser\FrontAccounting\ProjectManagement\Engine\CpmEngine`.
2. **`Engine\TaskDependencyGraph` → `Scheduling\TaskDependencyGraph`.** Lives beside the engine that consumes it, for the same BC reason.
3. **`Dictionary\Schema` copied WITHOUT `Schema::adapter()`.** That method builds an `FaDbAdapter` from `TB_PREF`; the wrapper keeps its own. Everything else verbatim.
4. **`Service\EventCloseTaskService` copied with a MANDATORY injected connection** (`__construct(DbConnectionInterface $db)`) instead of `?… = null` defaulting to `Schema::adapter()`. Same reason.
5. **`Entity\{ProjectType,ProjectRevenue,ProjectSalesOrder}` copied verbatim** (namespaces only). No name clash with the existing `Entity\{Project,Task,ProjectAssignment}`.
6. **`tests/Unit/Scheduling/CpmEngineTest` replaced** — the FA module's 24-case UT-PM-006 suite supersedes this package's 10-case suite, which is a strict subset. `TaskDependencyGraph` tests came with it (`detectCycle`).
7. **`tests/Unit/Service/EventCloseTaskServiceTest` ported** to an injected `FakeDbConnection` instead of the FA module's stubbed `db_*` globals; assertions tightened from interpolated literals to bound parameters.
8. **`Schema::createSqls()` / `definitionFor()` gained `T_TASK_DEPENDENCIES`** in **both** trees — the CPM dependency table was in neither list, so a standalone consumer building its schema from the dictionary would have got no dependency table.
9. **NOT copied: any `use` of a `ksfraser\FrontAccounting\*` namespace, `db_*`, `TB_PREF`, `hook_invoke_all`, `FaDbAdapter`, or a `hooks.php`.** Enforced going forward by `tests/Unit/Architecture/NoFaCouplingTest.php`.

---

## 4. Layout defect found and fixed (this is why the suite was red)

`composer.json` maps `Ksfraser\ProjectManagement\ => src/`, but **27 of the
package's 33 `src/` classes lived at `src/Ksfraser/ProjectManagement/<Sub>/`**.
The autoloader therefore looked for `src/Ksfraser/ProjectManagement/<Sub>/…`
*twice* and never found them. The classes were unreachable and 152 of 162 tests
errored with "class does not exist".

Fix: those directories were moved up one level (`src/Ksfraser/ProjectManagement/Contract`
→ `src/Contract`, etc.). **No namespace changed**, so no `use` statement
anywhere needed editing — this is the fix that removes the `Ksfraser` vs
`ksfraser` casing trap entirely, since there is now exactly one spelling of the
prefix and one directory shape. `NoFaCouplingTest::testEverySourceClassIsPsr4Addressable`
asserts the invariant so it cannot regress.

The package now has a single flat convention:

```
src/Contract/  src/DAO/  src/DTO/  src/Dictionary/  src/Dto/
src/Entity/    src/Event/ src/Exception/  src/Gantt/  src/Scheduling/
src/Service/   src/ProjectService.php  src/ProjectFileService.php
```

Note `DTO/` and `Dto/` are two *distinct* namespaces (`…\DTO\File\FileDTO` and
`…\Dto\TaskDto`), pre-existing and both intentional. They are separate
generations of the same idea (rich DTO vs scheduling input DTO) and were left
distinct deliberately; merging them is an owner decision.

---

## 5. Other defects fixed in passing

| Where | Defect |
|---|---|
| `src/Gantt/GanttTimelineBuilder.php:54` | `in_array($id, $critical, truehedge)` — `truehedge` is an undefined constant. Every `GanttTimelineBuilder::build()` call that reached that line was a fatal `Error`. Now `true`. |
| `composer.json` | `require` was missing `ksfraser/exceptions`, `psr/event-dispatcher` and `psr/log`, all of which `src/` imports. Declared. |
| `composer.json` / lock | The lock predated the `ksfraser/ksf-common-db` requirement, so the package's own DB contract was never installed. Reinstalled; PHPUnit is now the `^9.6` the 7.3 platform pin requires (it was a stale 10.5 in `vendor/`). |

---

## 6. Open items for the owner (deliberately not actioned)

1. **PHP floor vs typed properties.** `composer.json` declares `"php": ">=7.3"`
   and `config.platform.php = 7.3.33`, but 19 existing files (117 typed
   properties) and the three entities copied in here use **typed properties,
   which are PHP 7.4**. This package cannot run on 7.3 as configured. Either the
   pin moves to 7.4.33 (matching the only consumer, `ksf_FA_ProjectManagement`,
   and the FA container) or the typed properties go. **Not changed here** —
   `AGENTS_ARCH.md` records 7.3 as the cross-module floor, so this is an
   architecture decision, not a refactor.
2. **The DAO merge** (§2.2) — two DAO styles in one package.
3. **The wrapper rewire** — `ksf_FA_ProjectManagement` still holds its own copy
   of the engine, dictionary, entities and services. It should eventually
   `composer require ksfraser/ksf-project-management` and delete them. Not done
   here because it changes what loads during FA bootstrap.
4. **`DTO/` vs `Dto/`** — two namespaces, one concept.