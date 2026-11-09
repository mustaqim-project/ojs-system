@if ($paginator->hasPages())
    <nav class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-3 mt-4 border-top" style="border-color: var(--border, #e2e8f0) !important;" role="navigation" aria-label="Navigasi Halaman">
        <div class="text-muted" style="font-size: 13px;">
            Menampilkan <span class="fw-semibold text-dark">{{ $paginator->firstItem() }}</span> – <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span> dari <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> hasil
        </div>

        <ul class="pagination pagination-sm m-0 gap-1 flex-wrap justify-content-center">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                    <span class="page-link"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">
                        <i class="bi bi-chevron-left me-1"></i> Sebelumnya
                    </a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Tiga Titik Pemisah --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Tautan Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">
                        Berikutnya <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                    <span class="page-link">Berikutnya <i class="bi bi-chevron-right ms-1"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@elseif ($paginator->total() > 0)
    <div class="d-flex align-items-center justify-content-between py-3 mt-4 border-top text-muted" style="border-color: var(--border, #e2e8f0) !important; font-size: 13px;">
        <div>Menampilkan semua <span class="fw-semibold text-dark">{{ $paginator->total() }}</span> hasil</div>
    </div>
@endif
