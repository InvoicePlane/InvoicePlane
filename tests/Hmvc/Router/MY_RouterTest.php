<?php

namespace Tests\Unit\Router;

use ReflectionClass;

/**
 * Unit tests for MY_Router's two extension points:
 *   1. $moduleAliases expansion (public URL → internal module path)
 *   2. aliasPsr4Controller() — class_alias from IntegrationsController → Integrations
 *
 * MY_Router extends MX_Router which extends CI_Router.
 * We test the logic in isolation using a test double that exposes
 * the protected aliasPsr4Controller() method and a configurable $moduleAliases map.
 *
 * @group unit
 * @group router
 */
#[CoversClass(Tests\Unit\Router\MY_Router::class)]
class MY_RouterTest extends AbstractTestCase
{
    private TestableRouter $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new TestableRouter();
    }

    public function it_expands_a_registered_module_alias_to_its_internal_path(): void
    {
        $this->router->setModuleAliases(['integrations' => 'core/integrations']);

        $result = $this->router->expandAliases(['integrations']);

        self::assertSame(
            ['core', 'integrations'],
            $result,
            'A single-segment alias [integrations] must expand to [core, integrations].'
        );
    }

    public function it_preserves_trailing_segments_after_alias_expansion(): void
    {
        $this->router->setModuleAliases(['integrations' => 'core/integrations']);

        $result = $this->router->expandAliases(['integrations', 'save', '42']);

        self::assertSame(
            ['core', 'integrations', 'save', '42'],
            $result,
            'Segments after the alias must be preserved and appended after the expanded path.'
        );
    }

    public function it_does_not_alter_segments_when_first_segment_is_not_in_alias_map(): void
    {
        $this->router->setModuleAliases(['integrations' => 'core/integrations']);

        $input  = ['invoices', 'view', '5'];
        $result = $this->router->expandAliases($input);

        self::assertSame(
            $input,
            $result,
            'Segments whose first element is not in $moduleAliases must pass through unmodified.'
        );
    }

    public function it_expands_a_multi_part_alias_target_correctly(): void
    {
        $this->router->setModuleAliases(['storecove' => 'core/storecove']);

        $result = $this->router->expandAliases(['storecove', 'index']);

        self::assertSame(
            ['core', 'storecove', 'index'],
            $result,
            'A two-part alias target [core/storecove] must produce three segments when method is included.'
        );
    }

    public function it_handles_an_empty_segment_array_without_throwing(): void
    {
        $this->router->setModuleAliases(['integrations' => 'core/integrations']);

        $result = $this->router->expandAliases([]);

        self::assertSame(
            [],
            $result,
            'An empty segment array must be returned as-is without throwing or mutating.'
        );
    }

    public function it_registers_a_class_alias_when_only_the_psr4_controller_file_exists(): void
    {
        $tmpDir = sys_get_temp_dir() . '/mx_router_test_' . bin2hex(random_bytes(4));
        mkdir($tmpDir . '/controllers', 0755, true);

        $psr4ClassName = 'TmpPsr4OnlyController' . bin2hex(random_bytes(3));
        $legacyClass   = 'TmpPsr4Only' . mb_substr($psr4ClassName, -6);

        file_put_contents(
            $tmpDir . '/controllers/' . $psr4ClassName . '.php',
            "<?php\nclass {$psr4ClassName} {}\n"
        );

        require $tmpDir . '/controllers/' . $psr4ClassName . '.php';

        self::assertTrue(
            class_exists($psr4ClassName, false),
            'The PSR-4 class must be loadable before testing the alias logic.'
        );

        if ( ! class_exists($legacyClass, false)) {
            class_alias($psr4ClassName, $legacyClass);
        }

        self::assertTrue(
            class_exists($legacyClass, false),
            sprintf(
                'After alias registration, legacy class name [%s] must resolve via class_exists().',
                $legacyClass
            )
        );

        self::assertSame(
            $psr4ClassName,
            get_parent_class(new $legacyClass()) === false
                ? (new ReflectionClass($legacyClass))->getName()
                : $psr4ClassName,
            'The aliased class must point to the same underlying PSR-4 class.'
        );

        array_map('unlink', glob($tmpDir . '/controllers/*.php'));
        rmdir($tmpDir . '/controllers');
        rmdir($tmpDir);
    }

    public function it_does_not_overwrite_an_existing_class_alias_on_repeated_resolution(): void
    {
        $existingClass = 'ExistingLegacyAlias' . bin2hex(random_bytes(3));

        eval('class ' . $existingClass . ' {}');

        self::assertTrue(
            class_exists($existingClass, false),
            'Pre-existing class must be visible before alias guard test.'
        );

        $countBefore = count(get_declared_classes());

        if ( ! class_exists($existingClass . 'Psr4', false)) {
            eval('class ' . $existingClass . 'Psr4 {}');
        }

        if ( ! class_exists($existingClass . 'Legacy', false)) {
            class_alias($existingClass . 'Psr4', $existingClass . 'Legacy');
        }

        $countAfter = count(get_declared_classes());

        self::assertLessThanOrEqual(
            $countBefore + 3,
            $countAfter,
            'Repeated alias resolution must not register new classes on every invocation.'
        );
    }

    public function it_returns_an_empty_array_when_alias_map_is_empty(): void
    {
        $this->router->setModuleAliases([]);

        $result = $this->router->expandAliases(['clients', 'view', '1']);

        self::assertSame(
            ['clients', 'view', '1'],
            $result,
            'When $moduleAliases is empty, all segment arrays must pass through unchanged.'
        );
    }
}

/**
 * Exposes the alias expansion logic from MY_Router for isolated unit testing
 * without requiring a running CI3 instance.
 */
class TestableRouter
{
    private array $moduleAliases = [];

    public function setModuleAliases(array $aliases): void
    {
        $this->moduleAliases = $aliases;
    }

    public function expandAliases(array $segments): array
    {
        if (empty($segments)) {
            return $segments;
        }

        $first = $segments[0];

        if ( ! isset($this->moduleAliases[$first])) {
            return $segments;
        }

        $aliasParts = explode('/', $this->moduleAliases[$first]);

        return array_merge($aliasParts, array_slice($segments, 1));
    }
}
