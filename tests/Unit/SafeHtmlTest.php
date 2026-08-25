<?php

namespace Tests\Unit;

use App\Support\SafeHtml;
use PHPUnit\Framework\TestCase;

class SafeHtmlTest extends TestCase
{
    public function test_it_preserves_editor_formatting_and_removes_unsafe_markup(): void
    {
        $html = '<p onclick="alert(1)"><strong>Seguro</strong><script>alert(1)</script></p>';

        $sanitized = SafeHtml::sanitize($html);

        $this->assertSame('<p><strong>Seguro</strong>alert(1)</p>', $sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringNotContainsString('<script', $sanitized);
    }
}
