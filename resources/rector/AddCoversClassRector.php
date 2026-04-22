<?php

declare(strict_types=1);

namespace Resources\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use Rector\Rector\AbstractRector;

class AddCoversClassRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        if ($this->hasCoversClass($node)) {
            return null;
        }

        $className = $this->getName($node);

        if ($className === null) {
            return null;
        }

        $coveredClass = $this->resolveCoveredClass($className);

        if ($coveredClass === null) {
            return null;
        }

        $this->addCoversAttribute($node, $coveredClass);

        return $node;
    }

    private function hasCoversClass(Class_ $class): bool
    {
        foreach ($class->attrGroups as $group) {
            foreach ($group->attrs as $attr) {
                if ($this->getName($attr->name) === 'CoversClass') {
                    return true;
                }
            }
        }

        return false;
    }

    private function resolveCoveredClass(string $testClass): ?string
    {
        if (! str_ends_with($testClass, 'Test')) {
            return null;
        }

        return substr($testClass, 0, -4);
    }

    private function addCoversAttribute(Class_ $class, string $coveredClass): void
    {
        $attribute = new Attribute(
            new Name('CoversClass'),
            [
                new Arg(
                    new ClassConstFetch(
                        new Name($coveredClass),
                        'class'
                    )
                ),
            ]
        );

        $class->attrGroups[] = new AttributeGroup([$attribute]);
    }
}
