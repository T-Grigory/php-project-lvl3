@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайт: {{$url->name}}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <td>ID</td>
                    <td>{{$url->id}}</td>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td>{{$url->name}}</td>
                </tr>
                <tr>
                    <td>Дата создания</td>
                    <td>{{$url->created_at}}</td>
                </tr>
            </table>
        </div>
        <h2 class="mt-5 mb-3">Проверки</h2>
        <form method="post" action="{{route('urlChecks.store', ['id' => $url->id])}}">
            @csrf
            <input type="submit" class="btn btn-primary" value="Запустить проверку">
        </form>
        <table class="table table-bordered table-hover text-nowrap">
            <tr>
                <th>ID</th>
                <th>Код ответа</th>
                <th>h1</th>
                <th>title</th>
                <th>description</th>
                <th>Дата создания</th>
            </tr>
            @foreach ($urlCheck as $url)
                <tr>
                    <td>{{$url->id}}</td>
                    <td>{{$url->status_code}}</td>
                    <td>{{Str::limit($url->h1, 10)}}</td>
                    <td>{{Str::limit($url->title, 30)}}</td>
                    <td>{{Str::limit($url->description, 30)}}</td>
                    <td>{{$url->created_at}}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection

