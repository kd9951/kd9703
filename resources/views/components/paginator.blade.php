@php
$paginator = $paginate->getPaginator(10);
$base_query = Request::all();
$buildquery = function($page) use ($base_query) { return http_build_query(array_merge($base_query, ['page' => $page])); };
@endphp
<nav aria-label="Page navigation example">
<ul class="pagination justify-content-end">
    @if($paginate->prev_page)
        <li class="page-item"><a class="page-link" href="?{{$buildquery($paginate->prev_page)}}">◀</a></li>
    @else
        <li class="page-item disabled"><span class="page-link">◀</span></li>
    @endif

    @if($paginator->show_first)
        <li class="page-item"><a class="page-link" href="?{{$buildquery(1)}}">1</a></li>
    @endif

    @if($paginator->show_forward_dash)
        <li class="page-item disabled"><span class="page-link">……</span></li>
    @endif

    @foreach($paginator->pages_forward as $page)
        <li class="page-item"><a class="page-link" href="?{{$buildquery($page)}}">{{$page}}</a></li>
    @endforeach

        <li class="page-item active"><span class="page-link">{{$paginate->current_page}}</span></li>

    @foreach($paginator->pages_backward as $page)
        <li class="page-item"><a class="page-link" href="?{{$buildquery($page)}}">{{$page}}</a></li>
    @endforeach

    @if($paginator->show_backward_dash)
        <li class="page-item disabled"><span class="page-link">……</span></li>
    @endif

    @if($paginator->show_last)
        <li class="page-item"><a class="page-link" href="?{{$buildquery($paginate->last_page)}}">{{$paginate->last_page}}</a></li>
    @endif
    @if($paginate->next_page)
        <li class="page-item"><a class="page-link" href="?{{$buildquery($paginate->next_page)}}">▶</a></li>
    @else
        <li class="page-item disabled"><span class="page-link">▶</span></li>
    @endif
</ul>
</nav>
