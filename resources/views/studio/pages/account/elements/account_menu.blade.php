 @php
     $currentRoute = Request::url();
 @endphp
 @if ($list_route)
     @foreach ($list_route as $item)
         @php
             $url = $item['url'] ?? "";
             $name = $item['name'] ?? "";
         @endphp
         <li class="{{ $currentRoute == $url ? 'active' : '' }}"><a href="{{ $url }}">{{$name}} </a>
         </li>
     @endforeach
 @endif
