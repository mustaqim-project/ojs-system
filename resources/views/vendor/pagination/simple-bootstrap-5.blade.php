@if ($paginator->hasPages())
    <nav class="d-flex align-items-center justify-content-between py-3 mt-4 border-top" style="border-color: var(--border, #e2e8f0) !important;" role="navigation" aria-label="Navigasi Halaman">
        <ul class="pagination pagination-sm m-0 gap-2 w-100 justify-content-between">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya <i class="bi bi-chevron-right ms-1"></i></a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Berikutnya <i class="bi bi-chevron-right ms-1"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
