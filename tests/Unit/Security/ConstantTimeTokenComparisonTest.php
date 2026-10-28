<?php

namespace Tests\Unit\Security;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-wcqc-qqv5-65ph: the stored password reset token digest must be compared with hash_equals(), never with
 * ==, !=, === or !==. The timing property cannot be observed from a request (see PasswordResetTokenComparisonTest,
 * which asserts the behaviour around it), and PHP gives no seam to intercept hash_equals(). The property is
 * therefore asserted on the syntax tree: swapping the call for an operator turns this test red.
 */
#[Group('security')]
final class ConstantTimeTokenComparisonTest extends TestCase
{
    private const FILE = 'application/modules/sessions/controllers/Sessions.php';

    #[Test]
    public function it_compares_the_stored_reset_token_with_hash_equals(): void
    {
        /* Arrange */
        $ast = $this->parse(self::FILE);

        /* Act */
        $calls = (new NodeFinder())->find($ast, fn (Node $n): bool => $n instanceof FuncCall
            && $n->name instanceof Node\Name
            && $n->name->toLowerString() === 'hash_equals'
            && $this->mentions($n, 'user_passwordreset_token'));

        /* Assert */
        self::assertNotEmpty($calls, 'The stored reset token must be compared through hash_equals().');
    }

    #[Test]
    public function it_never_compares_the_stored_reset_token_with_an_operator(): void
    {
        /* Arrange */
        $ast = $this->parse(self::FILE);

        /* Act */
        $comparisons = (new NodeFinder())->find($ast, fn (Node $n): bool => $n instanceof BinaryOp
            && ($n instanceof BinaryOp\Identical || $n instanceof BinaryOp\NotIdentical || $n instanceof BinaryOp\Equal || $n instanceof BinaryOp\NotEqual)
            && ($this->mentions($n->left, 'user_passwordreset_token') || $this->mentions($n->right, 'user_passwordreset_token')));

        /* Assert */
        self::assertSame([], $comparisons, 'A short-circuiting comparison of the reset token leaks timing.');
    }

    /**
     * @return Node\Stmt[]
     */
    private function parse(string $relativePath): array
    {
        $code = (string) file_get_contents(dirname(__DIR__, 3) . '/' . $relativePath);

        return (new ParserFactory())->createForHostVersion()->parse($code) ?? [];
    }

    private function mentions(Node $node, string $property): bool
    {
        return (new NodeFinder())->findFirst($node, static fn (Node $n): bool => $n instanceof Node\Expr\PropertyFetch
            && $n->name instanceof Node\Identifier
            && $n->name->toString() === $property) !== null;
    }
}
