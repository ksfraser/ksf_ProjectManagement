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
     */
    public function queueSelects(array $queue): void
    {
        $this->selectQueue = $queue;
    }

    /**
     * @return array<int,array{sql:string,params:array}>
     */
    public function writes(): array
    {
        return $this->writes;
    }

    /**
     * @return array<int,array{sql:string,params:array}>
     */
    public function reads(): array
    {
        return $this->reads;
    }

    /**
     * The SQL text of the most recent read or write.
     *
     * @return string
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
     */
    public function lastParams(): array
    {
        $last = end($this->log);

        return $last === false ? [] : $last['params'];
    }

    public function fetchAssoc(string $sql, array $params = []): ?array
    {
        $rows = $this->nextRows();
        $this->reads[] = ['sql' => $sql, 'params' => $params];
        $this->log[]   = ['sql' => $sql, 'params' => $params];

        return $rows === [] ? null : $rows[0];
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $rows = $this->nextRows();
        $this->reads[] = ['sql' => $sql, 'params' => $params];
        $this->log[]   = ['sql' => $sql, 'params' => $params];

        return $rows;
    }

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

    public function executeUpdate(string $sql, array $params = []): int
    {
        $this->writes[] = ['sql' => $sql, 'params' => $params];
        $this->log[]    = ['sql' => $sql, 'params' => $params];

        return 1;
    }

    public function lastInsertId(): int
    {
        return $this->nextId++;
    }

    public function quote($value): string
    {
        return "'" . str_replace("'", "''", (string) $value) . "'";
    }

    public function beginTransaction(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollBack(): void
    {
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function nextRows(): array
    {
        if ($this->selectQueue === []) {
            return [];
        }

        return array_shift($this->selectQueue);
    }
}