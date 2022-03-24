<?php

namespace App\Http\Controllers;

use DiDom\Document;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index(): Factory|View
    {
        $urls = DB::table('urls')->paginate(15);

        $lastChecks = DB::table('url_checks')
            ->distinct('url_id')
            ->orderBy('url_id')
            ->latest()
            ->get()
            ->keyBy('url_id');

        return view('urls', compact('urls', 'lastChecks'));
    }

    public function store(Request $request): Redirector|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'url.name' => 'required|max:255|url'
        ]);

        if ($validator->fails()) {
            flash('Некорректный URL')->error();
            return redirect()->route('main')->withErrors($validator->errors());
        }

        $name = $validator->validated()['url']['name'];

        $id = DB::table('urls')->where('name', $name)->value('id');

        $message = 'Страница уже существует';

        if (is_null($id)) {
            DB::table('urls')->insert(['name' => $name, 'created_at' => Carbon::now()]);
            $id = DB::table('urls')->where('name', $name)->value('id');
            $message = 'Страница успешно добавлена';
        }
        flash($message)->info();
        return redirect()->route('urls.show', ['url' => $id]);
    }

    public function show(int $id): View|Factory
    {
        $url = DB::table('urls')->find($id);
        $urlCheck = DB::table('url_checks')->where('url_id', $id)->orderByDesc('created_at')->get();

        if (empty($url)) {
            abort(404);
        }
        return view('url', ['url' => $url, 'urlCheck' => $urlCheck]);
    }

    public function check(int $id): Redirector|RedirectResponse
    {
        $url = DB::table('urls')->find($id);

        if (empty($url)) {
            abort_unless($url, 404);
        }

        try {
            $response = Http::get($url->name);

            $document = new Document($response->body());
            $title = optional($document->first('title'))->text();
            $h1 = optional($document->first('h1'))->text();
            $description = optional($document->first('meta[name=description]'))->attr('content');
            $statusCode = $response->status();
        } catch (\Exception $exception) {
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
