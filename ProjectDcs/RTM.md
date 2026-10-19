# RTM — Requirements Traceability Matrix — ksf_ProjectManagement

| | |
|---|---|
| ID | RTM-PM (module-wide) |
| Generated | from the PHPUnit run of the tree at this commit |
| Suite state | **201 tests, 201 passing, 0 failing** |
| Requirement ids in this matrix | **45** (the module-wide set, not the PM-006 slice) |
| Supersedes | The hand-written `RTM.md` of 2026-05-11, which listed a test-count summary of 65 and a test-case vocabulary (`PM-PROJ-001`, `PM-SVC-TASK-005`, …) that names no test method in the suite. |

## How this file was generated

Reproducible, in this order:

1. `./vendor/bin/phpunit --log-junit /tmp/opencode/junit.xml` — the run every
   **Verified / Partially verified** status below comes from. A row's status is that
   test's actual outcome, never an opinion. If a row's named test is red, the row is
   not *Verified*.
2. A join that walks the JUnit XML, takes each testcase's PASS/FAIL, and joins it to
   the `@BABOK Related:` ids in that test method's own docblock (the nearest
   `/** … */` above the method signature). That join produces the Test column.
3. The same ids are joined to the classes and methods in `src/` that cite them, which
   produces the Implementing column. Line numbers are from the working tree at the
   commit being described, not from the requirement text.
4. `FR-*`/`BR-*` titles are read from this repo's `ProjectDcs/`; `UT-*` titles from the
   bolded case bullets in `ProjectDcs/003-Scheduling/UT-PM-006-cpm-unit.md`.

Status values, applied by this rule:

- **Verified** — at least one test passes, none are red, and code in `src/` claims the id.
- **Partially verified** — implemented but only partly demonstrated: some tests red, or
  no test in the suite cites the id at all.
- **Not implemented** — tests cite the id but nothing in `src/` claims it, or the
  requirement has no executable referent.
- **Blocked** — code exists and the requirement does not, so only the owner can settle
  what "implemented" would even mean. Listed in §Blocked below.

A `UT-*` row has no independent Implementing column: per `ID-SCHEME-DECISION.md` §2 a UT
id is a test id, so its Implementing column is the `FR-*` ids its own tests co-cite.

## Status summary

| Status | Requirement ids |
|---|---|
| **Not implemented** | 4 |
| **Partially verified** | 6 |
| **Verified** | 35 |
| *Blocked* (features, listed in §Blocked) | 6 |
| **Total** | **45** |

## BR-PM-003

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-PM-003 | Link to CRM customer | `DatabaseProjectRepository::findByCustomer` — ksf_ProjectManagement/src/DAO/DatabaseProjectRepository.php:101<br>`Project::setCustomerId` — ksf_ProjectManagement/src/Entity/Project.php:169<br>`ProjectRepositoryInterface::findByCustomer` — ksf_ProjectManagement/src/Repository/ProjectRepositoryInterface.php:47 | `ProjectTest::testSetCustomerIdReturnsSelf` (pass) | **Verified** — 1 passing test. |

## BR-PM-004

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-PM-004 | Assign project manager | `DatabaseProjectRepository::findByManager` — ksf_ProjectManagement/src/DAO/DatabaseProjectRepository.php:112<br>`Project::setProjectManager` — ksf_ProjectManagement/src/Entity/Project.php:186<br>`ProjectRepositoryInterface::findByManager` — ksf_ProjectManagement/src/Repository/ProjectRepositoryInterface.php:52 | `ProjectTest::testSetProjectManagerReturnsSelf` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnMissingManager` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnInvalidManager` (pass) | **Verified** — 3 passing tests. |

## BR-PM-005

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-PM-005 | Status lifecycle | `DatabaseProjectRepository::findByStatus` — ksf_ProjectManagement/src/DAO/DatabaseProjectRepository.php:90<br>`DatabaseProjectRepository::countByStatus` — ksf_ProjectManagement/src/DAO/DatabaseProjectRepository.php:164<br>`Project::setStatus` — ksf_ProjectManagement/src/Entity/Project.php:220<br>`ProjectRepositoryInterface::findByStatus` — ksf_ProjectManagement/src/Repository/ProjectRepositoryInterface.php:42 | `ProjectTest::testSetStatusReturnsSelf` (pass) | **Verified** — 1 passing test. |

