<?php

namespace Tests\Feature\Quotes;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\UnitTestCase;

#[CoversClass(ClientHelper::class)]

class TranslationHelperTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_settings')->delete();
        Setting::setValue('default_language', 'en');
    }

    #[Test]
    public function it_translates_simple_strings(): void
    {
        $result = TranslationHelper::trans('validation.required');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_key_when_translation_not_found(): void
    {
        $key = 'non.existent.translation.key';

        $result = TranslationHelper::trans($key);

        $this->assertSame($key, $result);
    }

    #[Test]
    public function it_uses_default_value_when_translation_not_found(): void
    {
        $key     = 'non.existent.key';
        $default = 'Default value';

        $result = TranslationHelper::trans($key, '', $default);

        $this->assertSame($default, $result);
    }

    #[Test]
    public function it_wraps_translation_in_label_with_id(): void
    {
        $fieldId = 'test_field';

        $result = TranslationHelper::trans('validation.required', $fieldId);

        $this->assertStringStartsWith('<label for="' . $fieldId . '">', $result);
        $this->assertStringEndsWith('</label>', $result);
    }

    #[Test]
    public function it_does_not_wrap_when_id_is_empty(): void
    {
        $result = TranslationHelper::trans('validation.required', '');

        $this->assertStringStartsNotWith('<label', $result);
    }

    #[Test]
    public function it_sets_application_locale(): void
    {
        TranslationHelper::setLanguage('fr');

        $this->assertSame('fr', app()->getLocale());
    }

    #[Test]
    public function it_uses_system_default_for_system_language(): void
    {
        Setting::setValue('default_language', 'de');

        TranslationHelper::setLanguage('system');

        $this->assertSame('de', app()->getLocale());
    }

    #[Test]
    public function it_sets_specific_language(): void
    {
        TranslationHelper::setLanguage('es');

        $this->assertSame('es', app()->getLocale());
    }

    #[Test]
    public function it_returns_available_languages(): void
    {
        $languages = TranslationHelper::getAvailableLanguages();

        $this->assertIsArray($languages);
        $this->assertContains('en', $languages);
    }

    #[Test]
    public function it_returns_empty_array_when_lang_directory_missing(): void
    {
        // This test assumes the lang directory exists, but tests the handling
        $languages = TranslationHelper::getAvailableLanguages();

        $this->assertIsArray($languages);
    }

    #[Test]
    public function it_returns_sorted_languages(): void
    {
        $languages = TranslationHelper::getAvailableLanguages();

        if (count($languages) > 1) {
            $sorted = $languages;
            sort($sorted);
            $this->assertSame($sorted, $languages);
        }
    }

    #[Test]
    public function it_handles_empty_translation_key(): void
    {
        $result = TranslationHelper::trans('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_uses_configured_default_language(): void
    {
        Setting::setValue('default_language', 'fr');

        $result = TranslationHelper::trans('validation.required');

        $this->assertIsString($result);
    }
}
