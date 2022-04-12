<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $parsedUrl = parse_url($name);
        $url = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";
        $normalizeUrl = Str::lower($url);

        $id = DB::table('urls')->where('name', $normalizeUrl)->value('id');

        $message = 'Страница уже существует';

        if (is_null($id)) {
            DB::table('urls')->insert(['name' => $normalizeUrl, 'created_at' => Carbon::now()]);
            $id = DB::table('urls')->where('name', $normalizeUrl)->value('id');
            $message = 'Страница успешно добавлена';
        }
        flash($message)->info();
        return redirect()->route('urls.show', ['url' => $id]);
    }

    public function show(int $id): View|Factory
    {
        $url = DB::table('urls')->find($id);
        $urlCheck = DB::table('url_checks')->where('url_id', $id)->orderByDesc('created_at')->get();

        abort_unless($url, 404);

        return view('url', ['url' => $url, 'urlCheck' => $urlCheck]);
    }
}
