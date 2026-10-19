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
  *
 * @BABOK Related: N/A — architecture guard: enforces the PHP 7.3 syntax floor that ARCH-PM-006 conformance requires
 */
final class Php74CompatibilityTest extends TestCase
{
    /** @var string */
    private $moduleDir;

    /**
     * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
     */
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
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
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
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
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
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
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
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
 */
    public function testPackageShipsSourcesToScan(): void
    {
        $this->assertGreaterThan(20, count($this->shippedSources()));
    }

    /**
     * @return void
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
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
     * Typed property declarations.
     *
     * Typed properties arrived in PHP 7.4, one version ABOVE this package's
     * 7.3 floor, so they belong here rather than in forbiddenConstructs() (that
     * list is about 8.x syntax, which 7.4 also rejects). They are invisible to
     * the whole rest of the tooling: the suite runs on PHP 8.1, where they
     * parse; `php -l` on the same host parses them; and nothing else in the
     * build resolves for 7.3. 160 of them shipped green once already, so this
     * is the guard that stops it happening twice.
     *
     * @return void
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
 */
    public function testNoTypedPropertiesAreShipped(): void
    {
        $violations = [];

        foreach ($this->shippedSources() as $path) {
            foreach ($this->typedPropertiesIn($path) as $hit) {
                $short = ltrim(str_replace($this->moduleDir, '', $path), '/');
                $violations[] = sprintf('%s:%d %s', $short, $hit['line'], $hit['text']);
            }
        }

        $this->assertSame(
            [],
            $violations,
            "Typed properties shipped; they are PHP 7.4+ and this package's floor is 7.3.\n"
            . "Use an untyped property with a /** @var T *\/ docblock instead:\n  "
            . implode("\n  ", $violations)
        );
    }

    /**
     * Every typed property declaration in a file, found by walking the token
     * stream forward from each visibility modifier rather than by regex.
     *
     * The naive regex `/(public|private|protected)\s+\w+\s+\$/` cannot tell a
     * typed property from a one-line method signature and matches both, so this
     * reads the actual tokens: starting at a visibility modifier, a type is
     * present only if a type-bearing token (T_STRING / T_ARRAY / T_CALLABLE /
     * T_NS_SEPARATOR, plus `?`) occurs before the declaration's T_VARIABLE.
     *
     * @return array<int,array{line:int,text:string}>
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
 */
    private function typedPropertiesIn(string $path): array
    {
        $src    = (string) file_get_contents($path);
        $tokens = token_get_all($src);
        $count  = count($tokens);
        $found  = [];

        $typeTokens = [T_STRING, T_ARRAY, T_NS_SEPARATOR, T_CALLABLE];

        for ($i = 0; $i < $count; $i++) {
            if (!is_array($tokens[$i])
                || !in_array($tokens[$i][0], [T_PUBLIC, T_PRIVATE, T_PROTECTED, T_VAR], true)
            ) {
                continue;
            }

            $typeStart = null;
            $typeEnd   = null;
            $isTyped   = false;
            $parts     = [$tokens[$i][1]];
            $j         = $i + 1;

            while ($j < $count) {
                $tok = $tokens[$j];
                if (is_array($tok)) {
                    $id = $tok[0];
                    if ($id === T_WHITESPACE || $id === T_DOC_COMMENT || $id === T_COMMENT) {
                        $parts[] = $tok[1];
                        $j++;
                        continue;
                    }
                    if ($id === T_STATIC || (defined('T_READONLY') && $id === T_READONLY)) {
                        $parts[] = $tok[1];
                        $j++;
                        continue;
                    }
                    if (in_array($id, $typeTokens, true)) {
                        $isTyped = true;
                        $parts[] = $tok[1];
                        $j++;
                        continue;
                    }
                    break;
                }
                if ($tok === '?' || $tok === '|') {
                    $isTyped = true;
                    $parts[] = $tok;
                    $j++;
                    continue;
                }
                break;
            }

            if (!$isTyped) {
                continue;
            }

            // A type only makes this a typed *property* if T_VARIABLE comes next.
            // `public function foo(): string` reaches here too and must not count.
            while ($j < $count && is_array($tokens[$j])
                && in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT], true)
            ) {
                $j++;
            }
            if ($j >= $count || !is_array($tokens[$j]) || $tokens[$j][0] !== T_VARIABLE) {
                continue;
            }

            $parts[] = $tokens[$j][1];
            $found[] = [
                'line' => $tokens[$i][2],
                'text' => trim((string) preg_replace('/\s+/', ' ', implode('', $parts))),
            ];
        }

        return $found;
    }

    /**
     * Guards the typed-property scanner above against silently matching nothing,
     * which is the failure mode that let the original 160 through.
     *
     * @return void
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
 */
    public function testTypedPropertyScannerDetectsAViolationInASyntheticSample(): void
    {
        $sample = <<<'PHP'
<?php
class Sample
{
    private string $typed;
    private ?int $nullable = null;
    protected array $rows = [];
    private $plain;
    public static $stateless;
    public function notAProperty(): string { return ''; }
}
PHP;
        $tmp = tempnam(sys_get_temp_dir(), 'php74guard') . '.php';
        file_put_contents($tmp, $sample);
        try {
            $hits = $this->typedPropertiesIn($tmp);
        } finally {
            @unlink($tmp);
        }

        $this->assertCount(3, $hits, 'Scanner must find the 3 typed properties and ignore the rest.');
        $this->assertStringContainsString('private string $typed', $hits[0]['text']);
        $this->assertStringContainsString('private ?int $nullable', $hits[1]['text']);
        $this->assertStringContainsString('protected array $rows', $hits[2]['text']);
    }

    /**
     * Source with comments and string literals blanked out. Byte offsets are
     * preserved (replaced spans keep their length) so reported line numbers
     * stay accurate.
      *
 * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
 */
    private function codeOnly(string $source): string
    {
        /**
         * @BABOK Related: N/A — architecture guard; enforces a structural rule rather than a behavioural requirement
         */
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