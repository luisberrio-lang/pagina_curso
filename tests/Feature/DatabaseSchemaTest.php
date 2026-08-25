<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_courses_table_contains_the_previous_price_column(): void
    {
        $this->assertTrue(Schema::hasColumn('courses', 'price_previous'));
    }
}
