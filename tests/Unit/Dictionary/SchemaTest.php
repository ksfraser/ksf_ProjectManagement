<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Dictionary;

use Ksfraser\ProjectManagement\Dictionary\Schema;
use PHPUnit\Framework\TestCase;

/**
 * Schema — the PM data dictionary, and the guard that it stayed FA-free.
 *
 * Ported-in coverage for the dictionary class that came across in the
 * ksf_FA_ProjectManagement consolidation. It is pure (a TableDefinition
 * factory), so no database double is involved.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 *
 * @BABOK Related: BR-012, FR-PM-006
 */
final class SchemaTest extends TestCase
{
    /**
     * Every T_* constant names a logical, UNPREFIXED table. The transport
     * adapter applies the company prefix; baking `0_` in here would double it
     * and is exactly the FA sql/ convention trap.
     *
     * @return void
     */
    public function testTableNamesAreLogicalAndUnprefixed(): void
    {
        $names = [
            Schema::T_PROJECTS, Schema::T_TASKS, Schema::T_TASK_DEPENDENCIES,
            Schema::T_ASSIGNMENTS, Schema::T_PROJECT_TYPES, Schema::T_ACTIVITY,
            Schema::T_FILES, Schema::T_PROGRESS, Schema::T_SALES_ORDERS,
            Schema::T_REVENUE,
        ];

        foreach ($names as $name) {
            $this->assertStringStartsWith('fa_pm_', $name);
            $this->assertStringNotContainsString('0_', $name);
        }
    }

    /**
     * Every table the dictionary knows about resolves through
     * definitionFor() — including the CPM dependency table.
     *
     * @return void
     */
    public function testDefinitionForResolvesEveryKnownTable(): void
    {
        $map = [
            Schema::T_PROJECTS         => 'project_id',
            Schema::T_TASKS            => 'task_id',
            Schema::T_TASK_DEPENDENCIES=> 'dependency_id',
            Schema::T_ASSIGNMENTS      => 'project_id',
            Schema::T_PROJECT_TYPES    => 'id',
            Schema::T_ACTIVITY         => 'id',
            Schema::T_FILES            => 'id',
            Schema::T_PROGRESS         => 'progress_id',
            Schema::T_SALES_ORDERS     => 'id',
            Schema::T_REVENUE          => 'revenue_id',
        ];

        foreach ($map as $table => $pk) {
            $def = Schema::definitionFor($table);
            $this->assertSame($table, $def->name(), $table);
            $this->assertSame($pk, $def->primaryKey(), $table);
        }
    }

    /**
     * The dependency table was missing from BOTH createSqls() and
     * definitionFor()'s table map. A standalone/CLI consumer that built its
     * schema from the dictionary would have got no dependency table at all and
     * then failed on the first CPM schedule. FA itself never hit it —
     * install.sql is what activation applies — which is why it survived the
     * original write-up. Fixed in both copies; the list is asserted above so
     * it cannot silently drop out again.
     *
     * @return void
     */
    public function testCreateSqlsCoversEveryInstalledTable(): void
    {
        $sqls = Schema::createSqls();

        $this->assertArrayHasKey(Schema::T_PROJECTS, $sqls);
        $this->assertArrayHasKey(Schema::T_TASKS, $sqls);
        $this->assertArrayHasKey(Schema::T_TASK_DEPENDENCIES, $sqls);
        foreach ($sqls as $table => $sql) {
            $this->assertStringContainsString('CREATE TABLE', $sql, $table);
        }
    }

    /**
     * The CPM scheduling columns the services persist (es/ef/ls/lf/slack/
     * is_critical) are in the tasks() dictionary, not bolted on by the DAO.
     *
     * @return void
     */
    public function testTasksDictionaryCarriesTheSchedulingColumns(): void
    {
        $def  = Schema::tasks();
        $cols = $def->columns();

        foreach (['es', 'ef', 'ls', 'lf', 'slack', 'is_critical'] as $col) {
            $this->assertTrue($def->hasColumn($col), "tasks() is missing {$col}");
            $this->assertContains($col, $cols);
        }
        $this->assertTrue($def->hasColumn('constraint_type'));
        $this->assertTrue($def->hasColumn('constraint_date'));
        $this->assertContains('is_milestone', $cols);
    }

    /**
     * The dependency table is CPM-shaped: predecessor edge, relation type and
     * lag, with a uniqueness guard against a duplicated edge.
     *
     * @return void
     */
    public function testTaskDependenciesDictionaryShape(): void
    {
        $def = Schema::taskDependencies();

        $this->assertTrue($def->hasColumn('task_id'));
        $this->assertTrue($def->hasColumn('predecessor_id'));
        $this->assertTrue($def->hasColumn('dependency_type'));
        $this->assertTrue($def->hasColumn('lag'));

        $sql = $def->createSql();
        $this->assertStringContainsString('UNIQUE', $sql);
        $this->assertStringContainsString('task_id', $sql);
        $this->assertStringContainsString('predecessor_id', $sql);
    }

    /**
     * Guard for the consolidation itself: this dictionary must not construct a
     * transport, and must not name a FrontAccounting global. The FA copy owns
     * `Schema::adapter()`; this package requires the connection to be injected.
     *
     * @return void
     */
    public function testSchemaExposesNoFaAdapterFactory(): void
    {
        $this->assertFalse(
            method_exists(Schema::class, 'adapter'),
            'Schema::adapter() builds an FaDbAdapter from TB_PREF and must stay in the FA wrapper'
        );

        $source = file_get_contents((string) (new \ReflectionClass(Schema::class))->getFileName());
        $this->assertIsString($source);
        $code = $this->codeOnly($source);
        $this->assertStringNotContainsString('TB_PREF', $code);
        $this->assertStringNotContainsString('FaDbAdapter(', $code);
    }

    /**
     * Comments and string literals blanked out, so prose that merely NAMES an
     * FA global (such as this package's consolidation note) cannot trip the
     * guard above. Byte offsets are preserved, so line numbers stay accurate.
     */
    private function codeOnly(string $source): string
    {
        $blank = static function (string $text): string {
            return preg_replace('/[^\n]/', ' ', $text);
        };
        $result = '';
        foreach (token_get_all($source) as $token) {
            if (is_array($token)) {
                $id = $token[0];
                if ($id === T_COMMENT || $id === T_DOC_COMMENT
                    || $id === T_CONSTANT_ENCAPSED_STRING || $id === T_ENCAPSED_AND_WHITESPACE
                ) {
                    $result .= $blank($token[1]);
                    continue;
                }
            }
            $result .= is_array($token) ? $token[1] : $token;
        }

        return $result;
    }
}