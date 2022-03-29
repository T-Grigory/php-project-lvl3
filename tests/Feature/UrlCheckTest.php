<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UrlCheckTest extends TestCase
{
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
                ->insert(['id' => $url['id'], 'name' => $url['name'], 'created_at' => Carbon::now()])
        );
    }

    /**
     * @dataProvider checkProvider
     * @param int $urlId
     * @param string $name
     * @param int $statusCode
     * @param mixed $h1
     * @param mixed $title
     * @param mixed $description
     * @return void
     */

    public function testCheck(
        int $urlId,
        string $name,
        int $statusCode,
        mixed $h1,
        mixed $title,
        mixed $description
    ): void {
        $body = file_get_contents(__DIR__ . "/fixtures/{$urlId}.html");

        Http::fake([
            $name => Http::response($body)
        ]);

        $response = $this->post(route('urlChecks.store', [
            'id' => $urlId
        ]));

        $response->assertRedirect(route('urls.show', ['url' => $urlId]));

        $this->assertDatabaseHas('url_checks', [
            'id' => 1,
            'url_id' => $urlId,
            'status_code' => $statusCode,
            'h1' => $h1,
            'title' => $title,
            'description' => $description
        ]);
    }

    public function checkProvider(): array
    {
        return [
            [
               'urlId' => 1,
               'name' => 'https://mail.ru',
               'statusCode' => 200,
               'h1' => null,
               'title' => 'Mail.ru: почта, поиск в интернете, новости, игры',
               'description' => 'Почта Mail.ru — крупнейшая бесплатная почта'
            ],
            [
                'url_id' => 2,
                'name' => 'https://ru.hexlet.io',
                'status_code' => 200,
                'h1' => 'Онлайн школа программирования, за выпускниками которой охотятся компании',
                'title' => 'Хекслет — больше чем школа программирования. Онлайн курсы, сообщество программистов',
                'description' => 'Живое онлайн сообщество программистов и разработчиков'
            ],
            [
                'url_id' => 3,
                'name' => 'https://www.google.ru',
                'status_code' => 200,
                'h1' => null,
                'title' => 'Google',
                'description' => null
            ]
        ];
    }

    /**
     * @dataProvider failedCheckProvider
     * @param int $id
     * @return void
     */

    public function testFailedCheck(int $id): void
    {
        $response = $this->post(route('urlChecks.store', [
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
