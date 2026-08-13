<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class InstitutionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = strtolower($request->get('q', ''));
        
        if (empty($search)) {
            return response()->json([]);
        }

        // Cache the global list for 7 days
        $universities = Cache::remember('global_universities_list', 60 * 60 * 24 * 7, function () {
            try {
                $response = Http::timeout(10)->get('https://raw.githubusercontent.com/Hipo/university-domains-list/master/world_universities_and_domains.json');
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                // Return null if request fails, so it doesn't cache empty value
            }
            return null;
        });

        // Fallback to local database if cache/fetch failed
        if (!$universities) {
            $dbUnis = Institution::where('name', 'like', "%{$search}%")
                ->orWhere('acronym', 'like', "%{$search}%")
                ->limit(15)
                ->get();
                
            $formatted = $dbUnis->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'acronym' => $item->acronym,
                    'city' => $item->city,
                ];
            });
            return response()->json($formatted);
        }

        // Search in-memory
        $matches = [];
        $count = 0;
        foreach ($universities as $uni) {
            $name = $uni['name'] ?? '';
            $country = $uni['country'] ?? '';
            $domain = isset($uni['domains'][0]) ? $uni['domains'][0] : '';
            
            // Check if matches query
            if (str_contains(strtolower($name), $search) || str_contains(strtolower($domain), $search)) {
                // Generate acronym from name
                $acronym = $this->generateAcronym($name);
                
                // Look up database to see if we already have it
                $dbUni = Institution::where('name', $name)->first();
                $id = $dbUni ? $dbUni->id : null;
                
                $matches[] = [
                    'id' => $id,
                    'name' => $name,
                    'acronym' => $acronym,
                    'city' => $country, // Show country/location context
                ];
                $count++;
                if ($count >= 15) {
                    break;
                }
            }
        }

        return response()->json($matches);
    }

    private function generateAcronym(string $name): ?string
    {
        $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', $name);
        $words = preg_split('/[\s\-]+/', $cleanName);
        $acronym = '';
        
        foreach ($words as $word) {
            $word = preg_replace('/[^a-zA-Z]/', '', $word);
            if (!empty($word)) {
                if (in_array(strtolower($word), ['of', 'in', 'and', 'dan', 'di', 'ke', 'the', 'university', 'universitas', 'institute', 'institut', 'technology', 'teknologi'])) {
                    continue;
                }
                $acronym .= strtoupper($word[0]);
            }
        }
        
        return !empty($acronym) ? $acronym : null;
    }
}
