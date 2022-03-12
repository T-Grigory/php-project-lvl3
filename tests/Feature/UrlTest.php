<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
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
        DB::table('urls')->insert(['name' => $this->name, 'created_at' => Carbon::now()]);

        $id = DB::table('urls')->where('name', $this->name)->value('id');

        $response = $this->get(route('urls.show', ['url' => $id]));
        $response->assertOk();
    }

    public function testCheck(): void
    {
        DB::table('urls')->insert(['name' => $this->name, 'created_at' => Carbon::now()]);

        $id = DB::table('urls')->where('name', $this->name)->value('id');

        Http::fake([
            $this->name => Http::response('<title>Яндекс</title>')
        ]);

        $response = $this->post(route('urlChecks', [
            'id' => $id
        ]));

        $response->assertRedirect(route('urls.show', ['url' => $id]));

        $this->assertDatabaseHas('url_checks', ['id' => $id]);
    }
}