## BR-PM-006

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-PM-006 | Standalone CPM/Scheduling Business-Logic Package | `CpmEngine::run` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:98<br>`CpmEngine::extractCyclePath` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:435<br>`CpmEngine` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:70<br>`TaskDependencyGraph` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:36 | *(no testcase in the run cites this id)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |

## BR-TASK-001

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-TASK-001 | Hierarchical tasks | `DatabaseTaskRepository::findByParent` — ksf_ProjectManagement/src/DAO/DatabaseTaskRepository.php:65<br>`TaskDTO::hasSubtasks` — ksf_ProjectManagement/src/DTO/Task/TaskDTO.php:287<br>`Task::setParentTaskId` — ksf_ProjectManagement/src/Entity/Task.php:101<br>`Task::hasSubtasks` — ksf_ProjectManagement/src/Entity/Task.php:310 | `DatabaseTaskRepositoryTest::testFindByParentReturnsChildTasks` (pass)<br>`TaskTest::testSetParentTaskIdReturnsSelf` (pass)<br>`TaskTest::testHasSubtasksReturnsFalse` (pass) | **Verified** — 3 passing tests. |

## BR-TASK-002

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-TASK-002 | Task assignments | `DatabaseTaskRepository::findByAssignee` — ksf_ProjectManagement/src/DAO/DatabaseTaskRepository.php:99<br>`Task::setAssignedTo` — ksf_ProjectManagement/src/Entity/Task.php:152<br>`TaskRepositoryInterface::findByAssignee` — ksf_ProjectManagement/src/Repository/TaskRepositoryInterface.php:47 | `DatabaseTaskRepositoryTest::testFindByAssigneeReturnsTasksForEmployee` (pass)<br>`TaskTest::testSetAssignedToReturnsSelf` (pass)<br>`ProjectServiceTest::testCreateTaskValidationFailsOnInvalidEmployee` (pass) | **Verified** — 3 passing tests. |

## BR-TASK-004

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-TASK-004 | Estimates vs actuals | `TaskDTO::getRemainingHours` — ksf_ProjectManagement/src/DTO/Task/TaskDTO.php:295<br>`Task::setEstimatedHours` — ksf_ProjectManagement/src/Entity/Task.php:203<br>`Task::setActualHours` — ksf_ProjectManagement/src/Entity/Task.php:220 | `TaskTest::testSetEstimatedHoursReturnsSelf` (pass)<br>`TaskTest::testSetActualHoursReturnsSelf` (pass) | **Verified** — 2 passing tests. |

## BR-TASK-005

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-TASK-005 | Dependencies | `Schema::taskDependencies` — ksf_ProjectManagement/src/Dictionary/Schema.php:141 | *(no testcase in the run cites this id)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |

## BR-TASK-006

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| BR-TASK-006 | Priority assignment | `Task::setPriority` — ksf_ProjectManagement/src/Entity/Task.php:254 | `TaskTest::testSetPriorityReturnsSelf` (pass) | **Verified** — 1 passing test. |

