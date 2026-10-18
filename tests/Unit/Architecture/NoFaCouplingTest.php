<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Architecture;

use PHPUnit\Framework\TestCase;

/**
 * Guards the package boundary: this is a FA-FREE library.
 *
 * The architecture rule is one-directional. `ksf_<Module>` is business logic
 * and becomes a Packagist library; `ksf_FA_<Module>` is the FrontAccounting
 * wrapper that supplies hooks and the DB transport. Runtime deployable code
 * inside FA must go through FaDbAdapter, which calls native `db_*`; PDO is
 * permitted for tests only.
 *
 * That boundary is exactly the kind of thing a consolidation erodes by
 * accident: a class copied out of the FA tree drags one `Schema::adapter()`
 * or one inline `db_query()` across with it and the package is quietly no
 * longer standalone. These tests are the tripwire. Comments and string
 * literals are stripped via the tokenizer first, so prose that has to NAME
 * `db_query` to explain why it is forbidden cannot trip them.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 *
 * @UML Note: AGENTS.md §ksf_common_db (interface + DI'd transport)
 */
final class NoFaCouplingTest extends TestCase
{
    /**
     * FrontAccounting globals and files. A `use` of one, or a call to one, is
     * coupling; a mention in a docblock is documentation.
     *
     * @return array<string,string>
     */
    private function faSymbols(): array
    {
        return [
            'native db function'   => '/(?<![\w$>:\\\\])(db_query|db_fetch_assoc|db_fetch_array|db_fetch_row|db_num_rows|db_escape|db_insert_id|db_error|db_query_value|db_query_row|db_affected_rows)\s*\(/',
            'FA table prefix const'=> '/(?<![\w$])TB_PREF\b/',
            'FA hook dispatcher'   => '/(?<![\w$])(hook_invoke|hook_invoke_all|hook_invoke_first|hook_invoke_last)\s*\(/',
            'FA session/current user' => '/(?<![\w$])wa_current_user\b/',
            'FA class'             => '/(?<![\w$\\\\])(hooks_ksf|FA_PM_Module)\b/',
        ];
    }

    /**
     * Everything under src/ is runtime code: the package must never call an FA
     * global, hard-depend on an FA namespace, or construct an FA transport.
     *
     * @return void
     */
    public function testShippedSourceContainsNoFaRuntime(): void
    {
        $violations = [];

        foreach ($this->shippedSources() as $path) {
            $source = (string) file_get_contents($path);
            $code   = $this->codeOnly($source);

            foreach ($this->faSymbols() as $label => $pattern) {
                if (preg_match($pattern, $code, $m, PREG_OFFSET_CAPTURE) !== 1) {
                    continue;
                }
                $line = substr_count(substr($code, 0, $m[0][1]), "\n") + 1;
                $violations[] = sprintf('%s:%d [%s] %s', $this->short($path), $line, $label, trim($m[0][0]));
            }
        }

        $this->assertSame(
            [],
            $violations,
            "FA runtime leaked into the standalone package:\n  " . implode("\n  ", $violations)
        );
    }

    /**
     * No FA-namespaced import. `ksfraser\CommonDb\*` is fine and expected —
     * that is the shared contract this package codes against.
     *
     * @return void
     */
    public function testShippedSourceImportsNoFrontAccountingNamespace(): void
    {
        $violations = [];

        foreach ($this->shippedSources() as $path) {
            $source = (string) file_get_contents($path);
            if (preg_match_all('/^\s*use\s+([^;]+);/m', $this->codeOnly($source), $m) === 0) {
                continue;
            }
            foreach ($m[1] as $import) {
                $import = trim($import);
                if (stripos($import, 'FrontAccounting') === false) {
                    continue;
                }
                $violations[] = $this->short($path) . ': use ' . $import;
            }
        }

        $this->assertSame(
            [],
            $violations,
            "FrontAccounting namespace imported into the standalone package:\n  " . implode("\n  ", $violations)
        );
    }

    /**
     * The package ships no FA extension surface. hooks.php loads during FA
     * bootstrap and an error there breaks every page in the application, so a
     * hooks.php in here would be a landmine, not a convenience.
     *
     * @return void
     */
    public function testPackageShipsNoFrontAccountingExtensionSurface(): void
    {
        $root = $this->moduleDir();

        foreach (['hooks.php', 'includes', 'pages', 'sql', '_init'] as $forbidden) {
            $this->assertFileDoesNotExist(
                $root . '/' . $forbidden,
                "{$forbidden} is FrontAccounting wiring and has no place in the standalone package"
            );
        }
    }

    /**
     * The DAO layer codes against the shared contract, never a concrete
     * transport. `FaDbAdapter` may not even be named in src/.
     *
     * @return void
     */
    public function testNoFaAdapterIsNamedInRuntimeSource(): void
    {
        foreach ($this->shippedSources() as $path) {
            $code = $this->codeOnly((string) file_get_contents($path));
            $this->assertStringNotContainsString(
                'FaDbAdapter',
                $code,
                $this->short($path) . ' names FaDbAdapter; the transport is the caller\'s decision'
            );
        }
    }

    /**
     * Every shipped src/ class must actually be reachable through the declared
     * PSR-4 map.
     *
     * This is not theoretical: 27 of the package's 33 src/ classes used to sit
     * at `src/Ksfraser/ProjectManagement/<Sub>/` while composer.json maps
     * `Ksfraser\ProjectManagement\ => src/`, so the autoloader looked for
     * `src/Ksfraser/ProjectManagement/...` a SECOND time and never found them.
     * They were unreachable, and 152 of 162 tests errored with "does not
     * exist" until the layout was corrected.
     *
     * @return void
     */
    public function testEverySourceClassIsPsr4Addressable(): void
    {
        $violations = [];

        foreach ($this->shippedSources() as $path) {
            $code = $this->codeOnly((string) file_get_contents($path));
            if (preg_match('/^\s*namespace\s+([^;]+);/m', $code, $m) !== 1) {
                continue;
            }
            $namespace = trim($m[1]);
            $relative  = ltrim(str_replace($this->moduleDir(), '', $path), '/');
            // src/ is the PSR-4 root for the Ksfraser\ProjectManagement\ prefix.
            $sub       = str_replace('\\', '/', substr($namespace, strlen('Ksfraser\\ProjectManagement\\')));
            $expected  = 'src/' . ($sub === '' ? '' : $sub . '/') . basename($path);

            if (strtolower($relative) !== strtolower($expected)) {
                $violations[] = sprintf('%s declares %s but sits at the PSR-4 path %s', $relative, $namespace, $expected);
            }
        }

        $this->assertSame([], $violations, "PSR-4 layout violation:\n  " . implode("\n  ", $violations));
    }

    /**
     * @return string[]
     */
    private function shippedSources(): array
    {
        $found    = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->moduleDir() . '/src', \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if (strtolower($file->getExtension()) === 'php') {
                $found[] = $file->getPathname();
            }
        }
        sort($found);

        return $found;
    }

    private function moduleDir(): string
    {
        return dirname(__DIR__, 3);
    }

    private function short(string $path): string
    {
        return ltrim(str_replace($this->moduleDir(), '', $path), '/');
    }

    /**
     * Comments and string literals blanked out, preserving byte offsets so line
     * numbers reported by the scanners stay accurate.
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
                    || $id === T_INLINE_HTML
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