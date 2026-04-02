@props([
    'paginator' => null,
    'view' => 'pagination::simple-bootstrap-4',
])

@if($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    {{ $paginator->links($view) }}
@endif

