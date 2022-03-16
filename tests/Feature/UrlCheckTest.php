<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use mysql_xdevapi\Exception;
use Tests\TestCase;

class UrlCheckTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $urls = [
            'https://mail.ru',
            'https://ru.hexlet.io',
            'https://www.google.ru',
        ];

        array_walk(
            $urls,
            fn($url) => DB::table('urls')->insert(['name' => $url, 'created_at' => Carbon::now()])
        );
    }

    /**
     * @dataProvider checkProvider
     * @param $id
     * @param $name
     * @return void
     */

    public function testCheck($id, $name): void
    {
        $body = file_get_contents(__DIR__ . "/fixtures/{$id}.html");

        Http::fake([
            $name => Http::response($body)
        ]);

        $response = $this->post(route('urlChecks', [
            'id' => $id
        ]));

        $response->assertRedirect(route('urls.show', ['url' => $id]));

        $this->assertDatabaseHas('url_checks', ['url_id' => $id]);
    }

    public function checkProvider(): array
    {
        return [
           ['id' => 1, 'name' => 'https://mail.ru'],
           ['id' => 2, 'name' => 'https://ru.hexlet.io'],
           ['id' => 3, 'name' => 'https://www.google.ru']
        ];
    }

    /**
     * @dataProvider failedCheckProvider
     * @param $id
     * @return void
     */

    public function testFailedCheck($id): void
    {
        $response = $this->post(route('urlChecks', [
            'id' => $id
        ]));

        $response->assertNotFound();
        $this->assertDatabaseMissing('url_checks', ['url_id' => $id]);
    }

    public function failedCheckProvider(): array
    {
        return [
            ['id' => 5],
            ['id' => 6],
            ['id' => 7]
        ];
    }

}
