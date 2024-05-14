<li class="nav-item">
    <a class="nav-link @if(Route::currentRouteName() == 'shell') active @endif" href="{{ route('home') }}">Shell</a>
</li>
<li class="nav-item">
    <a class="nav-link @if(Route::currentRouteName() == 'connection') active @endif" href="{{ route('connection') }}">Connections</a>
</li>
<li class="nav-item">
    <a class="nav-link @if(Route::currentRouteName() == 'script') active @endif" href="{{ route('script') }}">Scripts</a>
</li>
<li class="nav-item">
    <a class="nav-link @if(Route::currentRouteName() == 'modifier') active @endif" href="{{ route('modifier') }}">Modifiers</a>
</li>
