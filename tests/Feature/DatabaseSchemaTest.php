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

    public function test_order_tables_contain_the_transactional_snapshot_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('orders', [
            'order_number', 'public_token', 'checkout_token_hash', 'subtotal', 'total',
            'currency', 'status', 'payment_status',
        ]));
        $this->assertTrue(Schema::hasColumns('order_items', [
            'order_id', 'course_id', 'course_title', 'course_slug', 'unit_price',
            'currency', 'quantity', 'line_total',
        ]));
    }
}
