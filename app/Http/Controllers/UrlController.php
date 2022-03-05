<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UrlController extends Controller
{

    public function index(): Factory|View
    {
        $urlChecks = DB::table('url_checks')
                        ->select('url_id', 'status_code', DB::raw('MAX(created_at) as last_check_url_created_at'))
                        ->groupBy('url_id', 'status_code')
                        ->orderBy('url_id');


        $urls = DB::table('urls')
                    ->select('id', 'name', 'last_check_url_created_at', 'status_code')
                    ->leftJoinSub($urlChecks, 'urls_checks', function ($join) {
                        $join->on('urls.id', '=', 'urls_checks.url_id');
                    })->orderBy('id')
                    ->paginate(15);

        return view('urls', ['urls' => $urls]);
    }

    public function store(Request $request): Redirector|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'url.name' => 'required|max:255|url'
        ]);

        if ($validator->fails()) {
            flash('Некорректный URL')->error();
            return redirect(route('main'));
        }

        $name = $request->input('url.name');

        $id = DB::table('urls')->where('name', $name)->value('id');

        $message = !is_null($id) ? 'Страница уже существует' : 'Страница успешно добавлена';

        if (is_null($id)) {
            DB::table('urls')->insert(['name' => $name]);
            $id = DB::table('urls')->where('name', $name)->value('id');
        }
        flash($message)->info();
        return redirect()->route('urls.show', ['url' => $id]);
    }

    public function show($id): View|Factory
    {
        $url = DB::table('urls')->find($id);
        $urlCheck = DB::table('url_checks')->where('url_id', $id)->orderByDesc('created_at')->get();

        if (empty($url)) {
            abort(404);
        }
        return view('url', ['url' => $url, 'urlCheck' => $urlCheck]);
    }

    public function check($id): Redirector|RedirectResponse
    {
        DB::table('url_checks')->insert(['url_id' => $id]);
        return redirect()->route('urls.show', ['url' => $id]);
    }
}
