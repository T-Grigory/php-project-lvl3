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
            ['id' => 1, 'name' => 'https://mail.ru'],
            ['id' => 2, 'name' => 'https://ru.hexlet.io'],
            ['id' => 3, 'name' => 'https://www.google.ru']
        ];

        array_walk(
            $urls,
            fn($url) => DB::table('urls')
                ->insert([
                    'id' => $url['id'],
                    'name' => $url['name'],
                    'created_at' => Carbon::create('2022', '03', '29', '11', '31', '44')
                ])
        );
    }

    public function testIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertStatus(200);
    }

    /**
     * @dataProvider storeProvider
     * @param string $name
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
            ['name' => 'https://yandex.ru'],
            ['name' => 'https://sibnet.ru'],
            ['name' => 'https://www.google.ru'],
        ];
    }

    /**
     * @dataProvider failedStoreProvider
     * @param string $name
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
            ['name' => ''],
            ['name' => 'sfdsfsdfs'],
            ['name' => 'mail.ru']
        ];
    }

    /**
     * @dataProvider showProvider
     * @param int $id
     * @param string $name
     * @return void
     */

    public function testShow(int $id, string $name)
    {
        $response = $this->get(route('urls.show', ['url' => $id]));
        $response->assertOk();
        $date = Carbon::create('2022', '03', '29', '11', '31', '44');
        $response->assertSee(["<td>{$id}</td>","<td>{$name}</td>", "<td>{$date}</td>"], false);
    }

    public function showProvider(): array
    {
        return [
            ['id' => 1, 'name' => 'https://mail.ru'],
            ['id' => 2, 'name' => 'https://ru.hexlet.io'],
            ['id' => 3, 'name' => 'https://www.google.ru']
        ];
    }

    /**
     * @dataProvider failedShowProvider
     * @param int $id
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
