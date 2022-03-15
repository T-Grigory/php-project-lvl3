<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageTest extends TestCase
{
    public function testMain(): void
    {
        $response = $this->get(route('main'));

        $response->assertStatus(200);
    }
}
