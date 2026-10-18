<?php

declare(strict_types=1);

namespace Ksfraser\ProjectManagement\Tests\Unit\Architecture;

use PHPUnit\Framework\TestCase;

/**
 * Guards the package against PHP 8-only syntax.
 *
 * This package declares a PHP 7.3/7.4 floor, but the unit suite executes on
 * whatever PHP the developer runs it with (8.1 here), where PHP 8 syntax
 * parses cleanly. That mismatch hides a whole class of defect: the union
 * return type `int|string` on ProjectFileService::saveFileRecord() shipped and
 * stayed green, while on 7.3/7.4 the file is a PARSE error and any consumer
 * that autoloaded it died outright. `php -l` on an 8.x host cannot catch it,
 * for exactly the same reason.
 *
 * So this scans the shipped source for constructs the target runtime cannot
 * parse. Comments and string literals are stripped via the tokenizer first, so
 * prose that has to NAME a construct to explain why it is forbidden cannot trip
 * it.
 *
 * Ported in shape from ksf_FA_ProjectManagement/tests/Unit/Php74CompatibilityTest,
 * which was written after the same defect shipped there.
 *
 * @package Ksfraser\ProjectManagement
 * @since   1.0.0
 */
final class Php74CompatibilityTest extends TestCase
{
    /** @var string */
    private $moduleDir;

    protected function setUp(): void
    {
        $this->moduleDir = dirname(__DIR__, 3);
    }

    /**
     * Constructs the target runtime rejects with a *parse* error, so the file
     * cannot even be loaded. `mixed` and `never` are deliberately absent here:
     * they PARSE on 7.4 as ordinary class names and fail at call time instead,
     * so they belong to runtimeLandmines() rather than here.
     *
     * @return array<string,string>
     */
    private function forbiddenConstructs(): array
    {
        return [
            // 8.0 union types, return position: `function f(): object|false`
            'union return type'  => '/\)\s*:\s*\??[A-Za-z_\\\\][A-Za-z0-9_\\\\]*\s*\|/',
            // 8.0 union types, parameter position: `function f(int|string $x)`
            'union param type'   => '/function\s+&?\w+\s*\(\s*\??[A-Za-z_\\\\][A-Za-z0-9_\\\\]*\s*\|/',
            // 8.0 nullsafe operator
            'nullsafe operator'  => '/\?->/',
            // 8.0 match expression
            'match expression'   => '/(?<![\w$])match\s*\(/',
            // 8.1 enum declaration
            'enum declaration'   => '/(?<![\w$])enum\s+[A-Za-z_]/',
            // 8.0 constructor property promotion
            'ctor promotion'     => '/function\s+__construct\s*\([^)]*\b(?:public|private|protected)\b/',
            // 8.0 static return type
            'static return type' => '/\)\s*:\s*static\b/',
            // 8.1 readonly property
            'readonly property'  => '/\breadonly\s+\$|\breadonly\s+[A-Za-z_\\\\]/',
        ];
    }

    /**
     * Constructs that PARSE on 7.4 but are broken there, so a parse-only scan
     * misses them: 7.4 does not reserve these words, so the parser reads them
     * as class names and every call raises a TypeError.
     *
     * @return array<string,string>
     */
    private function runtimeLandmines(): array
    {
        return [
            'mixed return type' => '/\)\s*:\s*mixed\b/',
            'never return type' => '/\)\s*:\s*never\b/',
        ];
    }

    /**
     * Every non-vendor PHP source file the package ships and executes.
     *
     * @return string[]
     */
    private function shippedSources(): array
    {
        $found    = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->moduleDir . '/src', \FilesystemIterator::SKIP_DOTS)
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

    /**
     * Guards against the scanner silently matching nothing.
     *
     * @return void
     */
    public function testPackageShipsSourcesToScan(): void
    {
        $this->assertGreaterThan(20, count($this->shippedSources()));
    }

    /**
     * @return void
     */
    public function testNoPhp8OnlySyntaxIsShipped(): void
    {
        $violations = [];

        foreach ($this->shippedSources() as $path) {
            $code  = $this->codeOnly((string) file_get_contents($path));
            $short = ltrim(str_replace($this->moduleDir, '', $path), '/');

            foreach ($this->forbiddenConstructs() + $this->runtimeLandmines() as $label => $pattern) {
                if (preg_match($pattern, $code, $m, PREG_OFFSET_CAPTURE) !== 1) {
                    continue;
                }
                $line = substr_count(substr($code, 0, $m[0][1]), "\n") + 1;
                $violations[] = sprintf('%s:%d [%s] %s', $short, $line, $label, trim($m[0][0]));
            }
        }

        $this->assertSame(
            [],
            $violations,
            "PHP 8-only syntax shipped; the package floor is 7.3:\n  " . implode("\n  ", $violations)
        );
    }

    /**
     * Source with comments and string literals blanked out. Byte offsets are
     * preserved (replaced spans keep their length) so reported line numbers
     * stay accurate.
     */
    private function codeOnly(string $source): string
    {
        $blank  = static function (string $text): string {
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