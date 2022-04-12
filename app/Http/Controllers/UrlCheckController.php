<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DiDom\Document;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlCheckController extends Controller
{
    public function store(int $id): Redirector|RedirectResponse
    {
        $url = DB::table('urls')->find($id);

        abort_unless($url, 404);

        try {
            $response = Http::get($url->name);

            $document = new Document($response->body());
            $title = optional($document->first('title'))->text();
            $h1 = optional($document->first('h1'))->text();
            $description = optional($document->first('meta[name=description]'))->attr('content');
            $statusCode = $response->status();
        } catch (HttpClientException | RequestException $exception) {
            flash($exception->getMessage())->error();
            return redirect()->route('urls.show', ['url' => $id]);
        }

        DB::table('url_checks')->insert([
            'url_id' => $id,
            'status_code' => $statusCode,
            'h1' => Str::limit($h1, 255, ''),
            'title' => Str::limit($title, 255, ''),
            'description' => Str::limit($description, 255, ''),
            'created_at' => Carbon::now()
        ]);

        flash('Страница успешно проверена')->info();
        return redirect()->route('urls.show', ['url' => $id]);
    }
}
