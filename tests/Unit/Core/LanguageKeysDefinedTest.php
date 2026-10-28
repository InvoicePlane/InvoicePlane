<?php

namespace Tests\Unit\Core;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A translation key that no language file defines is printed as the bare key. trans('access_denied') was used in 14 places
 * (the guards added for the user-client and settings advisories), trans('invalid_request') in 5 (the POST and CSRF guards)
 * and trans('invalid_value_for_field') in 2 (settings validation), and none of them had an English string, so the refusal
 * pages said "access_denied". Every literal key passed to trans(), _trans() or lang() must exist in the English language
 * files; English is the fallback of every other language.
 *
 * KNOWN_UNDEFINED lists keys that were already undefined and are not part of a refusal path; it may only shrink. The two
 * form_validation_* keys come from CodeIgniter's own language files.
 */
#[Group('compatibility')]
final class LanguageKeysDefinedTest extends TestCase
{
    private const KNOWN_UNDEFINED = [
        'item_sku', 'original_product', 'annual', 'invoice_number', 'diagnosis',
        'form_validation_valid_email', 'form_validation_min_length',
    ];

    #[Test]
    public function it_defines_an_english_string_for_every_literal_translation_key(): void
    {
        /* Arrange */
        $root    = dirname(__DIR__, 3) . '/application';
        $defined = [];
        foreach (glob($root . '/language/english/*.php') ?: [] as $file) {
            preg_match_all("/'([^']+)'\\s*=>/", (string) file_get_contents($file), $keys);
            $defined = array_merge($defined, $keys[1]);
        }

        $parser  = (new ParserFactory())->createForHostVersion();
        $finder  = new NodeFinder();
        $missing = [];

        /* Act */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            $path = $file->getPathname();
            if ($file->getExtension() !== 'php' || str_contains($path, '/language/') || str_contains($path, '/third_party/')) {
                continue;
            }

            foreach ($finder->find($parser->parse((string) file_get_contents($path)) ?? [], static fn (Node $n): bool => $n instanceof FuncCall && $n->name instanceof Node\Name && in_array(strtolower($n->name->toString()), ['trans', '_trans', 'lang'], true) && ($n->args[0]->value ?? null) instanceof String_) as $call) {
                $key = $call->args[0]->value->value;
                if ( ! in_array($key, $defined, true) && ! in_array($key, self::KNOWN_UNDEFINED, true)) {
                    $missing[$key][] = substr($path, strlen($root) + 1) . ':' . $call->getStartLine();
                }
            }
        }

        /* Assert */
        ksort($missing);
        $report = [];
        foreach ($missing as $key => $places) {
            $report[] = $key . ' (' . count($places) . 'x, e.g. ' . $places[0] . ')';
        }
        self::assertSame([], $report, "Translation keys without an English string:\n" . implode("\n", $report));
    }
}