## FR-PM-001

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-001 | Project Management | `ProjectServiceInterface::createProject` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:25<br>`ProjectServiceInterface::getProject` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:30<br>`ProjectServiceInterface::updateProject` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:35<br>`ProjectServiceInterface::deleteProject` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:40 | `DatabaseProjectRepositoryTest::testFindReturnsProjectWhenFound` (pass)<br>`DatabaseProjectRepositoryTest::testFindReturnsNullWhenNotFound` (pass)<br>`DatabaseProjectRepositoryTest::testFindAllReturnsArrayOfProjects` (pass)<br>`DatabaseProjectRepositoryTest::testFindByStatusReturnsFilteredProjects` (pass)<br>`DatabaseProjectRepositoryTest::testFindByCustomerReturnsCustomerProjects` (pass)<br>`DatabaseProjectRepositoryTest::testFindByManagerReturnsManagerProjects` (pass)<br>`DatabaseProjectRepositoryTest::testFindActiveReturnsActiveProjects` (pass)<br>`DatabaseProjectRepositoryTest::testCountReturnsTotalCount` (pass)<br>`DatabaseProjectRepositoryTest::testCountByStatusReturnsFilteredCount` (pass)<br>`DatabaseProjectRepositoryTest::testSaveInsertsNewProject` (pass)<br>`DatabaseProjectRepositoryTest::testSaveUpdatesExistingProject` (pass)<br>`DatabaseProjectRepositoryTest::testDeleteExecutesDeleteQuery` (pass)<br>`SchemaTest::testTableNamesAreLogicalAndUnprefixed` (pass)<br>`SchemaTest::testDefinitionForResolvesEveryKnownTable` (pass)<br>`SchemaTest::testCreateSqlsCoversEveryInstalledTable` (pass)<br>`ProjectTest::testConstructorSetsRequiredFields` (pass)<br>`ProjectTest::testConstructorSetsDefaultValues` (pass)<br>`ProjectTest::testSetNameReturnsSelf` (pass)<br>`ProjectTest::testSetDescriptionReturnsSelf` (pass)<br>`ProjectTest::testSetStartDateReturnsSelf` (pass)<br>`ProjectTest::testSetEndDateReturnsSelf` (pass)<br>`ProjectTest::testSetEndDateCanBeNull` (pass)<br>`ProjectTest::testSetBudgetReturnsSelf` (pass)<br>`ProjectTest::testSetCustomerIdReturnsSelf` (pass)<br>`ProjectTest::testSetProjectManagerReturnsSelf` (pass)<br>`ProjectTest::testSetPriorityReturnsSelf` (pass)<br>`ProjectTest::testSetStatusReturnsSelf` (pass)<br>`ProjectTest::testToArrayReturnsAllFields` (pass)<br>`ProjectCreatedEventTest::testGetProject` (pass)<br>`ProjectUpdatedEventTest::testGetProject` (pass)<br>`ProjectUpdatedEventTest::testGetChangedFields` (pass)<br>`ProjectUpdatedEventTest::testGetChangedFieldsDefaultsToEmpty` (pass)<br>`ProjectServiceTest::testCreateProjectSuccess` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnEmptyName` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnMissingStartDate` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnMissingManager` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnInvalidManager` (pass)<br>`ProjectServiceTest::testCreateProjectValidationFailsOnEndDateBeforeStartDate` (pass)<br>`ProjectServiceTest::testGetProjectSuccess` (pass)<br>`ProjectServiceTest::testGetProjectThrowsNotFoundException` (pass)<br>`ProjectServiceTest::testUpdateProjectSuccess` (pass)<br>`ProjectServiceTest::testDeleteProjectSuccess` (pass)<br>*(+12 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 42 passing tests. |

## FR-PM-002

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-002 | Task Management | `ProjectServiceInterface::createTask` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:45<br>`ProjectServiceInterface::getTask` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:50<br>`ProjectServiceInterface::getProjectTasks` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:55<br>`ProjectServiceInterface::updateTask` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:60 | `DatabaseTaskRepositoryTest::testFindReturnsTaskWhenFound` (pass)<br>`DatabaseTaskRepositoryTest::testFindReturnsNullWhenNotFound` (pass)<br>`DatabaseTaskRepositoryTest::testFindByProjectReturnsTasksForProject` (pass)<br>`DatabaseTaskRepositoryTest::testFindByParentReturnsChildTasks` (pass)<br>`DatabaseTaskRepositoryTest::testFindByAssigneeReturnsTasksForEmployee` (pass)<br>`DatabaseTaskRepositoryTest::testFindByStatusReturnsFilteredTasks` (pass)<br>`DatabaseTaskRepositoryTest::testSaveInsertsNewTask` (pass)<br>`DatabaseTaskRepositoryTest::testSaveUpdatesExistingTask` (pass)<br>`DatabaseTaskRepositoryTest::testDeleteExecutesDeleteQuery` (pass)<br>`SchemaTest::testTableNamesAreLogicalAndUnprefixed` (pass)<br>`SchemaTest::testDefinitionForResolvesEveryKnownTable` (pass)<br>`SchemaTest::testCreateSqlsCoversEveryInstalledTable` (pass)<br>`TaskTest::testConstructorSetsRequiredFields` (pass)<br>`TaskTest::testConstructorSetsDefaultValues` (pass)<br>`TaskTest::testSetParentTaskIdReturnsSelf` (pass)<br>`TaskTest::testSetNameReturnsSelf` (pass)<br>`TaskTest::testSetDescriptionReturnsSelf` (pass)<br>`TaskTest::testSetAssignedToReturnsSelf` (pass)<br>`TaskTest::testSetStartDateReturnsSelf` (pass)<br>`TaskTest::testSetStartDateCanBeNull` (pass)<br>`TaskTest::testSetEndDateReturnsSelf` (pass)<br>`TaskTest::testSetEndDateCanBeNull` (pass)<br>`TaskTest::testSetEstimatedHoursReturnsSelf` (pass)<br>`TaskTest::testSetActualHoursReturnsSelf` (pass)<br>`TaskTest::testSetPriorityReturnsSelf` (pass)<br>`TaskTest::testSetStatusReturnsSelf` (pass)<br>`TaskTest::testHasSubtasksReturnsFalse` (pass)<br>`TaskTest::testToArrayReturnsAllFields` (pass)<br>`TaskCreatedEventTest::testGetTask` (pass)<br>`ProjectServiceTest::testCreateTaskSuccess` (pass)<br>`ProjectServiceTest::testCreateTaskValidationFailsOnMissingProjectId` (pass)<br>`ProjectServiceTest::testCreateTaskValidationFailsOnMissingName` (pass)<br>`ProjectServiceTest::testCreateTaskValidationFailsOnInvalidEmployee` (pass)<br>`ProjectServiceTest::testGetTaskSuccess` (pass)<br>`ProjectServiceTest::testGetTaskThrowsNotFoundException` (pass)<br>`ProjectServiceTest::testGetProjectTasksReturnsArray` (pass)<br>*(+9 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 36 passing tests. |

## FR-PM-003

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-003 | Resource Allocation | `EmployeeServiceInterface::getEmployee` — ksf_ProjectManagement/src/Contract/EmployeeServiceInterface.php:20<br>`EmployeeServiceInterface::employeeExists` — ksf_ProjectManagement/src/Contract/EmployeeServiceInterface.php:25<br>`EmployeeServiceInterface::getEmployeesByDepartment` — ksf_ProjectManagement/src/Contract/EmployeeServiceInterface.php:30<br>`EmployeeServiceInterface` — ksf_ProjectManagement/src/Contract/EmployeeServiceInterface.php:15 | `DatabaseAssignmentRepositoryTest::testFindByProjectAndEmployeeReturnsAssignmentWhenFound` (pass)<br>`DatabaseAssignmentRepositoryTest::testFindByProjectAndEmployeeReturnsNullWhenNotFound` (pass)<br>`DatabaseAssignmentRepositoryTest::testFindByProjectReturnsAllAssignments` (pass)<br>`DatabaseAssignmentRepositoryTest::testFindByEmployeeReturnsEmployeeAssignments` (pass)<br>`DatabaseAssignmentRepositoryTest::testFindActiveByProjectReturnsActiveAssignments` (pass)<br>`DatabaseAssignmentRepositoryTest::testFindActiveByEmployeeReturnsActiveAssignments` (pass)<br>`DatabaseAssignmentRepositoryTest::testSaveInsertsNewAssignment` (pass)<br>`DatabaseAssignmentRepositoryTest::testSaveUpdatesExistingAssignment` (pass)<br>`DatabaseAssignmentRepositoryTest::testDeleteExecutesDeleteQuery` (pass)<br>`SchemaTest::testTableNamesAreLogicalAndUnprefixed` (pass)<br>`SchemaTest::testDefinitionForResolvesEveryKnownTable` (pass)<br>`SchemaTest::testCreateSqlsCoversEveryInstalledTable` (pass)<br>`ProjectAssignmentTest::testConstructorSetsRequiredFields` (pass)<br>`ProjectAssignmentTest::testConstructorSetsEndDateToNull` (pass)<br>`ProjectAssignmentTest::testSetRoleReturnsSelf` (pass)<br>`ProjectAssignmentTest::testSetStartDateReturnsSelf` (pass)<br>`ProjectAssignmentTest::testSetEndDateReturnsSelf` (pass)<br>`ProjectAssignmentTest::testSetEndDateCanBeNull` (pass)<br>`ProjectAssignmentTest::testSetAllocationPercentageClampsToValidRange` (pass)<br>`ProjectAssignmentTest::testSetAllocationPercentageReturnsSelf` (pass)<br>`ProjectAssignmentTest::testIsEndedReturnsFalseWhenNoEndDate` (pass)<br>`ProjectAssignmentTest::testIsEndedReturnsFalseWhenEndDateInFuture` (pass)<br>`ProjectAssignmentTest::testIsEndedReturnsTrueWhenEndDateInPast` (pass)<br>`ProjectAssignmentTest::testToArrayReturnsAllFields` (pass)<br>`EmployeeAssignedToProjectEventTest::testGetAssignment` (pass)<br>`EmployeeAssignedToProjectEventTest::testGetProjectId` (pass)<br>`EmployeeAssignedToProjectEventTest::testGetEmployeeId` (pass)<br>`EmployeeAssignedToProjectEventTest::testGetRole` (pass)<br>`ProjectServiceTest::testAssignEmployeeToProjectSuccess` (pass)<br>`ProjectServiceTest::testAssignEmployeeToProjectFailsIfAlreadyAssigned` (pass)<br>`ProjectServiceTest::testRemoveEmployeeFromProjectSuccess` (pass)<br>`ProjectServiceTest::testGetProjectTeamReturnsArray` (pass)<br>*(+9 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 32 passing tests. |

## FR-PM-004

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-004 | Progress Calculation | `ProjectServiceInterface::updateTaskProgress` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:70<br>`ProjectServiceInterface` — ksf_ProjectManagement/src/Contract/ProjectServiceInterface.php:20<br>`DatabaseTaskRepository::countByProject` — ksf_ProjectManagement/src/DAO/DatabaseTaskRepository.php:136<br>`DatabaseTaskRepository::countCompletedByProject` — ksf_ProjectManagement/src/DAO/DatabaseTaskRepository.php:147 | `DatabaseTaskRepositoryTest::testCountByProjectReturnsCount` (pass)<br>`DatabaseTaskRepositoryTest::testCountCompletedByProjectReturnsCount` (pass)<br>`ProjectAssignmentTest::testIsActiveReturnsTrueWhenWithinDateRange` (pass)<br>`ProjectAssignmentTest::testIsActiveReturnsTrueWhenNoEndDate` (pass)<br>`ProjectAssignmentTest::testIsActiveReturnsFalseWhenStartDateInFuture` (pass)<br>`ProjectAssignmentTest::testIsActiveReturnsFalseWhenEndDateInPast` (pass)<br>`ProjectTest::testGetDurationReturnsNullWhenNoEndDate` (pass)<br>`ProjectTest::testGetDurationReturnsDaysDifference` (pass)<br>`ProjectTest::testIsActiveReturnsTrueForActiveProject` (pass)<br>`ProjectTest::testIsActiveReturnsFalseForCompletedProject` (pass)<br>`ProjectTest::testIsActiveReturnsFalseForCancelledProject` (pass)<br>`ProjectTest::testIsActiveReturnsFalseForFutureProject` (pass)<br>`ProjectTest::testIsActiveReturnsFalseForPastProject` (pass)<br>`ProjectTest::testIsActiveReturnsTrueForProjectWithNoEndDate` (pass)<br>`TaskTest::testSetProgressClampsToValidRange` (pass)<br>`TaskTest::testSetProgressReturnsSelf` (pass)<br>`TaskTest::testIsCompletedReturnsTrueWhenStatusCompleted` (pass)<br>`TaskTest::testIsCompletedReturnsTrueWhenProgress100` (pass)<br>`TaskTest::testIsCompletedReturnsFalseOtherwise` (pass)<br>`TaskTest::testGetDurationReturnsNullWhenNoStartDate` (pass)<br>`TaskTest::testGetDurationReturnsNullWhenNoEndDate` (pass)<br>`TaskTest::testGetDurationReturnsDaysDifference` (pass)<br>`TaskProgressUpdatedEventTest::testGetTask` (pass)<br>`TaskProgressUpdatedEventTest::testGetPreviousProgress` (pass)<br>`TaskProgressUpdatedEventTest::testGetNewProgress` (pass)<br>`TaskProgressUpdatedEventTest::testGetProgressDelta` (pass)<br>`TaskProgressUpdatedEventTest::testGetProgressDeltaNegative` (pass)<br>`ProjectServiceTest::testUpdateTaskProgressSuccess` (pass)<br>*(+6 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 28 passing tests. |

## FR-PM-005

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-005 | Overdue Detection | `DatabaseProjectRepository::findOverdue` — ksf_ProjectManagement/src/DAO/DatabaseProjectRepository.php:123<br>`DatabaseTaskRepository::findOverdue` — ksf_ProjectManagement/src/DAO/DatabaseTaskRepository.php:110<br>`ProjectDTO::isOverdue` — ksf_ProjectManagement/src/DTO/Project/ProjectDTO.php:265<br>`ProjectDTO` — ksf_ProjectManagement/src/DTO/Project/ProjectDTO.php:21 | `DatabaseProjectRepositoryTest::testFindOverdueReturnsOverdueProjects` (pass)<br>`DatabaseProjectRepositoryTest::testHydrationWithNullEndDate` (pass)<br>`DatabaseTaskRepositoryTest::testFindOverdueReturnsOverdueTasks` (pass)<br>`DatabaseTaskRepositoryTest::testHydrationWithNullDates` (pass)<br>`ProjectTest::testIsOverdueReturnsFalseWhenNoEndDate` (pass)<br>`ProjectTest::testIsOverdueReturnsFalseWhenCompleted` (pass)<br>`ProjectTest::testIsOverdueReturnsTrueWhenPastEndDateAndNotCompleted` (pass)<br>`ProjectTest::testIsOverdueReturnsFalseWhenFutureEndDate` (pass)<br>`TaskTest::testIsOverdueReturnsFalseWhenNoEndDate` (pass)<br>`TaskTest::testIsOverdueReturnsFalseWhenCompleted` (pass)<br>`TaskTest::testIsOverdueReturnsTrueWhenPastEndDateAndNotCompleted` (pass)<br>`TaskTest::testIsOverdueReturnsFalseWhenFutureEndDate` (pass)<br>*(+4 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 12 passing tests. |

## FR-PM-006

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| FR-PM-006 | Time Integration | `TaskDTO::getTimeEntries` — ksf_ProjectManagement/src/DTO/Task/TaskDTO.php:236 | *(no testcase in the run cites this id)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |
| FR-PM-006-001 | CpmEngine: precedence-graph schedule computation | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `SchemaTest::testTasksDictionaryCarriesTheSchedulingColumns` (pass)<br>`GanttTimelineBuilderTest::testBuildFromEngineResult` (pass)<br>`CpmEngineTest::testCycleReportExcludesAcyclicTasks` (pass)<br>`CpmEngineTest::testCalendarDateAnchorDoesNotExplodeIntoDaysSinceEpoch` (pass)<br>*(+3 non-testcase members cite it: setUp/fixture/helpers)* | **Verified** — 4 passing tests. |
| FR-PM-006-003 | Dependency guard: cycle + self-ref rejection | `CpmEngine::detectCycle` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:377<br>`TaskDependencyGraph::addEdge` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:64<br>`TaskDependencyGraph::addNode` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:86<br>`TaskDependencyGraph::nodes` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:100 | `SchemaTest::testTaskDependenciesDictionaryShape` (pass)<br>`CpmEngineTest::testDetectCycleReturnsAClosedPathOnTheRealCycleOnly` (pass)<br>`CpmEngineTest::testDetectCycleAcceptsAnAcyclicDiamond` (pass) | **Verified** — 3 passing tests. |

## UC-PM-005

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| UC-PM-005 | Project Budget Tracking | `Project::setBudget` — ksf_ProjectManagement/src/Entity/Project.php:152 | `ProjectTest::testSetBudgetReturnsSelf` (pass) | **Verified** — 1 passing test. |

## UT-PM-006

| Requirement | Title | Implementing | Test | Status |
|---|---|---|---|---|
| UT-PM-006 | Unit Test Specification standalone CPM package | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | *(no testcase in the run cites this id)*<br>*(+1 non-testcase member cite it: setUp/fixture/helpers)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |
| UT-PM-006-001-001 | forward pass ES/EF for FS(+lag) chain. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001001FsChainForwardPass` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-002 | FS edge honours lag 3 `ES = EF_pred + 3`. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001002FsWithLagThree` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-003 | SS edge via predecessor ES+lag. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001003StartToStartEdgeHonoursPredecessorEs` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-004 | FF edge gates the successor's finish: `ES_succ = EF_pred + lag dur_succ`. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001004FinishToFinishEdgeGatesTheSuccessorFinish` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-005 | SF edge via predecessor EF+lag duration. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001005StartToFinishEdgeSubtractsSuccessorDuration` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-006 | milestone EF=ES (zero duration). | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001006MilestoneHasZeroDurationAndEfEqualsEs` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-007 | `start_no_earlier_than` anchor raises ES. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001007StartNoEarlierThanAnchorRaisesEs` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-008 | `finish_no_earlier_than` anchor raises EF. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001008FinishNoEarlierThanAnchorRaisesEf` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-001-009 | multiple-input merge takes the max of incoming candidates. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006001009MergeTaskEsIsTheMaxIncomingCandidate` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-002-001 | backward pass LF/LS; critical chain has zero total float. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006002001CriticalChainHasZeroSlackOnBothTasks` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-002-002 | non-critical merge task has float > 0. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006002002NonCriticalMergeTaskHasPositiveSlack` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-002-003 | total float and criticality tolerance. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006002003SlackIsSignedTotalFloatAndCriticalityIsAOneSidedBand` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-002-004 | milestone critical iff slack <= 0.5. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006002004MilestoneCriticalityFollowsSlack` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-002-005 | `critical` array task ids + project_duration correct. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006002005CriticalIdSetAndProjectDuration` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-003-001 | self-dependency rejected, and it is NOT a cycle. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006003001SelfDependencyIsRejectedAndIsNotACycle` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-003-002 | cycle `ok=false` + cycle path (closed traversal), no throw. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testUt006003002CycleIsReportedAsAClosedPath` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-003-003 | negative lag used raw. | *(nothing in `src/` cites this id)* | `CpmEngineTest::testUt006003003NegativeLagIsUsedRaw` (pass) | **Not implemented** — 1 test cites it but no class or method in src/ claims it. |
| UT-PM-006-003-004 | empty task set `ok=true`, duration 0, empty critical. | *(nothing in `src/` cites this id)* | `CpmEngineTest::testUt006003004EmptyTaskSetIsValidAndZeroLength` (pass) | **Not implemented** — 1 test cites it but no class or method in src/ claims it. |
| UT-PM-006-003-005 | constraint key variants; `type` defaults to `fs`. | *(nothing in `src/` cites this id)* | `CpmEngineTest::testUt006003005AcceptsAllKeyVariantsAndDefaultsTypeToFs` (pass) | **Not implemented** — 1 test cites it but no class or method in src/ claims it. |
| UT-PM-006-003-006 | missing predecessor/successor references skipped, not an error. | *(nothing in `src/` cites this id)* | `CpmEngineTest::testUt006003006DanglingDependencyReferencesAreSkipped` (pass) | **Not implemented** — 1 test cites it but no class or method in src/ claims it. |
| UT-PM-006-005-002 | back-edge closing a cycle rejected (no write). | `TaskDependencyGraph::addEdge` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:64<br>`CpmEngine::detectCycle` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:377<br>`TaskDependencyGraph::addNode` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:86<br>`TaskDependencyGraph::nodes` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:100 | *(no testcase in the run cites this id)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |
| UT-PM-006-005-005 | `CpmEngine::detectCycle()` + `Engine\TaskDependencyGraph` exist and are reachable. | `CpmEngine::detectCycle` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:377<br>`TaskDependencyGraph::addEdge` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:64<br>`TaskDependencyGraph::addNode` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:86<br>`TaskDependencyGraph::nodes` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:100 | *(no testcase in the run cites this id)* | **Partially verified** — implemented, but no test in the suite cites this id — nothing verifies it |
| UT-PM-006-006-001 | the cycle report names only the nodes actually on a loop. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testCycleReportExcludesAcyclicTasks` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-006-002 | a calendar-date anchor is a schedule offset, not days-since-epoch. | `DependencyDto::__construct` — ksf_ProjectManagement/src/Dto/DependencyDto.php:29<br>`DependencyDto` — ksf_ProjectManagement/src/Dto/DependencyDto.php:15<br>`GanttRowDto::__construct` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:33<br>`GanttRowDto` — ksf_ProjectManagement/src/Dto/GanttRowDto.php:15 | `CpmEngineTest::testCalendarDateAnchorDoesNotExplodeIntoDaysSinceEpoch` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-006-003 | `detectCycle()` returns a genuine closed cycle path naming only the loop's own nodes. | `TaskDependencyGraph::nodes` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:100<br>`TaskDependencyGraph::successorsOf` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:114<br>`CpmEngine::detectCycle` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:377<br>`TaskDependencyGraph::addEdge` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:64 | `CpmEngineTest::testDetectCycleReturnsAClosedPathOnTheRealCycleOnly` (pass) | **Verified** — 1 passing test. |
| UT-PM-006-006-004 | `detectCycle()` accepts an acyclic graph. | `CpmEngine::detectCycle` — ksf_ProjectManagement/src/Scheduling/CpmEngine.php:377<br>`TaskDependencyGraph::addEdge` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:64<br>`TaskDependencyGraph::addNode` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:86<br>`TaskDependencyGraph::nodes` — ksf_ProjectManagement/src/Scheduling/TaskDependencyGraph.php:100 | `CpmEngineTest::testDetectCycleAcceptsAnAcyclicDiamond` (pass) | **Verified** — 1 passing test. |

## Blocked

Code exists for these, but no requirement document in either repo defines
them, so there is nothing to verify against. Not fixed here: minting an id for them
would be inventing a requirement, and that is an owner decision.

| Feature | Why blocked | Code |
|---|---|---|
| **N/A no requirement defined in this repo** | no requirement document exists in either repo | 26 class/method across `EventCloseTaskService`, `EventCloseTaskServiceTest`, `ProjectEntityTest` |
| **N/A no requirement defined in this repo for project-type reference data** | no requirement document exists in either repo | 9 class/method across `ProjectType` |
| **N/A no requirement defined in this repo for revenue** | no requirement document exists in either repo | 13 class/method across `ProjectRevenue` |
| **N/A no requirement defined in this repo for sales-order-to-** | no requirement document exists in either repo | 10 class/method across `ProjectSalesOrder` |
| **N/A no requirement defined in this repo for the attachment/storage feature; FR-PROJECT-004 lives in ksf_FA_ProjectManagement/ProjectDcs and this package only carries the transport-agnostic contract for it** | no requirement document exists in either repo | 44 class/method across `FileDTO`, `FileServiceInterface`, `ProjectFileService`, `ProjectFileUploadedEvent` |
| **N/A test helper/assertion plumbing; carries no requirement of its own** | no requirement document exists in either repo | 4 class/method across `CpmEngineTest` |

## N/A-marked members serving no requirement

## Inherited requirement definitions

Carried forward verbatim from the 2026-05-11 `RTM.md`, which was the only place these
ids were given a title. They are retained unchanged so the ids above remain *defined*
and this file is self-contained. Note the test-case vocabulary in them
(`PM-PROJ-001`, `PM-SVC-TASK-005`, …) names no method in the current suite and is
therefore NOT used as a join key — only the `@BABOK Related:` ids are.

### 2. Requirement Mapping

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

### 3. Functional Requirements Detail

| FR ID | Requirement | Priority | Status | Test Coverage |
|-------|-------------|----------|--------|---------------|
| FR-PM-001 | Project management (CRUD) | High | ✓ | PM-PROJ-001, PM-PROJ-002, PM-SVC-PROJ-001, PM-SVC-PROJ-004 |
| FR-PM-002 | Task management (hierarchical) | High | ✓ | PM-TASK-001-012, PM-SVC-TASK-001-008 |
| FR-PM-003 | Resource allocation | High | ✓ | PM-ASSIGN-001-005, PM-SVC-RES-001-006 |
| FR-PM-004 | Progress calculation | High | ✓ | PM-PROJ-011, PM-PROJ-012, PM-TASK-010 |
| FR-PM-005 | Overdue detection | Medium | ✓ | PM-PROJ-006-008, PM-TASK-008 |
| FR-PM-006 | Time integration | Medium | ✓ | PM-INT-TIME-001-003 |

---

155 class/method docblocks carry an explicit `@BABOK Related: N/A — …` marker:
PSR-14 plumbing, DI/composition-root plumbing, test doubles and fixtures, private
helpers whose requirement is carried by the public method above them, and the
Blocked features above. They are enumerated in the source, not repeated here.
