<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $urls = [
            'https://mail.ru',
            'https://ru.hexlet.io',
            'https://www.google.ru'
        ];

        array_walk(
            $urls,
            fn($url) => DB::table('urls')->insert(['name' => $url, 'created_at' => Carbon::now()])
        );
    }

    public function testIndex(): void
    {
        $response = $this->get(route('urls.index'));

        $response->assertStatus(200);
    }

    /**
     * @dataProvider storeProvider
     * @param mixed name
     * @return void
     */

    public function testStore(string $name)
    {
        $response = $this->post(route('urls.store'), ['url' => ['name' => $name]]);

        $id = DB::table('urls')->where('name', $name)->value('id');
        $response->assertRedirect(route('urls.show', ['url' => $id]));

        $this->assertDatabaseHas('urls', ['name' => $name]);
    }

    public function storeProvider(): array
    {
        return [
            ['https://yandex.ru'],
            ['https://sibnet.ru'],
            ['https://www.google.ru'],
        ];
    }

    /**
     * @dataProvider failedStoreProvider
     * @param mixed name
     * @return void
     */

    public function testFailedStore(string $name)
    {
        $response = $this->post(route('urls.store'), ['url' => ['name' => $name]]);

        $response->assertRedirect(route('main'));
        $this->assertDatabaseMissing('urls', ['name' => $name]);
    }

    public function failedStoreProvider(): array
    {
        return [
            [''],
            ['sfdsfsdfs'],
            ['mail.ru']
        ];
    }

    /**
     * @dataProvider showProvider
     * @param mixed $id
     * @return void
     */

    public function testShow(int $id)
    {
        $response = $this->get(route('urls.show', ['url' => $id]));
        $response->assertOk();
    }

    public function showProvider(): array
    {
        return [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3]
        ];
    }

    /**
     * @dataProvider failedShowProvider
     * @param mixed $id
     * @return void
     */

    public function testFailedShow(int $id)
    {
        $response = $this->get(route('urls.show', ['url' => $id]));
        $response->assertNotFound();
    }

    public function failedShowProvider(): array
    {
        return [
            ['id' => 4],
            ['id' => 5],
            ['id' => 6]
        ];
    }
}
