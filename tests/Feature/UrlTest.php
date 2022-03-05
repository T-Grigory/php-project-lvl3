<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    private string $name = 'https://ya.ru';

    public function testMain()
    {
        $response = $this->get(route('main'));

        $response->assertStatus(200);
    }

    public function testIndex()
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testStore()
    {
        $response = $this->post(route('urls.store'), ['url' => ['name' => $this->name]]);

        $id = DB::table('urls')->where('name', $this->name)->value('id');
        $response->assertRedirect(route('urls.show', ['url' => $id]));

        $this->assertDatabaseHas('urls', ['name' => $this->name]);
    }

    public function testShow()
    {
        DB::table('urls')->insert(['name' => $this->name]);

        $id = DB::table('urls')->where('name', $this->name)->value('id');

        $response = $this->get(route('urls.show', ['url' => $id]));
        $response->assertOk();
    }

    public function testCheck()
    {
        DB::table('urls')->insert(['name' => $this->name]);

        $id = DB::table('urls')->where('name', $this->name)->value('id');

        $response = $this->post(route('urlChecks', ['id' => $id]));

        $response->assertRedirect(route('urls.show', ['url' => $id]));

        $this->assertDatabaseHas('url_checks', ['id' => $id]);
    }
}
