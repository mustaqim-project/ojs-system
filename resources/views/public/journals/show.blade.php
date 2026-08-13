@extends('layouts.app')
@section('content')

    {{-- Hero Header --}}
    <div style="background:var(--bg-surface);border-bottom:1px solid var(--border);padding:60px 0 40px;">
        <div class="container" style="max-width:1100px;">

            <div style="font-size:13px;color:var(--text-muted);margin-bottom:20px;font-weight:500;">
                <a href="{{ route('public.home') }}" style="color:var(--text-muted);text-decoration:none;">Beranda</a> <span
                    style="margin:0 6px;">›</span>
                <a href="{{ route('public.journals.index') }}"
                    style="color:var(--text-muted);text-decoration:none;">Jurnal</a> <span style="margin:0 6px;">›</span>
                <span style="color:var(--text-main);">{{ $journal->abbreviation ?? $journal->title }}</span>
            </div>

            <div style="display:flex;align-items:flex-start;gap:36px;flex-wrap:wrap;">
                {{-- Cover Image Card --}}
                <div
                    style="flex-shrink:0;width:220px;background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:16px;box-shadow:var(--shadow-md);">
                    @if ($journal->cover_image)
                        <img src="{{ asset($journal->cover_image) }}" alt="{{ $journal->title }}"
                            style="width:100%;height:280px;border-radius:var(--radius-md);object-fit:cover;border:1px solid var(--border);box-shadow:0 4px 12px rgba(0,0,0,0.05);display:block;margin-bottom:16px;">
                    @else
                        <div
                            style="width:100%;height:280px;border-radius:var(--radius-md);background:linear-gradient(135deg, var(--primary-light), rgba(37,99,235,0.05));color:var(--primary);display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:48px;box-shadow:0 4px 12px rgba(37,99,235,0.15);margin-bottom:16px;gap:8px;">
                            <i class="bi bi-journal-bookmark-fill"></i>
                            <span
                                style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;opacity:0.8;">No
                                Cover</span>
                        </div>
                    @endif

                    {{-- Submission Button --}}
                    <a href="{{ route('author.articles.create', ['journal_id' => $journal->id]) }}"
                        class="btn btn-primary w-100"
                        style="font-weight:700;font-size:13px;border-radius:8px;padding:10px 12px;box-shadow:0 2px 8px rgba(37,99,235,0.25);">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Kirim Naskah
                    </a>
                </div>

                {{-- Journal Info Column --}}
                <div style="flex:1;min-width:300px;">
                    <div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
                        @if ($journal->abbreviation)
                            <span
                                style="font-size:12px;font-family:monospace;background:var(--bg-app);color:var(--text-muted);padding:4px 10px;border-radius:6px;font-weight:700;border:1px solid var(--border);">{{ $journal->abbreviation }}</span>
                        @endif
                        <span
                            style="font-size:12px;background:var(--bg-app);color:var(--text-main);border:1px solid var(--border);padding:4px 12px;border-radius:20px;font-weight:600;text-transform:capitalize;">
                            <i class="bi bi-arrow-repeat me-1" style="color:var(--primary);"></i> {{ $journal->frequency }}
                            Edisi
                        </span>
                        <span
                            style="font-size:12px;background:var(--success-bg);color:var(--success);padding:4px 12px;border-radius:20px;font-weight:700;">
                            <i class="bi bi-check-circle-fill" style="font-size:10px;margin-right:4px;"></i> Aktif
                        </span>
                    </div>

                    <h1
                        style="font-size:32px;font-weight:800;color:var(--text-main);letter-spacing:-0.03em;margin-bottom:16px;line-height:1.2;">
                        {{ $journal->title }}</h1>

                    @if ($journal->description)
                        <div class="journal-description"
                            style="font-size:15px;color:var(--text-muted);margin-bottom:24px;line-height:1.75;max-width:800px;">
                            {!! $journal->description !!}
                        </div>
                    @endif

                    <div
                        style="display:flex;gap:20px;flex-wrap:wrap;font-size:13px;color:var(--text-muted);background:var(--bg-app);padding:16px 20px;border-radius:var(--radius-md);border:1px solid var(--border);display:inline-flex;">
                        @if ($journal->issn_print)
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i class="bi bi-upc-scan" style="color:var(--primary);"></i>
                                <strong style="color:var(--text-main);font-weight:600;">ISSN (Cetak):</strong> <span
                                    style="font-family:monospace;">{{ $journal->issn_print }}</span>
                            </div>
                        @endif
                        @if ($journal->issn_online)
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i class="bi bi-laptop" style="color:var(--primary);"></i>
                                <strong style="color:var(--text-main);font-weight:600;">ISSN (Online):</strong> <span
                                    style="font-family:monospace;">{{ $journal->issn_online }}</span>
                            </div>
                        @endif
                        @if ($journal->publisher)
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i class="bi bi-building" style="color:var(--primary);"></i>
                                <strong style="color:var(--text-main);font-weight:600;">Penerbit:</strong>
                                {{ $journal->publisher }}
                            </div>
                        @endif
                        @if ($journal->editor)
                            <div style="display:flex;align-items:center;gap:6px;">
                                <i class="bi bi-person-badge" style="color:var(--primary);"></i>
                                <strong style="color:var(--text-main);font-weight:600;">Ketua Redaksi:</strong>
                                {{ $journal->editor->name }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Issues & Articles --}}
    <div class="container" style="max-width:1100px;padding:48px 24px;">

        <h3 style="font-size:20px;font-weight:800;color:var(--text-main);margin-bottom:24px;letter-spacing:-0.02em;">Edisi
            yang Diterbitkan</h3>

        @forelse($journal->issues as $issue)
            <div
                style="background:var(--bg-surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:32px;box-shadow:var(--shadow-sm);">

                {{-- Issue Header --}}
                <div
                    style="padding:20px 24px;background:var(--bg-app);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                    <div>
                        <h2 style="font-size:16px;font-weight:700;color:var(--text-main);margin:0 0 4px 0;">
                            {{ $issue->title }}</h2>
                        <div style="font-size:13px;color:var(--text-muted);font-weight:500;">{{ $issue->display_title }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:16px;">
                        @if ($issue->published_date)
                            <span style="font-size:13px;color:var(--text-muted);font-weight:500;">
                                <i
                                    class="bi bi-calendar3 me-1"></i>{{ $issue->published_date->locale('id')->translatedFormat('d M Y') }}
                            </span>
                        @endif
                        <span
                            style="font-size:12px;background:var(--success-bg);color:var(--success);padding:4px 12px;border-radius:20px;font-weight:700;">
                            Diterbitkan
                        </span>
                    </div>
                </div>

                {{-- Articles in Issue --}}
                <div>
                    @forelse($issue->publishedArticles as $article)
                        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:20px;transition:background 0.2s;"
                            onmouseover="this.style.background='var(--bg-app)'"
                            onmouseout="this.style.background='transparent';">

                            <div style="flex:1;min-width:0;">
                                <a href="{{ route('public.articles.show', $article->slug) }}"
                                    style="font-size:15px;font-weight:700;color:var(--primary);text-decoration:none;line-height:1.4;display:block;margin-bottom:6px;">
                                    {{ $article->title }}
                                </a>
                                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                                    <span style="font-size:13px;color:var(--text-main);font-weight:500;">
                                        <i class="bi bi-person me-1"
                                            style="color:var(--text-muted);"></i>{{ $article->author->name }}
                                    </span>
                                    @if ($article->pages_start)
                                        <span style="font-size:13px;color:var(--text-muted);">
                                            <i class="bi bi-file-text me-1"></i>Halaman
                                            {{ $article->pages_start }}–{{ $article->pages_end }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if ($article->doi)
                                <div style="flex-shrink:0;text-align:right;">
                                    <span
                                        style="display:inline-block;font-size:11px;font-family:monospace;color:var(--text-muted);background:var(--bg-app);border:1px solid var(--border);padding:4px 8px;border-radius:4px;">
                                        DOI: {{ $article->doi }}
                                    </span>
                                </div>
                            @endif

                        </div>
                    @empty
                        <div style="padding:40px 24px;text-align:center;">
                            <i class="bi bi-file-earmark-text"
                                style="font-size:32px;color:var(--border);display:block;margin-bottom:12px;"></i>
                            <div style="color:var(--text-muted);font-size:14px;">Belum ada artikel yang diterbitkan pada
                                edisi ini.</div>
                        </div>
                    @endforelse
                </div>

            </div>
        @empty
            <div style="padding:60px 0;">
                <x-ui.empty-state icon="bi-collection" title="Belum ada edisi yang diterbitkan"
                    description="Jurnal ini belum menerbitkan edisi apa pun." />
            </div>
        @endforelse

    </div>
@endsection
