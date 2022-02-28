@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <h1 class="mt-5 mb-3">Сайты</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Последняя проверка</th>
                    <th>Код ответа</th>
                </tr>
                @foreach ($urls->items() as $url)
                    <tr>
                        <td>{{ $url->id }}</td>
                        <td><a href="{{route('urls.show', ['url' => $url->id])}}">{{$url->name}}</a></td>
                        <td> </td>
                        <td></td>
                    </tr>
                @endforeach
            </table>
            {{$urls->links()}}
        </div>
    </div>
@endsection
