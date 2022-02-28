<nav class="navbar navbar-expand-md navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="{{route('main')}}">Анализатор страниц</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            @foreach(config('menu') as $item)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{route($item['route'])}}">{{$item['title']}}</a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
