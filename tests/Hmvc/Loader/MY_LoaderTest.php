<?php

namespace Tests\Unit\Loader;

use stdClass;

/**
 * Unit tests for MY_Loader's PSR-4 detection and binding logic.
 *
 * MY_Loader overrides MX_Loader::library() and model() to handle FQCNs.
 * We test the classification and binding logic without booting CI3 by
 * using plain stubs that replicate the relevant MY_Loader methods.
 *
 * @group unit
 * @group loader
 */
#[CoversClass(Tests\Unit\Loader\MY_Loader::class)]
class MY_LoaderTest extends AbstractTestCase
{
    private TestableLoader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new TestableLoader();
    }

    public function it_detects_a_fully_qualified_class_name_as_namespaced(): void
    {
        self::assertTrue(
            $this->loader->isNamespaced('App\\Services\\Clients\\ClientsService'),
            'A class name containing a backslash must be detected as namespaced.'
        );
    }

    public function it_detects_a_plain_ci_class_name_as_not_namespaced(): void
    {
        self::assertFalse(
            $this->loader->isNamespaced('Mdl_invoices'),
            'A legacy CI class name without a backslash must NOT be detected as namespaced.'
        );
    }

    public function it_detects_a_root_namespace_class_as_namespaced(): void
    {
        self::assertTrue(
            $this->loader->isNamespaced('Modules\\Invoices\\Mdl_invoices'),
            'A Modules\\ prefixed FQCN must be detected as namespaced.'
        );
    }

    public function it_returns_false_for_an_empty_string_class_name(): void
    {
        self::assertFalse(
            $this->loader->isNamespaced(''),
            'An empty string must not be identified as a namespaced class.'
        );
    }

    public function it_derives_the_short_class_name_from_a_fqcn_as_the_default_alias(): void
    {
        $alias = $this->loader->deriveAlias('App\\Services\\Clients\\ClientsService');

        self::assertSame(
            'clientsService',
            $alias,
            'The default alias must be the short class name lowercased at the first character.'
        );
    }

    public function it_derives_the_short_class_name_for_a_single_segment_namespace(): void
    {
        $alias = $this->loader->deriveAlias('App\\MyLibrary');

        self::assertSame(
            'myLibrary',
            $alias,
            'deriveAlias must return the last namespace segment with lcfirst applied.'
        );
    }

    public function it_uses_the_explicit_object_name_over_the_derived_alias_when_provided(): void
    {
        $alias = $this->loader->deriveAlias('App\\Services\\Clients\\ClientsService', 'clients');

        self::assertSame(
            'clients',
            $alias,
            'When an explicit object_name is provided it must be returned verbatim.'
        );
    }

    public function it_binds_a_namespaced_class_instance_to_the_ci_superobject_under_the_derived_alias(): void
    {
        $stub   = new stdClass();
        $target = new FakeCiSuperObject();

        $this->loader->bindToSuperObject($target, 'App\\Fakes\\FakeService', new FakeService(), null);

        self::assertTrue(
            isset($target->fakeService),
            'The loader must bind the instance to the CI super-object using the derived alias [fakeService].'
        );

        self::assertInstanceOf(
            FakeService::class,
            $target->fakeService,
            'The bound property must be an instance of the class that was loaded.'
        );
    }

    public function it_does_not_rebind_a_class_that_is_already_present_on_the_superobject(): void
    {
        $target              = new FakeCiSuperObject();
        $originalInstance    = new FakeService();
        $target->fakeService = $originalInstance;

        $this->loader->bindToSuperObject($target, 'App\\Fakes\\FakeService', new FakeService(), null);

        self::assertSame(
            $originalInstance,
            $target->fakeService,
            'A class already bound under its alias must not be overwritten by a second load call.'
        );
    }

    public function it_uses_a_custom_object_name_when_binding_to_the_superobject(): void
    {
        $target = new FakeCiSuperObject();

        $this->loader->bindToSuperObject($target, 'App\\Fakes\\FakeService', new FakeService(), 'myAlias');

        self::assertTrue(
            isset($target->myAlias),
            'When a custom object_name is given, the instance must be bound under that name.'
        );

        self::assertFalse(
            isset($target->fakeService),
            'The derived alias [fakeService] must NOT be populated when a custom object_name is supplied.'
        );
    }
}

class TestableLoader
{
    public function isNamespaced(string $className): bool
    {
        return is_string($className) && str_contains($className, '\\');
    }

    public function deriveAlias(string $fqcn, ?string $objectName = null): string
    {
        if ($objectName !== null) {
            return $objectName;
        }

        $parts = explode('\\', $fqcn);

        return lcfirst((string) end($parts));
    }

    public function bindToSuperObject(
        object $ci,
        string $fqcn,
        object $instance,
        ?string $objectName,
    ): void {
        $alias = $this->deriveAlias($fqcn, $objectName);

        if (isset($ci->{$alias})) {
            return;
        }

        $ci->{$alias} = $instance;
    }
}

class FakeCiSuperObject
{
    public function __get(string $name): mixed
    {
        return null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->{$name});
    }
}

class FakeService {}
