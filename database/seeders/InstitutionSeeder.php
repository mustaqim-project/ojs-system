<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $institutions = [];

        try {
            $this->command->info('Fetching universities list from global dataset...');
            
            // Fetch list from Hipo global university dataset (contains all indonesian universities)
            $response = Http::timeout(10)->get('https://raw.githubusercontent.com/Hipo/university-domains-list/master/world_universities_and_domains.json');

            if ($response->successful()) {
                $allUniversities = $response->json();
                
                // Filter universities in Indonesia
                $indonesianUnis = array_filter($allUniversities, function ($uni) {
                    return isset($uni['country']) && strtolower($uni['country']) === 'indonesia';
                });

                $this->command->info('Found ' . count($indonesianUnis) . ' universities in Indonesia.');

                foreach ($indonesianUnis as $uni) {
                    $name = $uni['name'];
                    $website = isset($uni['web_pages'][0]) ? $uni['web_pages'][0] : null;
                    $acronym = $this->generateAcronym($name);

                    $institutions[] = [
                        'name'         => $name,
                        'acronym'      => $acronym,
                        'country_code' => 'ID',
                        'city'         => $uni['state-province'] ?? null,
                        'website'      => $website,
                        'ror_id'       => null,
                    ];
                }
            } else {
                $this->command->warn('Failed to fetch from GitHub API. Using fallback list...');
                $institutions = $this->getFallbackInstitutions();
            }
        } catch (\Exception $e) {
            $this->command->warn('Network error: ' . $e->getMessage() . '. Using fallback list...');
            $institutions = $this->getFallbackInstitutions();
        }

        $this->command->info('Seeding ' . count($institutions) . ' institutions into database...');

        foreach ($institutions as $data) {
            Institution::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $this->command->info('Institutions seeded successfully!');
    }

    /**
     * Helper to generate acronym from university name.
     */
    private function generateAcronym(string $name): ?string
    {
        $cleanName = preg_replace('/\s*\(.*?\)\s*/', '', $name);
        $words = preg_split('/[\s\-]+/', $cleanName);
        $acronym = '';
        
        foreach ($words as $word) {
            $word = preg_replace('/[^a-zA-Z]/', '', $word);
            if (!empty($word)) {
                if (in_array(strtolower($word), ['of', 'in', 'and', 'dan', 'di', 'ke', 'the'])) {
                    continue;
                }
                $acronym .= strtoupper($word[0]);
            }
        }
        
        return !empty($acronym) ? $acronym : null;
    }

    /**
     * Fallback Indonesian universities list.
     */
    private function getFallbackInstitutions(): array
    {
        return [
            [
                'name' => 'Universitas Indonesia',
                'acronym' => 'UI',
                'country_code' => 'ID',
                'city' => 'Depok',
                'website' => 'https://ui.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Gadjah Mada',
                'acronym' => 'UGM',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://ugm.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Teknologi Bandung',
                'acronym' => 'ITB',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://itb.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Pertanian Bogor',
                'acronym' => 'IPB',
                'country_code' => 'ID',
                'city' => 'Bogor',
                'website' => 'https://ipb.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Airlangga',
                'acronym' => 'UNAIR',
                'country_code' => 'ID',
                'city' => 'Surabaya',
                'website' => 'https://unair.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Padjadjaran',
                'acronym' => 'UNPAD',
                'country_code' => 'ID',
                'city' => 'Sumedang',
                'website' => 'https://unpad.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Diponegoro',
                'acronym' => 'UNDIP',
                'country_code' => 'ID',
                'city' => 'Semarang',
                'website' => 'https://undip.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Brawijaya',
                'acronym' => 'UB',
                'country_code' => 'ID',
                'city' => 'Malang',
                'website' => 'https://ub.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Hasanuddin',
                'acronym' => 'UNHAS',
                'country_code' => 'ID',
                'city' => 'Makassar',
                'website' => 'https://unhas.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Teknologi Sepuluh Nopember',
                'acronym' => 'ITS',
                'country_code' => 'ID',
                'city' => 'Surabaya',
                'website' => 'https://its.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sebelas Maret',
                'acronym' => 'UNS',
                'country_code' => 'ID',
                'city' => 'Surakarta',
                'website' => 'https://uns.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Andalas',
                'acronym' => 'UNAND',
                'country_code' => 'ID',
                'city' => 'Padang',
                'website' => 'https://unand.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sumatera Utara',
                'acronym' => 'USU',
                'country_code' => 'ID',
                'city' => 'Medan',
                'website' => 'https://usu.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Syiah Kuala',
                'acronym' => 'USK',
                'country_code' => 'ID',
                'city' => 'Banda Aceh',
                'website' => 'https://usk.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Yogyakarta',
                'acronym' => 'UNY',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://uny.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Malang',
                'acronym' => 'UM',
                'country_code' => 'ID',
                'city' => 'Malang',
                'website' => 'https://um.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Pendidikan Indonesia',
                'acronym' => 'UPI',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://upi.edu',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Semarang',
                'acronym' => 'UNNES',
                'country_code' => 'ID',
                'city' => 'Semarang',
                'website' => 'https://unnes.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Surabaya',
                'acronym' => 'UNESA',
                'country_code' => 'ID',
                'city' => 'Surabaya',
                'website' => 'https://unesa.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Jakarta',
                'acronym' => 'UNJ',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://unj.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Medan',
                'acronym' => 'UNIMED',
                'country_code' => 'ID',
                'city' => 'Medan',
                'website' => 'https://unimed.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Padang',
                'acronym' => 'UNP',
                'country_code' => 'ID',
                'city' => 'Padang',
                'website' => 'https://unp.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Makassar',
                'acronym' => 'UNM',
                'country_code' => 'ID',
                'city' => 'Makassar',
                'website' => 'https://unm.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Manado',
                'acronym' => 'UNIMA',
                'country_code' => 'ID',
                'city' => 'Manado',
                'website' => 'https://unima.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Negeri Gorontalo',
                'acronym' => 'UNG',
                'country_code' => 'ID',
                'city' => 'Gorontalo',
                'website' => 'https://ung.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Udayana',
                'acronym' => 'UNUD',
                'country_code' => 'ID',
                'city' => 'Badung',
                'website' => 'https://unud.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sriwijaya',
                'acronym' => 'UNSRI',
                'country_code' => 'ID',
                'city' => 'Palembang',
                'website' => 'https://unsri.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Lampung',
                'acronym' => 'UNILA',
                'country_code' => 'ID',
                'city' => 'Bandar Lampung',
                'website' => 'https://unila.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Riau',
                'acronym' => 'UNRI',
                'country_code' => 'ID',
                'city' => 'Pekanbaru',
                'website' => 'https://unri.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Syiah Kuala',
                'acronym' => 'USK',
                'country_code' => 'ID',
                'city' => 'Banda Aceh',
                'website' => 'https://usk.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Jambi',
                'acronym' => 'UNJA',
                'country_code' => 'ID',
                'city' => 'Jambi',
                'website' => 'https://unja.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Bengkulu',
                'acronym' => 'UNIB',
                'country_code' => 'ID',
                'city' => 'Bengkulu',
                'website' => 'https://unib.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Tanjungpura',
                'acronym' => 'UNTAN',
                'country_code' => 'ID',
                'city' => 'Pontianak',
                'website' => 'https://untan.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Lambung Mangkurat',
                'acronym' => 'ULM',
                'country_code' => 'ID',
                'city' => 'Banjarmasin',
                'website' => 'https://ulm.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Mulawarman',
                'acronym' => 'UNMUL',
                'country_code' => 'ID',
                'city' => 'Samarinda',
                'website' => 'https://unmul.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sam Ratulangi',
                'acronym' => 'UNSRAT',
                'country_code' => 'ID',
                'city' => 'Manado',
                'website' => 'https://unsrat.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Tadulako',
                'acronym' => 'UNTAD',
                'country_code' => 'ID',
                'city' => 'Palu',
                'website' => 'https://untad.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Halu Oleo',
                'acronym' => 'UHO',
                'country_code' => 'ID',
                'city' => 'Kendari',
                'website' => 'https://uho.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Mataram',
                'acronym' => 'UNRAM',
                'country_code' => 'ID',
                'city' => 'Mataram',
                'website' => 'https://unram.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Nusa Cendana',
                'acronym' => 'UNDANA',
                'country_code' => 'ID',
                'city' => 'Kupang',
                'website' => 'https://undana.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Pattimura',
                'acronym' => 'UNPATTI',
                'country_code' => 'ID',
                'city' => 'Ambon',
                'website' => 'https://unpatti.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Cenderawasih',
                'acronym' => 'UNCEN',
                'country_code' => 'ID',
                'city' => 'Jayapura',
                'website' => 'https://uncen.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Jenderal Soedirman',
                'acronym' => 'UNSOED',
                'country_code' => 'ID',
                'city' => 'Purwokerto',
                'website' => 'https://unsoed.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Tidar',
                'acronym' => 'UNTIDAR',
                'country_code' => 'ID',
                'city' => 'Magelang',
                'website' => 'https://untidar.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sultan Ageng Tirtayasa',
                'acronym' => 'UNTIRTA',
                'country_code' => 'ID',
                'city' => 'Serang',
                'website' => 'https://untirta.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Trunojoyo Madura',
                'acronym' => 'UTM',
                'country_code' => 'ID',
                'city' => 'Bangkalan',
                'website' => 'https://trunojoyo.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Bangka Belitung',
                'acronym' => 'UBB',
                'country_code' => 'ID',
                'city' => 'Pangkalpinang',
                'website' => 'https://ubb.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Borneo Tarakan',
                'acronym' => 'UBT',
                'country_code' => 'ID',
                'city' => 'Tarakan',
                'website' => 'https://borneo.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Musamus Merauke',
                'acronym' => 'UNMUS',
                'country_code' => 'ID',
                'city' => 'Merauke',
                'website' => 'https://unmus.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Teuku Umar',
                'acronym' => 'UTU',
                'country_code' => 'ID',
                'city' => 'Meulaboh',
                'website' => 'https://utu.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Siliwangi',
                'acronym' => 'UNSIL',
                'country_code' => 'ID',
                'city' => 'Tasikmalaya',
                'website' => 'https://unsil.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Singaperbangsa Karawang',
                'acronym' => 'UNSIKA',
                'country_code' => 'ID',
                'city' => 'Karawang',
                'website' => 'https://unsika.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Khairun',
                'acronym' => 'UNKHAIR',
                'country_code' => 'ID',
                'city' => 'Ternate',
                'website' => 'https://unkhair.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Papua',
                'acronym' => 'UNIPA',
                'country_code' => 'ID',
                'city' => 'Manokwari',
                'website' => 'https://unipa.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Jember',
                'acronym' => 'UNEJ',
                'country_code' => 'ID',
                'city' => 'Jember',
                'website' => 'https://unej.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Malikussaleh',
                'acronym' => 'UNIMAL',
                'country_code' => 'ID',
                'city' => 'Lhokseumawe',
                'website' => 'https://unimal.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Maritim Raja Ali Haji',
                'acronym' => 'UMRAH',
                'country_code' => 'ID',
                'city' => 'Tanjungpinang',
                'website' => 'https://umrah.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UPN Veteran Jakarta',
                'acronym' => 'UPNVJ',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://upnvj.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UPN Veteran Yogyakarta',
                'acronym' => 'UPNYK',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://upnyk.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UPN Veteran Jawa Timur',
                'acronym' => 'UPNVJT',
                'country_code' => 'ID',
                'city' => 'Surabaya',
                'website' => 'https://upnjatim.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Syarif Hidayatullah',
                'acronym' => 'UINJKT',
                'country_code' => 'ID',
                'city' => 'Tangerang Selatan',
                'website' => 'https://uinjkt.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Sunan Kalijaga',
                'acronym' => 'UINSUKA',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://uin-suka.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Sunan Gunung Djati',
                'acronym' => 'UINSGD',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://uinsgd.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Maulana Malik Ibrahim',
                'acronym' => 'UINMALANG',
                'country_code' => 'ID',
                'city' => 'Malang',
                'website' => 'https://uin-malang.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Alauddin',
                'acronym' => 'UINALAUDDIN',
                'country_code' => 'ID',
                'city' => 'Makassar',
                'website' => 'https://uin-alauddin.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Raden Fatah',
                'acronym' => 'UINRADENFATAH',
                'country_code' => 'ID',
                'city' => 'Palembang',
                'website' => 'https://radenfatah.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'UIN Walisongo',
                'acronym' => 'UINWALISONGO',
                'country_code' => 'ID',
                'city' => 'Semarang',
                'website' => 'https://walisongo.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Seni Indonesia Yogyakarta',
                'acronym' => 'ISIYK',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://isi.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Seni Indonesia Surakarta',
                'acronym' => 'ISISOLO',
                'country_code' => 'ID',
                'city' => 'Surakarta',
                'website' => 'https://isi-ska.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Seni Indonesia Denpasar',
                'acronym' => 'ISIDPS',
                'country_code' => 'ID',
                'city' => 'Denpasar',
                'website' => 'https://isi-dps.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Teknologi Sumatera',
                'acronym' => 'ITERA',
                'country_code' => 'ID',
                'city' => 'Lampung Selatan',
                'website' => 'https://itera.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Institut Teknologi Kalimantan',
                'acronym' => 'ITK',
                'country_code' => 'ID',
                'city' => 'Balikpapan',
                'website' => 'https://itk.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Jakarta',
                'acronym' => 'PNJ',
                'country_code' => 'ID',
                'city' => 'Depok',
                'website' => 'https://pnj.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Bandung',
                'acronym' => 'POLBAN',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://polban.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Semarang',
                'acronym' => 'POLINES',
                'country_code' => 'ID',
                'city' => 'Semarang',
                'website' => 'https://polines.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Elektronika Negeri Surabaya',
                'acronym' => 'PENS',
                'country_code' => 'ID',
                'city' => 'Surabaya',
                'website' => 'https://pens.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Malang',
                'acronym' => 'POLINEMA',
                'country_code' => 'ID',
                'city' => 'Malang',
                'website' => 'https://polinema.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Medan',
                'acronym' => 'POLMED',
                'country_code' => 'ID',
                'city' => 'Medan',
                'website' => 'https://polmed.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Sriwijaya',
                'acronym' => 'POLSRI',
                'country_code' => 'ID',
                'city' => 'Palembang',
                'website' => 'https://polsri.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Samarinda',
                'acronym' => 'POLNES',
                'country_code' => 'ID',
                'city' => 'Samarinda',
                'website' => 'https://polnes.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Ujung Pandang',
                'acronym' => 'PNUP',
                'country_code' => 'ID',
                'city' => 'Makassar',
                'website' => 'https://poliupg.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Politeknik Negeri Bali',
                'acronym' => 'PNB',
                'country_code' => 'ID',
                'city' => 'Badung',
                'website' => 'https://pnb.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Telkom',
                'acronym' => 'TEL-U',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://telkomuniversity.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Bina Nusantara',
                'acronym' => 'BINUS',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://binus.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Islam Indonesia',
                'acronym' => 'UII',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://uii.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Muhammadiyah Yogyakarta',
                'acronym' => 'UMY',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://umy.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Muhammadiyah Malang',
                'acronym' => 'UMM',
                'country_code' => 'ID',
                'city' => 'Malang',
                'website' => 'https://umm.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Muhammadiyah Surakarta',
                'acronym' => 'UMS',
                'country_code' => 'ID',
                'city' => 'Surakarta',
                'website' => 'https://ums.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Kristen Satya Wacana',
                'acronym' => 'UKSW',
                'country_code' => 'ID',
                'city' => 'Salatiga',
                'website' => 'https://uksw.edu',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Katolik Parahyangan',
                'acronym' => 'UNPAR',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://unpar.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Sanata Dharma',
                'acronym' => 'USD',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://usd.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Trisakti',
                'acronym' => 'USAkti',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://trisakti.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Tarumanagara',
                'acronym' => 'UNTAR',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://untar.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Pelita Hapan',
                'acronym' => 'UPH',
                'country_code' => 'ID',
                'city' => 'Tangerang',
                'website' => 'https://uph.edu',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Mercu Buana',
                'acronym' => 'UMB',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://mercubuana.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Atma Jaya Yogyakarta',
                'acronym' => 'UAJY',
                'country_code' => 'ID',
                'city' => 'Yogyakarta',
                'website' => 'https://uajy.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Katolik Indonesia Atma Jaya',
                'acronym' => 'UAJ',
                'country_code' => 'ID',
                'city' => 'Jakarta',
                'website' => 'https://atmajaya.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Gunadarma',
                'acronym' => 'UG',
                'country_code' => 'ID',
                'city' => 'Depok',
                'website' => 'https://gunadarma.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Islam Bandung',
                'acronym' => 'UNISBA',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://unisba.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Pasundan',
                'acronym' => 'UNPAS',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://unpas.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Islam Sultan Agung',
                'acronym' => 'UNISSULA',
                'country_code' => 'ID',
                'city' => 'Semarang',
                'website' => 'https://unissula.ac.id',
                'ror_id' => null,
            ],
            [
                'name' => 'Universitas Komputer Indonesia',
                'acronym' => 'UNIKOM',
                'country_code' => 'ID',
                'city' => 'Bandung',
                'website' => 'https://unikom.ac.id',
                'ror_id' => null,
            ],
        ];
    }
}
