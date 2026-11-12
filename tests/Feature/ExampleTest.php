<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_successfully(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_public_catalog_page_loads_successfully(): void
    {
        $this->get('/alat')->assertStatus(200);
    }
}