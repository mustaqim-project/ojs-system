{{-- Usage: <x-orcid-badge :orcid="$user->orcid"/> --}}
@props(['orcid' => null])
@if($orcid)
<a href="https://orcid.org/{{ $orcid }}" target="_blank" rel="noopener noreferrer"
   title="ORCID iD: {{ $orcid }}"
   style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border-radius:4px;border:1px solid #A6CE39;background:#F0FDF4;color:#2F855A;font-size:12px;font-weight:600;text-decoration:none;font-family:monospace;transition:all 0.15s;"
   onmouseover="this.style.background='#A6CE39';this.style.color='#fff';"
   onmouseout="this.style.background='#F0FDF4';this.style.color='#2F855A';">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512" style="fill:currentColor;flex-shrink:0;">
        <path d="M512 256c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0s256 114.6 256 256z"/>
        <path fill="#fff" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2-13.2zm171.1 90.2h-38.6c-4.9 0-9-2.2-11.7-5.9-2.2 3.7-6.8 5.9-11.7 5.9H236v-78.4h42.1c16.1 0 26.2 9.3 26.2 21.6 0 9.2-5.5 15.6-13.8 18.2 10.3 2.1 16.7 9.3 16.7 20.3v10.7c0 4.1.8 5.7 3.3 5.7h18.7v7.7zm-90.1-70.7v24.6h17.9c10.2 0 15.6-4.5 15.6-12.3s-5.4-12.3-15.6-12.3h-17.9zm0 32.4v30.6h19.5c10.4 0 15.7-4.8 15.7-15.3 0-10.4-5.3-15.3-15.7-15.3h-19.5z"/>
    </svg>
    {{ $orcid }}
    <i class="bi bi-box-arrow-up-right" style="font-size:10px;opacity:0.7;"></i>
</a>
@else
<span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border-radius:4px;border:1px solid var(--border);background:var(--bg-app);color:var(--text-muted);font-size:12px;">
    <i class="bi bi-dash-circle" style="font-size:10px;"></i>
    Not connected
</span>
@endif
