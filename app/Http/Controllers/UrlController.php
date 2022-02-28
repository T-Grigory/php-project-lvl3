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
    public function index(): View|Factory
    {
        return view('urls', ['urls' => DB::table('urls')->paginate(15)]);
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

        $name = $request->input('url.name' );

        $url = $this->getData('name', $name);

        $message = !empty($url) ? 'Страница уже существует' : 'Страница успешно добавлена';

        if (empty($url)) {
            $this->saveUrl($name);
            $url = $this->getData('name', $name);
        }
        flash($message)->info();
        return redirect()->route('urls.show', ['url' => $url[0]->id]);
    }

    public  function saveUrl($name): void
    {
        DB::insert('insert into urls (name) values (?)', [$name]);
    }

    public function getData($name, $value): array
    {
        return DB::select("select * from urls where {$name} = :{$name}", [$name => $value]);
    }

    public function show($id): View|Factory
    {
        $url = $this->getData('id', $id);
        if (empty($url)) {
            abort(404);
        }
        return view('url', ['url' => $url[0]]);
    }
}
