@props([
    'links' => []
])
<nav {{ $attributes }}>
    <ul class="flex space-x-2 text-slate-500 items-center">
        @foreach($links as $label => $link)
            @if(!$loop->first) <li class="text-2xl">&rarr;</li> @endif
            <li>
                @if($link)
                    <a href="{{ $link }}" class="underline underline-offset-2 hover:text-indigo-700 hover:shadow-lg">{{ $label }}</a>
                @else
                    {{ $label }}
                @endif
            </li>
        @endforeach
    </ul>
</nav>
