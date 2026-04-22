<?php

declare(strict_types=1);

namespace Resources\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symfony\Component\Finder\Finder;

class AddCoversClassAndMoveTestRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            Class_::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        $filePath = $node->getAttribute(
            AttributeKey::FILE_INFO
        )->getRealPath();

        if (! $this->isRootTestFile($filePath)) {
            return null;
        }

        if ($this->hasCoversClass($node)) {
            return null;
        }

        $coveredClass = $this->guessCoveredClass(
            $filePath
        );

        if ($coveredClass === null) {
            return null;
        }

        $this->addCoversAttribute(
            $node,
            $coveredClass
        );

        $this->moveTestFile(
            $filePath,
            $coveredClass
        );

        return $node;
    }

    private function isRootTestFile(
        string $filePath
    ): bool {
        $testsDir = realpath(
            getcwd() . '/tests'
        );

        if ($testsDir === false) {
            return false;
        }

        $directory = dirname($filePath);

        return realpath($directory)
            === $testsDir;
    }

    private function hasCoversClass(
        Class_ $class
    ): bool {
        foreach ($class->attrGroups as $group) {
            foreach ($group->attrs as $attr) {
                if (
                    $this->getName($attr->name)
                    === 'CoversClass'
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    private function guessCoveredClass(
        string $filePath
    ): ?string {
        $fileName = basename($filePath);

        if (! str_ends_with(
            $fileName,
            'Test.php'
        )) {
            return null;
        }

        $className = str_replace(
            'Test.php',
            '',
            $fileName
        );

        return $this->findMatchingClass(
            $className
        );
    }

    private function findMatchingClass(
        string $className
    ): ?string {
        $finder = new Finder();

        $finder
            ->files()
            ->in(getcwd() . '/app')
            ->name($className . '.php');

        foreach ($finder as $file) {
            $relativePath =
                $file->getRelativePath();

            $namespace =
                'App\\' .
                str_replace(
                    '/',
                    '\\',
                    $relativePath
                );

            return '\\' .
                trim(
                    $namespace .
                    '\\' .
                    $className,
                    '\\'
                );
        }

        return null;
    }

    private function addCoversAttribute(
        Class_ $class,
        string $coveredClass
    ): void {
        $attribute = new Attribute(
            new Name('CoversClass'),
            [
                new Node\Arg(
                    new Node\Expr\ClassConstFetch(
                        new Name(
                            ltrim(
                                $coveredClass,
                                '\\'
                            )
                        ),
                        'class'
                    )
                ),
            ]
        );

        $class->attrGroups[] =
            new AttributeGroup(
                [$attribute]
            );
    }

    private function moveTestFile(
        string $filePath,
        string $coveredClass
    ): void {
        $targetPath =
            $this->buildTargetPath(
                $filePath,
                $coveredClass
            );

        if ($targetPath === null) {
            return;
        }

        if (file_exists($targetPath)) {
            return;
        }

        @mkdir(
            dirname($targetPath),
            0777,
            true
        );

        rename(
            $filePath,
            $targetPath
        );
    }

    private function buildTargetPath(
        string $filePath,
        string $coveredClass
    ): ?string {
        $baseTests =
            realpath(
                getcwd() . '/tests'
            );

        if ($baseTests === false) {
            return null;
        }

        $classPath =
            str_replace(
                'App\\',
                '',
                ltrim(
                    $coveredClass,
                    '\\'
                )
            );

        $classPath =
            str_replace(
                '\\',
                '/',
                $classPath
            );

        $fileName =
            basename($filePath);

        return
            $baseTests .
            '/Unit/' .
            dirname($classPath) .
            '/' .
            $fileName;
    }
}
