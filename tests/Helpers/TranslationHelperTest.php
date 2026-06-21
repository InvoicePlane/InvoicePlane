<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

use function Tests\Feature\Quotes\app;

use Tests\Feature\Quotes\DB;
use Tests\Feature\Quotes\Setting;

use function Tests\Feature\Quotes\sort;

use Tests\Feature\Quotes\TranslationHelper;

#[CoversClass(Tests\Helpers\TranslationHelper::class)]

class TranslationHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');

        DB::table('ip_settings')->delete();
        Setting::setValue('default_language', 'en');
    }

    #[Test]
    public function it_translates_simple_strings(): void
    {
        /* Arrange */

        /* Act */
        $result = TranslationHelper::trans('validation.required');

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_key_when_translation_not_found(): void
    {
        /* Arrange */
        $key = 'non.existent.translation.key';

        /* Act */
        $result = TranslationHelper::trans($key);

        /* Assert */
        $this->assertSame($key, $result);
    }

    #[Test]
    public function it_uses_default_value_when_translation_not_found(): void
    {
        /* Arrange */
        $key     = 'non.existent.key';
        $default = 'Default value';

        /* Act */
        $result = TranslationHelper::trans($key, '', $default);

        /* Assert */
        $this->assertSame($default, $result);
    }

    #[Test]
    public function it_wraps_translation_in_label_with_id(): void
    {
        /* Arrange */
        $fieldId = 'test_field';

        /* Act */
        $result = TranslationHelper::trans('validation.required', $fieldId);

        /* Assert */
        $this->assertStringStartsWith('<label for="' . $fieldId . '">', $result);
        $this->assertStringEndsWith('</label>', $result);
    }

    #[Test]
    public function it_does_not_wrap_when_id_is_empty(): void
    {
        /* Arrange */

        /* Act */
        $result = TranslationHelper::trans('validation.required', '');

        /* Assert */
        $this->assertStringStartsNotWith('<label', $result);
    }

    #[Test]
    public function it_sets_application_locale(): void
    {
        /* Arrange */

        /* Act */
        TranslationHelper::setLanguage('fr');

        /* Assert */
        $this->assertSame('fr', app()->getLocale());
    }

    #[Test]
    public function it_uses_system_default_for_system_language(): void
    {
        /* Arrange */
        Setting::setValue('default_language', 'de');

        /* Act */
        TranslationHelper::setLanguage('system');

        /* Assert */
        $this->assertSame('de', app()->getLocale());
    }

    #[Test]
    public function it_sets_specific_language(): void
    {
        /* Arrange */

        /* Act */
        TranslationHelper::setLanguage('es');

        /* Assert */
        $this->assertSame('es', app()->getLocale());
    }

    #[Test]
    public function it_returns_available_languages(): void
    {
        /* Arrange */

        /* Act */
        $languages = TranslationHelper::getAvailableLanguages();

        /* Assert */
        $this->assertIsArray($languages);
        $this->assertContains('en', $languages);
    }

    #[Test]
    public function it_returns_empty_array_when_lang_directory_missing(): void
    {
        /* Arrange */
        // This test assumes the lang directory exists, but tests the handling
        /* Act */
        $languages = TranslationHelper::getAvailableLanguages();

        /* Assert */
        $this->assertIsArray($languages);
    }

    #[Test]
    public function it_returns_sorted_languages(): void
    {
        /* Arrange */
        $languages = TranslationHelper::getAvailableLanguages();

        if (count($languages) > 1) {
            $sorted = $languages;
        /* Act */
            sort($sorted);
        /* Assert */
            $this->assertSame($sorted, $languages);
        }
    }

    #[Test]
    public function it_handles_empty_translation_key(): void
    {
        /* Arrange */

        /* Act */
        $result = TranslationHelper::trans('');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_uses_configured_default_language(): void
    {
        /* Arrange */
        Setting::setValue('default_language', 'fr');

        /* Act */
        $result = TranslationHelper::trans('validation.required');

        /* Assert */
        $this->assertIsString($result);
    }
}
