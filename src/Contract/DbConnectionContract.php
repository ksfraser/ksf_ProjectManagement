<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Contract;

/**
 * DbConnectionContract — transport seam for the ksf PM package.
 *
 * Deliberately FA-agnostic and PDO-shaped in METHOD SEMANTICS only; the
 * package NEVER depends on a PDO class or impl. The FA host injects an
 * adapter backed by FrontAccounting's db_query/db_escape procedural core
 * (see ksf-common-fa FaDbAdapter); standalone hosts inject a PDO adapter or
 * an in-memory test driver. This mirrors the ksf-common-db DbConnectionInterface
 * shape so a single adapter can serve both packages.
 *
 * PHP 7.3 compatible. Side-effect-free contract — exceptions are transport
 * concerns, not this interface's concern; DB failures surface as exceptions
 * from the concrete adapter.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 *
 * Contract key:
 *   fetchMap / fetchAll  -> selectors (assoc row maps)
 *   execute/insert/update-> effects; execute returns affected/inserted id
 *   quote/escape           -> literal escaping for positional SQL
 */
interface DbConnectionContract
{
    /** @return array<string,mixed>|null first row assoc or null */
    public function fetchMap(string $sql, array $params = []);

    /** @return array<int,array<string,mixed>> all rows or [] */
    public function fetchAll(string $sql, array $params = []);

    /** @return mixed last inserted id for a UUID/autoindex table */
    public function insert(string $sql, array $params = []);

    /** @return int affected row count */
    public function execute(string $sql, array $params = []);

    /** @return int affected row count */
    public function update(string $sql, array $params = []);

    /** @return string escaped literal (transport-appropriate) */
    public function quote(string $value);
}
