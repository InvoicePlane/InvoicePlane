<?php

declare(strict_types=1);

namespace Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\BuilderFactory;
use PhpParser\NodeFinder;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class MarkWeakTestIncompleteRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof ClassMethod) {
            return null;
        }

        if (! $this->isTestMethod($node)) {
            return null;
        }

        if ($node->stmts === null) {
            return null;
        }

        if (! $this->isWeakTest($node)) {
            return null;
        }

        if ($this->alreadyMarkedIncomplete($node)) {
            return null;
        }

        $incompleteCall = $this->createIncompleteCall();

        array_unshift(
            $node->stmts,
            $incompleteCall
        );

        return $node;
    }

    private function isTestMethod(ClassMethod $method): bool
    {
        $name = (string) $method->name;

        if (str_starts_with($name, 'test')) {
            return true;
        }

        foreach ($method->attrGroups as $group) {
            foreach ($group->attrs as $attr) {
                if ($this->getName($attr->name) === 'Test') {
                    return true;
                }
            }
        }

        return false;
    }

    private function isWeakTest(ClassMethod $method): bool
    {
        $finder = new NodeFinder();

        $methodCalls = $finder->findInstanceOf(
            $method->stmts,
            MethodCall::class
        );

        $assertCalls = [];

        foreach ($methodCalls as $call) {
            $name = $this->getName($call->name);

            if ($name === null) {
                continue;
            }

            if ($this->isLiteralTrueAssertion($call)) {
                return true;
            }

            if (str_starts_with($name, 'assert')) {
                $assertCalls[] = $name;
            }
        }

        if ($assertCalls === []) {
            return true;
        }

        return $this->onlyStatusAssertions(
            $assertCalls
        );
    }

    private function onlyStatusAssertions(
        array $assertCalls
    ): bool {
        foreach ($assertCalls as $call) {
            if (! $this->isTrivialAssertion($call)) {
                return false;
            }
        }

        return true;
    }

    private function alreadyMarkedIncomplete(
        ClassMethod $method
    ): bool {
        $finder = new NodeFinder();

        $calls = $finder->findInstanceOf(
            $method->stmts,
            MethodCall::class
        );

        foreach ($calls as $call) {
            if (
                $this->getName($call->name)
                === 'markTestIncomplete'
            ) {
                return true;
            }
        }

        return false;
    }

    private function createIncompleteCall(): Stmt\Expression
    {
        return new Stmt\Expression(
            new MethodCall(
                new Expr\Variable('this'),
                'markTestIncomplete',
                [
                    $this->nodeFactory->createArg(
                        'weak test'
                    ),
                ]
            )
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Marks weak PHPUnit tests as incomplete',
            []
        );
    }

    private function isTrivialAssertion(
        string $assertName
    ): bool {
        return in_array(
            $assertName,
            [
                'assertOk',
                'assertStatus',
                'assertTrue',
                'assertFalse',
                'assertNotNull',
                'assertNull',
            ],
            true
        );
    }

    private function isLiteralTrueAssertion(
        MethodCall $call
    ): bool {
        if ($this->getName($call->name) !== 'assertTrue') {
            return false;
        }

        if ($call->args === []) {
            return false;
        }

        $arg = $call->args[0]->value;

        return $arg instanceof Node\Expr\ConstFetch
            && $this->getName($arg->name) === 'true';
    }
}