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
        $data = $this->getData();
        $response->assertRedirect(route('urls.show', ['url' => $data[0]->id]));

        $this->assertDatabaseHas('urls', ['name' => $data[0]->name]);
    }

      public function testShow()
      {
          DB::insert('insert into urls (name) values (?)', [$this->name]);
          $data = $this->getData();

          $response = $this->get(route('urls.show', ['url' => $data[0]->id]));
          $response->assertOk();
      }

      private function getData(): array
      {
          return DB::select("select * from urls where name = :name", ['name' => $this->name]);
      }
}
