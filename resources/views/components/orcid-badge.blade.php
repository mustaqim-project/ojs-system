@props(['orcid', 'size' => '16'])

@if($orcid)
    @php
        // Bersihkan orcid id dari URL jika input hanya berupa ID mentah
        $cleanId = preg_replace('#^https?://orcid\.org/#', '', $orcid);
        $fullUrl = "https://orcid.org/{$cleanId}";
    @endphp
    <a href="{{ $fullUrl }}" target="_blank" rel="noopener noreferrer" 
       class="d-inline-flex align-items-center gap-1 text-decoration-none text-success fw-medium hover-opacity"
       style="font-size: 13px;"
       title="Lihat profil ORCID terverifikasi">
        
        {{-- ORCID iD Green Icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 512 512" style="vertical-align: middle;">
            <path fill="#A6CE39" d="M512 256c0 141.4-114.6 256-256 256S0 397.4 0 256 114.6 0 256 0s256 114.6 256 256z"/>
            <path fill="#FFF" d="M178.8 286.2h-21.3v-78.4h21.3v78.4zm-10.7-90.2c-7.3 0-13.2-5.9-13.2-13.2s5.9-13.2 13.2-13.2 13.2 5.9 13.2 13.2-5.9 13.2-13.2 13.2zm171.1 90.2h-38.6c-4.9 0-9-2.2-11.7-5.9-2.2 3.7-6.8 5.9-11.7 5.9H236v-78.4h42.1c16.1 0 26.2 9.3 26.2 21.6 0 9.2-5.5 15.6-13.8 18.2 10.3 2.1 16.7 9.3 16.7 20.3v10.7c0 4.1.8 5.7 3.3 5.7h18.7v7.7zm-90.1-70.7v24.6h17.9c10.2 0 15.6-4.5 15.6-12.3s-5.4-12.3-15.6-12.3h-17.9zm0 32.4v30.6h19.5c10.4 0 15.7-4.8 15.7-15.3 0-10.4-5.3-15.3-15.7-15.3h-19.5z"/>
        </svg>

        {{-- ORCID iD URL Text (bisa disembunyikan atau diringkas dengan CSS jika di mobile) --}}
        <span class="d-none d-sm-inline" style="color: #64748b; font-size: 12px; font-family: monospace;">orcid.org/{{ $cleanId }}</span>
    </a>
@endif
