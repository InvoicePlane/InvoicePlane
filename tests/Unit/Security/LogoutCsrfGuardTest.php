<?php

namespace Tests\Unit\Security;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * GHSA-wj32-69hp-233w: Sessions::logout() must only destroy the session behind a verified CSRF token.
 *
 * In the stock configuration CodeIgniter's global CSRF check rejects a tokenless POST before any controller runs, so
 * a request cannot reach the inline verify_csrf_token() call with an invalid token and removing the call is not
 * visible over HTTP (the GET refusal and the missing-token 403 are covered in Tests\Feature\Security\LogoutCsrfTest). The inline check is the
 * second layer for a deployment that lists the URI in csrf_exclude_uris, so it is asserted on the syntax tree:
 * sess_destroy() must sit inside an if whose condition calls verify_csrf_token().
 */
#[Group('security')]
final class LogoutCsrfGuardTest extends TestCase
{
    #[Test]
    public function it_destroys_the_session_only_inside_a_verify_csrf_token_branch(): void
    {
        /* Arrange */
        $code   = (string) file_get_contents(dirname(__DIR__, 3) . '/application/modules/sessions/controllers/Sessions.php');
        $ast    = (new ParserFactory())->createForHostVersion()->parse($code) ?? [];
        $finder = new NodeFinder();

        /** @var ClassMethod $logout */
        $logout = $finder->findFirst($ast, static fn (Node $n): bool => $n instanceof ClassMethod && $n->name->toString() === 'logout');
        self::assertNotNull($logout, 'Sessions::logout() must exist.');

        /* Act */
        $guarded = $finder->find($logout->stmts, static function (Node $n) use ($finder): bool {
            if ( ! $n instanceof If_) {
                return false;
            }

            $checksToken = $finder->findFirst($n->cond, static fn (Node $c): bool => $c instanceof FuncCall
                && $c->name instanceof Node\Name
                && $c->name->toString() === 'verify_csrf_token') !== null;

            $destroys = $finder->findFirst($n->stmts, static fn (Node $s): bool => $s instanceof MethodCall
                && $s->name instanceof Node\Identifier
                && $s->name->toString() === 'sess_destroy') !== null;

            return $checksToken && $destroys;
        });

        $destroyCalls = $finder->find($logout->stmts, static fn (Node $s): bool => $s instanceof MethodCall
            && $s->name instanceof Node\Identifier
            && $s->name->toString() === 'sess_destroy');

        /* Assert */
        self::assertCount(1, $destroyCalls, 'logout() must destroy the session in exactly one place.');
        self::assertCount(1, $guarded, 'That call must be guarded by verify_csrf_token().');
    }
}
