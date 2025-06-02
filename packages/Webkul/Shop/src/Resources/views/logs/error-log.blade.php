@extends('shop::layouts.master')

@section('page_title')
Error logs | Volanti Jet Catering
@stop

@section('content-wrapper')

<div class="container my-5 text-center">

    <h2 class="text-center mb-4">Error Logs Entries</h2>
    @if($logs->isNotEmpty())
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">Customer ID</th>
                <th scope="col" class="text-center">Message Type</th>
                <th scope="col" class="text-center">Message</th>
                <th scope="col" class="text-center">URL</th>
                <th scope="col" class="text-center">Created At</th>
                <th scope="col" class="text-center">Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <th scope="row" class="text-center">{{ $log->id }}</th>
                <td class="text-center" style="max-width: 200px;">{{ $log->customer_id ?? 'N/A' }}</td>
                <td class="text-center" style="max-width: 200px;">{{ $log->message_type ?? 'N/A' }}</td>
                <td class="text-center" style="max-width: 400px;max-height:200px;overflow:auto;">{{ $log->message ?? 'N/A' }}</td>
                <td class="text-center" style="max-width: 300px;max-height:200px;overflow:auto;">{{ $log->page_url ?? 'N/A' }}</td>
                <td class="text-center" style="max-width: 200px;">{{ $log->created_at }}</td>
                <td class="text-center" style="max-width: 200px;">{{ $log->updated_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation example">
            <ul class="pagination flex-wrap w-100" style="gap:5px;">
                {{-- Previous Button --}}
                <li class="page-item {{ $logs->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $logs->previousPageUrl() }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
    
                {{-- Page Numbers --}}
                @php
                    $currentPage = $logs->currentPage();
                    $start = max(1, $currentPage - 2); // Start from 2 pages before current page
                    $end = min($logs->lastPage(), $currentPage + 2); // End at 2 pages after current page
                @endphp
                
                @if($start > 1)
                    <li class="page-item"><a class="page-link" href="{{ $logs->url(1) }}">1</a></li>
                    @if($start > 2) <li class="page-item disabled"><span class="page-link">...</span></li> @endif
                @endif
                
                @for($i = $start; $i <= $end; $i++)
                    <li class="page-item {{ $logs->currentPage() == $i ? 'active' : '' }}" style="{{ $logs->currentPage() == $i ? 'color: white !important; border-bottom: none !important;' : '' }}">
                        <a class="page-link" href="{{ $logs->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                @if($end < $logs->lastPage())
                    @if($end < $logs->lastPage() - 1) <li class="page-item disabled"><span class="page-link">...</span></li> @endif
                    <li class="page-item"><a class="page-link" href="{{ $logs->url($logs->lastPage()) }}">{{ $logs->lastPage() }}</a></li>
                @endif
    
                {{-- Next Button --}}
                <li class="page-item {{ $logs->hasMorePages() ? '' : 'disabled' }}">
                    <a class="page-link" href="{{ $logs->nextPageUrl() }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    @else
    <div>No error logs found at the moment.</div>
    @endif
    
</div>
@stop
