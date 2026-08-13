<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Invoice;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $journal  = Journal::where('slug', 'jikti')->first();
        $issue    = Issue::where('journal_id', $journal->id)->where('status', 'published')->first();
        $author   = User::where('email', 'author@ojs.id')->first();
        $author2  = User::where('email', 'author2@ojs.id')->first();
        $editor   = User::where('role', 'editor')->first();
        $reviewer = User::where('email', 'reviewer1@ojs.id')->first();

        // ===== ARTIKEL PUBLISHED =====
        $art1 = Article::updateOrCreate(
            ['slug' => 'implementasi-machine-learning-deteksi-spam-email'],
            [
                'journal_id'         => $journal->id,
                'issue_id'           => $issue->id,
                'author_id'          => $author->id,
                'assigned_editor_id' => $editor->id,
                'title'              => 'Implementasi Machine Learning untuk Deteksi Spam Email Menggunakan Naive Bayes',
                'slug'               => 'implementasi-machine-learning-deteksi-spam-email',
                'abstract'           => 'Penelitian ini mengkaji penerapan algoritma Naive Bayes dalam mendeteksi email spam. Dengan dataset 10.000 email, sistem mencapai akurasi 97.3%. Metode preprocessing meliputi tokenisasi, stemming, dan penghapusan stopword. Hasil menunjukkan bahwa Naive Bayes efektif untuk klasifikasi teks dalam deteksi spam.',
                'keywords'           => 'machine learning, naive bayes, spam detection, email classification',
                'language'           => 'id',
                'manuscript_file'    => 'manuscripts/sample-manuscript.pdf',
                'status'             => 'published',
                'pages_start'        => 1,
                'pages_end'          => 12,
                'doi'                => '10.12345/jikti.2024.1.1',
                'submitted_at'       => now()->subMonths(3),
                'accepted_at'        => now()->subMonths(2),
                'published_at'       => now()->subMonth(),
            ]
        );

        // Invoice untuk artikel published
        $invoice1 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-2024-' . $art1->id . '-ABC1'],
            [
                'journal_id'     => $journal->id,
                'submission_id'  => $art1->id,
                'invoice_number' => 'INV-2024-' . $art1->id . '-ABC1',
                'amount'         => 500000,
                'currency'       => 'IDR',
                'due_date'       => now()->addDays(14),
                'status'         => 'paid',
                'approved_by'    => User::where('role', 'admin')->first()->id,
            ]
        );

        // Payment untuk artikel published
        Payment::updateOrCreate(
            ['invoice_id' => $invoice1->id],
            [
                'invoice_id'    => $invoice1->id,
                'author_id'     => $author->id,
                'amount'        => 500000,
                'payment_method' => 'bank_transfer',
                'payment_date'  => now()->subMonth(),
                'proof_path'    => 'payments/sample-proof.jpg',
                'status'        => 'verified',
                'notes'         => 'Pembayaran APC untuk artikel ML Spam Detection',
                'verified_by'   => User::where('role', 'admin')->first()->id,
                'verified_at'   => now()->subMonth(),
            ]
        );

        // ===== ARTIKEL UNDER REVIEW =====
        $art2 = Article::updateOrCreate(
            ['slug' => 'analisis-performa-restful-api-laravel-vs-fastapi'],
            [
                'journal_id'         => $journal->id,
                'issue_id'           => null,
                'author_id'          => $author2->id,
                'assigned_editor_id' => $editor->id,
                'title'              => 'Analisis Performa RESTful API: Laravel vs FastAPI dalam Lingkungan Produksi',
                'slug'               => 'analisis-performa-restful-api-laravel-vs-fastapi',
                'abstract'           => 'Studi komparatif antara Laravel dan FastAPI dalam hal throughput, latensi, dan konsumsi memori pada beban tinggi. Pengujian dilakukan dengan Apache JMeter dengan skenario 1000 concurrent users. FastAPI menunjukkan latensi 40% lebih rendah, namun Laravel unggul dalam kemudahan pengembangan dan ekosistem.',
                'keywords'           => 'API, Laravel, FastAPI, performance, benchmark',
                'language'           => 'id',
                'manuscript_file'    => 'manuscripts/sample-manuscript.pdf',
                'status'             => 'under_review',
                'submitted_at'       => now()->subDays(10),
            ]
        );

        // Review untuk artikel under review
        Review::updateOrCreate(
            ['article_id' => $art2->id, 'reviewer_id' => $reviewer->id],
            [
                'article_id'  => $art2->id,
                'reviewer_id' => $reviewer->id,
                'status'      => 'in_progress',
                'due_date'    => now()->addDays(7),
            ]
        );

        // ===== ARTIKEL WAITING PAYMENT =====
        $art3 = Article::updateOrCreate(
            ['slug' => 'deep-learning-klasifikasi-penyakit-tanaman-padi'],
            [
                'journal_id'         => $journal->id,
                'issue_id'           => null,
                'author_id'          => $author->id,
                'assigned_editor_id' => $editor->id,
                'title'              => 'Deep Learning untuk Klasifikasi Penyakit Tanaman Padi Berbasis Citra',
                'slug'               => 'deep-learning-klasifikasi-penyakit-tanaman-padi',
                'abstract'           => 'Penelitian ini mengembangkan sistem klasifikasi penyakit tanaman padi menggunakan CNN (Convolutional Neural Network). Dataset terdiri dari 5000 gambar daun padi dengan 5 kategori penyakit. Model mencapai akurasi 94.7% pada data uji, lebih baik dari metode tradisional SVM (89.2%).',
                'keywords'           => 'deep learning, CNN, plant disease, rice, classification',
                'language'           => 'id',
                'manuscript_file'    => 'manuscripts/sample-manuscript.pdf',
                'status'             => 'waiting_payment',
                'submitted_at'       => now()->subMonths(2),
                'accepted_at'        => now()->subDays(5),
            ]
        );

        // Invoice untuk artikel waiting payment
        $invoice3 = Invoice::updateOrCreate(
            ['invoice_number' => 'INV-2024-' . $art3->id . '-XYZ9'],
            [
                'journal_id'     => $journal->id,
                'submission_id'  => $art3->id,
                'invoice_number' => 'INV-2024-' . $art3->id . '-XYZ9',
                'amount'         => 500000,
                'currency'       => 'IDR',
                'due_date'       => now()->addDays(14),
                'status'         => 'waiting_payment',
            ]
        );

        // ===== ARTIKEL SUBMITTED BARU =====
        Article::updateOrCreate(
            ['slug' => 'analisis-sentimen-twitter-pilkada-menggunakan-bert'],
            [
                'journal_id'      => $journal->id,
                'issue_id'        => null,
                'author_id'       => $author2->id,
                'title'           => 'Analisis Sentimen Twitter terhadap Pilkada Menggunakan Model BERT',
                'slug'            => 'analisis-sentimen-twitter-pilkada-menggunakan-bert',
                'abstract'        => 'Penelitian ini menganalisis sentimen publik di Twitter terkait Pilkada 2024 menggunakan model BERT yang telah di-fine-tune dengan corpus Bahasa Indonesia. Hasil menunjukkan 52% sentimen positif, 28% negatif, dan 20% netral.',
                'keywords'        => 'sentiment analysis, BERT, Twitter, NLP, Pilkada',
                'language'        => 'id',
                'manuscript_file' => 'manuscripts/sample-manuscript.pdf',
                'status'          => 'submitted',
                'submitted_at'    => now()->subDays(2),
            ]
        );

        $this->command->info('Articles seeded with payments and reviews!');
    }
}
