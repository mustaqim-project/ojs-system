<?php
$translations = [
    'Home' => 'Beranda',
    'About' => 'Tentang',
    'About Journal' => 'Tentang Jurnal',
    'Focus & Scope' => 'Fokus & Ruang Lingkup',
    'Journal Policies' => 'Kebijakan Jurnal',
    'Editorial' => 'Editorial',
    'Editorial Team' => 'Tim Editorial',
    'Reviewer Board' => 'Dewan Peninjau',
    'Browse' => 'Jelajahi',
    'Current Issue' => 'Edisi Terkini',
    'Archive' => 'Arsip',
    'All Articles' => 'Semua Artikel',
    'All Journals' => 'Semua Jurnal',
    'Guidelines' => 'Panduan',
    'Author Guidelines' => 'Panduan Penulis',
    'Publication Ethics' => 'Etika Publikasi',
    'Peer Review Process' => 'Proses Peninjauan',
    'Information' => 'Informasi',
    'Announcements' => 'Pengumuman',
    'Call for Papers' => 'Panggilan Makalah',
    'Indexing' => 'Pengindeksan',
    'Contact Us' => 'Hubungi Kami',
];

foreach ($translations as $en => $id) {
    App\Models\Navigation::where('title', $en)->update(['title' => $id]);
}
echo "Translations updated successfully.\n";
