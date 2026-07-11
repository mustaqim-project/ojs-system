<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Services\OrcidService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrcidProfileController extends Controller
{
    /**
     * Sinkronisasikan profil User saat ini dari data publik ORCID.
     */
    public function sync(OrcidService $orcidService): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->orcid) {
            return back()->with('error', 'Anda belum menghubungkan akun ORCID. Harap hubungkan terlebih dahulu.');
        }

        // Ekstrak ID dari URL orcid
        $orcidId = preg_replace('#^https?://orcid\.org/#', '', $user->orcid);

        try {
            // Gunakan user token jika ada (agar bisa baca data limited jika user memberikan izin)
            $profile = $orcidService->parseSummaryProfile($orcidId, $user->orcid_token);

            if (empty($profile)) {
                return back()->with('error', 'Gagal memuat profil dari ORCID. Pastikan profil ORCID Anda diset Publik.');
            }

            // Update user profile
            $user->update([
                'name'        => $profile['name'] ?? $user->name,
                'affiliation' => $profile['affiliation'] ?? $user->affiliation,
                'bio'         => $profile['bio'] ?? $user->bio,
            ]);

            return back()->with('success', 'Profil Anda berhasil disinkronkan dengan data ORCID!');
        } catch (\Exception $e) {
            Log::error('ORCID Sync Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage());
        }
    }
}
