<?php

namespace Tests\Unit\Core;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A class, function or method declared twice is a compile error ("Cannot redeclare ...") on the request that loads
 * both declarations, and it only shows up on the code path that does (a demo server fataled on /settings because
 * Settings::remove_logo() was declared twice). Two checks over the syntax tree of application/:
 *
 *  - no class declares the same method name twice;
 *  - no two files declare the same class or function name, except HMVC module controllers (every module has its own
 *    Ajax, Payments, ... controller and only one of them is ever loaded per request) and third_party code.
 */
#[Group('compatibility')]
final class NoDuplicateDeclarationsTest extends TestCase
{
    #[Test]
    public function it_declares_no_method_twice_in_one_class(): void
    {
        /* Act */
        $duplicates = [];
        foreach ($this->declarations() as ['file' => $file, 'ast' => $ast]) {
            foreach ((new NodeFinder())->findInstanceOf($ast, Class_::class) as $class) {
                $seen = [];
                foreach ($class->getMethods() as $method) {
                    $name = strtolower($method->name->toString());
                    if (isset($seen[$name])) {
                        $duplicates[] = $file . ': ' . ($class->name?->toString() ?? 'anonymous') . '::' . $method->name->toString() . '() on lines ' . $seen[$name] . ' and ' . $method->getStartLine();
                    }
                    $seen[$name] = $method->getStartLine();
                }
            }
        }

        /* Assert */
        self::assertSame([], $duplicates, "Methods declared twice:\n" . implode("\n", $duplicates));
    }

    #[Test]
    public function it_declares_every_class_and_function_in_exactly_one_file(): void
    {
        /* Act */
        $declaredIn = [];
        foreach ($this->declarations() as ['file' => $file, 'ast' => $ast]) {
            if (str_starts_with($file, 'third_party/') || (bool) preg_match('#^(modules/[^/]+/)?controllers/#', $file)) {
                continue;
            }

            $finder = new NodeFinder();
            foreach ($finder->findInstanceOf($ast, Class_::class) as $class) {
                if ($class->name !== null) {
                    $declaredIn['class ' . strtolower($class->name->toString())][] = $file;
                }
            }
            foreach ($finder->findInstanceOf($ast, Function_::class) as $function) {
                $declaredIn['function ' . strtolower($function->name->toString())][] = $file;
            }
        }

        $duplicates = [];
        foreach ($declaredIn as $name => $files) {
            if (count(array_unique($files)) > 1) {
                $duplicates[] = $name . ' in ' . implode(', ', array_unique($files));
            }
        }
        sort($duplicates);

        /* Assert */
        self::assertSame([], $duplicates, "Declared in more than one file:\n" . implode("\n", $duplicates));
    }

    /**
     * @return iterable<array{file: string, ast: array<Node\Stmt>}>
     */
    private function declarations(): iterable
    {
        $parser = (new ParserFactory())->createForHostVersion();
        $root   = dirname(__DIR__, 3) . '/application';

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() === 'php') {
                yield ['file' => substr($file->getPathname(), strlen($root) + 1), 'ast' => $parser->parse((string) file_get_contents($file->getPathname())) ?? []];
            }
        }
    }
}
