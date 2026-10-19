<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Support;

use ksfraser\CommonDb\Contract\DbConnectionInterface;

/**
 * Recording in-memory stand-in for DbConnectionInterface.
 *
 * The FA module's equivalent fake is built on stubbed `db_*` globals, which
 * this package cannot have. This one records the SQL text and the bound
 * parameters separately, so a test can assert on either without a database —
 * which is also why it exercises the named-placeholder contract the way
 * PdoDbAdapter does at runtime.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
  *
 * @BABOK Related: N/A — test double for DbConnectionInterface (unit tests only; PdoDbAdapter is never used at FA runtime)
 */
final class FakeDbConnection implements DbConnectionInterface
{
    /** @var array<int,array<string,mixed>> */
    private $selectQueue = [];

    /** @var array<int,array{sql:string,params:array}> */
    private $writes = [];

    /** @var array<int,array{sql:string,params:array}> */
    private $reads = [];

    /** @var array<int,array{sql:string,params:array}> Chronological, reads and writes interleaved. */
    private $log = [];

    /** @var int */
    private $nextId = 1;

    /**
     * Queue the row-sets successive fetchAssoc()/fetchAll() calls consume.
     *
     * @param array<int,array<int,array<string,mixed>>> $queue
     * @return void
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    public function queueSelects(array $queue): void
    {
        $this->selectQueue = $queue;
    }

    /**
     * @return array<int,array{sql:string,params:array}>
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    public function writes(): array
    {
        return $this->writes;
    }

    /**
     * @return array<int,array{sql:string,params:array}>
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    public function reads(): array
    {
        return $this->reads;
    }

    /**
     * The SQL text of the most recent read or write.
     *
     * @return string
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    public function lastSql(): string
    {
        $last = end($this->log);

        return $last === false ? '' : $last['sql'];
    }

    /**
     * The bound parameters of the most recent read or write.
     *
     * @return array<string,mixed>
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    public function lastParams(): array
    {
        $last = end($this->log);

        return $last === false ? [] : $last['params'];
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function fetchAssoc(string $sql, array $params = []): ?array
    {
        $rows = $this->nextRows();
        $this->reads[] = ['sql' => $sql, 'params' => $params];
        $this->log[]   = ['sql' => $sql, 'params' => $params];

        return $rows === [] ? null : $rows[0];
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $rows = $this->nextRows();
        $this->reads[] = ['sql' => $sql, 'params' => $params];
        $this->log[]   = ['sql' => $sql, 'params' => $params];

        return $rows;
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function fetchScalar(string $sql, array $params = [], $default = null)
    {
        $rows = $this->nextRows();
        $this->reads[] = ['sql' => $sql, 'params' => $params];
        $this->log[]   = ['sql' => $sql, 'params' => $params];
        if ($rows === [] || $rows[0] === []) {
            return $default;
        }
        $first = $rows[0];

        return reset($first);
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function executeUpdate(string $sql, array $params = []): int
    {
        $this->writes[] = ['sql' => $sql, 'params' => $params];
        $this->log[]    = ['sql' => $sql, 'params' => $params];

        return 1;
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function lastInsertId(): int
    {
        return $this->nextId++;
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function quote($value): string
    {
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function beginTransaction(): void
    {
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function commit(): void
    {
    }

    /**
     * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
     */
    public function rollBack(): void
    {
    }

    /**
     * @return array<int,array<string,mixed>>
      *
 * @BABOK Related: N/A — test helper/assertion plumbing; carries no requirement of its own
 */
    private function nextRows(): array
    {
        if ($this->selectQueue === []) {
            return [];
        }

        return array_shift($this->selectQueue);
    }
}