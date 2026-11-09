<?php

namespace Tests\Feature;

use App\Events\SubmissionStageChanged;
use App\Models\Article;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ArticleAcceptedNotification;
use App\Notifications\ArticlePublishedNotification;
use App\Notifications\ArticleRejectedNotification;
use App\Notifications\ArticleSubmittedNotification;
use App\Notifications\DeskRejectedNotification;
use App\Notifications\PreReviewRevisionNotification;
use App\Notifications\ProductionStageNotification;
use App\Notifications\ReviewAcknowledgmentNotification;
use App\Notifications\ReviewAssignedNotification;
use App\Notifications\ReviewResponseNotification;
use App\Notifications\ReviewSubmittedNotification;
use App\Notifications\RevisionRequiredNotification;
use App\Notifications\RevisionSubmittedNotification;
use App\Notifications\SubmissionAcknowledgmentNotification;
use App\Services\ArticleService;
use Database\Seeders\NavigationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EditorialEmailWorkflowNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            NavigationSeeder::class,
        ]);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create([
            'role'      => $role,
            'is_active' => true,
        ]);
        $user->syncRoles([$role]);

        return $user;
    }

    private function makeArticle(array $attributes = []): Article
    {
        $title = $attributes['title'] ?? 'Judul Riset Ilmiah ' . Str::random(5);
        return Article::create(array_merge([
            'title'           => $title,
            'slug'            => Str::slug($title) . '-' . Str::random(5),
            'abstract'        => 'Abstrak pengujian lengkap tentang manajemen alur penerbitan jurnal akademik berstandar internasional.',
            'keywords'        => ['ojs', 'jurnal', 'review'],
            'language'        => 'id',
            'tracking_code'   => 'ART-2026-' . strtoupper(Str::random(6)),
            'manuscript_file' => 'uploads/manuscripts/test.pdf',
            'status'          => 'submitted',
            'current_stage'   => 'submitted',
        ], $attributes));
    }

    public function test_submission_notifies_editors_and_acknowledges_author(): void
    {
        Notification::fake();
        Storage::fake('public');

        $author = $this->makeUser('author');
        $editor = $this->makeUser('editor');
        $journal = Journal::factory()->create(['is_active' => true]);

        $payload = [
            'journal_id'      => $journal->id,
            'title'           => 'Analisis Sistem Notifikasi Terpadu OJS',
            'abstract'        => 'Abstrak pengujian alur kerja notifikasi dari tahapan submit, peer review, hingga penerbitan publikasi naskah ilmiah secara komprehensif.',
            'keywords'        => 'notifikasi, workflow, ojs',
            'language'        => 'id',
            'manuscript_file' => UploadedFile::fake()->create('manuscript.pdf', 300, 'application/pdf'),
        ];

        $response = $this->actingAs($author)->post(route('author.articles.store'), $payload);
        $response->assertSessionHasNoErrors();

        $article = Article::where('title', 'Analisis Sistem Notifikasi Terpadu OJS')->firstOrFail();

        // 1. Editor should receive ArticleSubmittedNotification
        Notification::assertSentTo($editor, ArticleSubmittedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });

        // 2. Author should receive SubmissionAcknowledgmentNotification
        Notification::assertSentTo($author, SubmissionAcknowledgmentNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_assigning_reviewer_sends_notification(): void
    {
        Notification::fake();

        $editor = $this->makeUser('editor');
        $reviewer = $this->makeUser('reviewer');
        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Uji Reviewer Assignment',
            'status'        => 'submitted',
        ]);

        $response = $this->actingAs($editor)->post(route('editor.articles.assign-reviewer', $article), [
            'reviewer_id' => $reviewer->id,
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($reviewer, ReviewAssignedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_reviewer_accept_and_decline_notifies_editors(): void
    {
        Notification::fake();

        $editor = $this->makeUser('editor');
        $reviewer = $this->makeUser('reviewer');
        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id'         => $journal->id,
            'author_id'          => $author->id,
            'assigned_editor_id' => $editor->id,
            'title'              => 'Uji Respon Reviewer',
            'status'             => 'under_review',
        ]);

        $review = Review::create([
            'article_id'  => $article->id,
            'reviewer_id' => $reviewer->id,
            'status'      => 'pending',
            'due_date'    => now()->addDays(14),
        ]);

        // Reviewer accepts
        $response = $this->actingAs($reviewer)->post(route('reviewer.reviews.accept', $review));
        $response->assertRedirect();

        Notification::assertSentTo($editor, ReviewResponseNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id && $notification->responseType === 'accepted';
        });
    }

    public function test_reviewer_submitting_review_notifies_editor_and_acknowledges_reviewer(): void
    {
        Notification::fake();

        $editor = $this->makeUser('editor');
        $reviewer = $this->makeUser('reviewer');
        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id'         => $journal->id,
            'author_id'          => $author->id,
            'assigned_editor_id' => $editor->id,
            'title'              => 'Uji Penyerahan Review',
            'status'             => 'under_review',
        ]);

        $review = Review::create([
            'article_id'  => $article->id,
            'reviewer_id' => $reviewer->id,
            'status'      => 'accepted',
            'due_date'    => now()->addDays(14),
        ]);

        $payload = [
            'recommendation'     => 'minor',
            'comments_to_author' => 'Naskah sangat baik dan berkualitas, mohon tambahkan referensi mutakhir pada bagian pendahuluan artikel ini.',
            'comments_to_editor' => 'Rekomendasi kuat untuk diterima setelah revisi minor.',
            'originality_score'  => 5,
            'methodology_score'  => 4,
            'relevance_score'    => 5,
            'writing_score'      => 4,
        ];

        $response = $this->actingAs($reviewer)->post(route('reviewer.reviews.submit', $review), $payload);
        $response->assertRedirect();

        // Editor gets ReviewSubmittedNotification
        Notification::assertSentTo($editor, ReviewSubmittedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });

        // Reviewer gets ReviewAcknowledgmentNotification
        Notification::assertSentTo($reviewer, ReviewAcknowledgmentNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_editorial_decisions_notify_author(): void
    {
        Notification::fake();

        $editor = $this->makeUser('editor');
        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        // 1. Decision = Revision Required
        $articleRevision = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Uji Keputusan Revisi',
            'status'        => 'under_review',
        ]);

        $this->actingAs($editor)->post(route('editor.articles.decision', $articleRevision), [
            'decision'    => 'revision',
            'editor_note' => 'Perbaiki bagian pembahasan sesuai masukan reviewer.',
        ]);

        Notification::assertSentTo($author, RevisionRequiredNotification::class, function ($notification) use ($articleRevision) {
            return $notification->article->id === $articleRevision->id;
        });

        // 2. Decision = Accept
        $articleAccept = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Uji Keputusan Accept',
            'status'        => 'under_review',
        ]);

        $this->actingAs($editor)->post(route('editor.articles.decision', $articleAccept), [
            'decision'    => 'accept',
            'editor_note' => 'Selamat, naskah Anda diterima untuk publikasi.',
        ]);

        Notification::assertSentTo($author, ArticleAcceptedNotification::class, function ($notification) use ($articleAccept) {
            return $notification->article->id === $articleAccept->id;
        });

        // 3. Decision = Reject
        $articleReject = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Uji Keputusan Reject',
            'status'        => 'under_review',
        ]);

        $this->actingAs($editor)->post(route('editor.articles.decision', $articleReject), [
            'decision'    => 'reject',
            'editor_note' => 'Mohon maaf, naskah belum memenuhi kriteria penerbitan.',
        ]);

        Notification::assertSentTo($author, ArticleRejectedNotification::class, function ($notification) use ($articleReject) {
            return $notification->article->id === $articleReject->id;
        });
    }

    public function test_author_uploading_revision_notifies_editors(): void
    {
        Notification::fake();
        Storage::fake('public');

        $editor = $this->makeUser('editor');
        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id'         => $journal->id,
            'author_id'          => $author->id,
            'assigned_editor_id' => $editor->id,
            'title'              => 'Uji Upload Revisi',
            'status'             => Article::STATUS_REVISION_REQUIRED,
        ]);

        $payload = [
            'revision_file' => UploadedFile::fake()->create('revised.pdf', 300, 'application/pdf'),
            'author_note'   => 'Berikut revisi sesuai catatan reviewer 1 dan 2.',
        ];

        $response = $this->actingAs($author)->post(route('author.articles.revision.store', $article), $payload);
        $response->assertRedirect();

        Notification::assertSentTo($editor, RevisionSubmittedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_publishing_article_notifies_author(): void
    {
        Notification::fake();

        $author = $this->makeUser('author');
        $admin = $this->makeUser('admin');
        $journal = Journal::factory()->create();
        $issue = Issue::create([
            'journal_id'     => $journal->id,
            'title'          => 'Vol 1 No 1 (2026)',
            'volume'         => 1,
            'number'         => 1,
            'year'           => 2026,
            'status'         => 'published',
            'published_date' => now(),
        ]);

        $article = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Uji Notifikasi Publish',
            'slug'          => 'uji-notifikasi-publish',
            'status'        => Article::STATUS_ACCEPTED,
        ]);

        $articleService = app(ArticleService::class);
        $articleService->publish($article, $issue->id);

        Notification::assertSentTo($author, ArticlePublishedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_publishing_issue_notifies_authors(): void
    {
        Notification::fake();

        $author = $this->makeUser('author');
        $admin = $this->makeUser('admin');
        $journal = Journal::factory()->create();
        $issue = Issue::create([
            'journal_id' => $journal->id,
            'title'      => 'Vol 2 No 1 (2026)',
            'volume'     => 2,
            'number'     => 1,
            'year'       => 2026,
            'status'     => 'draft',
        ]);

        $article = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Artikel Dalam Issue Terbit',
            'slug'          => 'artikel-dalam-issue-terbit',
            'status'        => Article::STATUS_ACCEPTED,
            'issue_id'      => $issue->id,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.issues.publish', $issue));
        $response->assertRedirect();

        Notification::assertSentTo($author, ArticlePublishedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id;
        });
    }

    public function test_stage_change_event_triggers_appropriate_notifications(): void
    {
        Notification::fake();

        $author = $this->makeUser('author');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id'    => $journal->id,
            'author_id'     => $author->id,
            'title'         => 'Tahap Copyediting',
            'status'        => 'accepted',
            'current_stage' => 'accepted',
        ]);

        // Trigger copyediting stage
        event(new SubmissionStageChanged($article, 'accepted', 'copy_editing'));

        Notification::assertSentTo($author, ProductionStageNotification::class, function ($notification) {
            return $notification->stage === 'copy_editing';
        });

        // Trigger desk rejected stage
        event(new SubmissionStageChanged($article, 'screening', 'desk_rejected'));

        Notification::assertSentTo($author, DeskRejectedNotification::class);
    }

    public function test_payment_rejection_notifies_author(): void
    {
        Notification::fake();

        $author = $this->makeUser('author');
        $admin = $this->makeUser('admin');
        $journal = Journal::factory()->create();

        $article = $this->makeArticle([
            'journal_id' => $journal->id,
            'author_id'  => $author->id,
            'title'      => 'Artikel Uji Pembayaran',
            'status'     => Article::STATUS_PAYMENT_UPLOADED,
        ]);

        $invoice = \App\Models\Invoice::create([
            'journal_id'     => $journal->id,
            'submission_id'  => $article->id,
            'invoice_number' => 'INV-2026-TEST01',
            'amount'         => 1500000,
            'due_date'       => now()->addDays(14),
            'status'         => 'waiting_verification',
        ]);

        $payment = \App\Models\Payment::create([
            'invoice_id'     => $invoice->id,
            'author_id'      => $author->id,
            'amount'         => 1500000,
            'payment_method' => 'bank_transfer',
            'payment_date'   => now(),
            'proof_path'     => 'uploads/payments/proof.jpg',
            'status'         => 'waiting_verification',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.reject', $payment), [
            'admin_notes' => 'Nominal transfer tidak sesuai dengan jumlah tagihan invoice.',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($author, \App\Notifications\PaymentRejectedNotification::class, function ($notification) use ($article) {
            return $notification->article->id === $article->id && str_contains($notification->adminNotes, 'Nominal transfer');
        });
    }

    public function test_smtp_configuration_loads_from_settings_table_not_env(): void
    {
        // 1. Simpan pengaturan SMTP pada tabel `settings` (menu Setting admin)
        \App\Models\Setting::set('mail_enabled', '1');
        \App\Models\Setting::set('mail_host', 'smtp.custom-domain.ac.id');
        \App\Models\Setting::set('mail_port', '465');
        \App\Models\Setting::set('mail_encryption', 'ssl');
        \App\Models\Setting::set('mail_username', 'redaksi@custom-domain.ac.id');
        \App\Models\Setting::set('mail_password', 'secret-smtp-password');
        \App\Models\Setting::set('mail_from_address', 'redaksi@custom-domain.ac.id');
        \App\Models\Setting::set('mail_from_name', 'Redaksi Jurnal Custom');

        // 2. Jalankan applyConfig dari service provider
        \App\Providers\MailConfigServiceProvider::applyConfig();

        // 3. Verifikasi konfigurasi mail Laravel telah dioverride oleh database
        $this->assertEquals('smtp', config('mail.default'));
        $this->assertEquals('smtp.custom-domain.ac.id', config('mail.mailers.smtp.host'));
        $this->assertEquals(465, config('mail.mailers.smtp.port'));
        $this->assertEquals('ssl', config('mail.mailers.smtp.encryption'));
        $this->assertEquals('redaksi@custom-domain.ac.id', config('mail.mailers.smtp.username'));
        $this->assertEquals('secret-smtp-password', config('mail.mailers.smtp.password'));
        $this->assertEquals('redaksi@custom-domain.ac.id', config('mail.from.address'));
        $this->assertEquals('Redaksi Jurnal Custom', config('mail.from.name'));
    }

    public function test_admin_can_send_test_email_from_settings_menu(): void
    {
        $admin = $this->makeUser('admin');

        \Illuminate\Support\Facades\Mail::fake();

        $response = $this->actingAs($admin)->post(route('admin.settings.test-email'), [
            'test_email' => 'editor@ojs.id',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
