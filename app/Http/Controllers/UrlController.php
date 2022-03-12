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

class UrlController extends Controller
{
    public function index(): Factory|View
    {
        $urlChecks = DB::table('url_checks')
                        ->distinct('url_id')
                        ->select('url_id', 'status_code', 'created_at as last_check_url_created_at')
                        ->orderBy('url_id')
                        ->orderByDesc('last_check_url_created_at');


        $urls = DB::table('urls')
                    ->select('id', 'name', 'last_check_url_created_at', 'status_code')
                    ->leftJoinSub($urlChecks, 'urls_checks', function ($join) {
                        $join->on('urls.id', '=', 'urls_checks.url_id');
                    })->orderBy('id')
                    ->paginate();

        return view('urls', ['urls' => $urls]);
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

        $name = $request->input('url.name');

        $id = DB::table('urls')->where('name', $name)->value('id');

        $message = !is_null($id) ? 'Страница уже существует' : 'Страница успешно добавлена';

        if (is_null($id)) {
            DB::table('urls')->insert(['name' => $name, 'created_at' => Carbon::now()]);
            $id = DB::table('urls')->where('name', $name)->value('id');
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

    public function check(int $id, Http $client = null): Redirector|RedirectResponse
    {
        $url = DB::table('urls')->find($id);
        if (empty($url)) {
            abort(404);
        }
        $response = $client ?? Http::get($url->name);
        $document = new Document($response->body());
        $title = optional($document->first('title'))->text();
        $h1 = optional($document->first('h1'))->text();
        $description = optional($document->first('meta[name=description]'))->attr('content');

        $statusCode = $response->status();

        DB::table('url_checks')->insert([
            'url_id' => $id,
            'status_code' => $statusCode,
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
            'created_at' => Carbon::now()
        ]);

        return redirect()->route('urls.show', ['url' => $id]);
    }
}
