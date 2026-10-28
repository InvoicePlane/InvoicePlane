<?php

namespace Tests\Unit\Core;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * CI and the supported baseline are PHP 8.2 (the docker workspace runs 8.4, which hides the problem). Calling a
 * function introduced later is a fatal "Call to undefined function" on that code path, so application code may not
 * use them. The scan is done on the syntax tree so comments and strings do not count.
 *
 * Introduced after PHP 8.2:
 *   8.3  json_validate, str_increment, str_decrement
 *   8.4  mb_trim, mb_ltrim, mb_rtrim, mb_str_pad, mb_ucfirst, mb_lcfirst, array_find, array_find_key, array_any, array_all
 */
#[Group('compatibility')]
final class Php82CompatibilityTest extends TestCase
{
    private const AFTER_PHP_82 = [
        'json_validate', 'str_increment', 'str_decrement', 'mb_str_pad',
        'mb_trim', 'mb_ltrim', 'mb_rtrim', 'mb_ucfirst', 'mb_lcfirst',
        'array_find', 'array_find_key', 'array_any', 'array_all',
    ];

    #[Test]
    public function it_calls_no_function_that_only_exists_after_php_82(): void
    {
        /* Arrange: every place PHP is executed, not only application/ (the entry point and the CI scripts ran 8.4-only calls) */
        $parser = (new ParserFactory())->createForHostVersion();
        $finder = new NodeFinder();
        $root   = dirname(__DIR__, 3);
        $found  = [];

        /* Act */
        foreach ($this->phpFiles($root) as $path) {
            $ast = $parser->parse((string) file_get_contents($path)) ?? [];
            foreach ($finder->find($ast, static fn (Node $n): bool => $n instanceof FuncCall && $n->name instanceof Node\Name) as $call) {
                $name = strtolower($call->name->toString());
                if (in_array($name, self::AFTER_PHP_82, true)) {
                    $found[] = substr($path, strlen($root) + 1) . ':' . $call->getStartLine() . ' ' . $name . '()';
                }
            }
        }

        /* Assert */
        sort($found);
        self::assertSame([], $found, "These calls need PHP 8.3/8.4 but the supported baseline is PHP 8.2:\n" . implode("\n", $found));
    }

    /**
     * @return list<string>
     */
    private function phpFiles(string $root): array
    {
        $files = glob($root . '/*.php') ?: [];

        foreach (['application', 'bootstrap', 'public', '.github/scripts', 'tests'] as $directory) {
            if ( ! is_dir($root . '/' . $directory)) {
                continue;
            }

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
                if ($file->getExtension() === 'php') {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }
}
