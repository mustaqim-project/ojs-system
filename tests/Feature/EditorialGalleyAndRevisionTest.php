<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EditorialGalleyAndRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions
        Permission::firstOrCreate(['name' => 'submission.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'submission.update', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'decision.make', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'decision.view', 'guard_name' => 'web']);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->givePermissionTo(['submission.view', 'submission.update', 'decision.make', 'decision.view']);

        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $authorRole->givePermissionTo(['submission.view', 'submission.update']);
    }

    private function makeArticle(User $author, Journal $journal, array $overrides = []): Article
    {
        return Article::create(array_merge([
            'journal_id'      => $journal->id,
            'author_id'       => $author->id,
            'title'           => 'Sample Article for Galley Test',
            'slug'            => 'sample-article-for-galley-test',
            'abstract'        => 'Original abstract content before revision.',
            'keywords'        => ['research', 'journal'],
            'manuscript_file' => 'uploads/manuscripts/sample.pdf',
            'status'          => Article::STATUS_SUBMITTED,
            'submitted_at'    => now(),
        ], $overrides));
    }

    public function test_admin_and_editor_can_upload_and_delete_galley_pdf(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $journal = Journal::create([
            'title' => 'Jurnal Teknologi',
            'slug'  => 'jurnal-teknologi',
        ]);

        $author = User::factory()->create(['is_active' => true]);
        $author->assignRole('author');

        $article = $this->makeArticle($author, $journal);

        $fakePdf = UploadedFile::fake()->create('final_layout.pdf', 500, 'application/pdf');

        // Admin upload Galley
        $response = $this->actingAs($admin)
            ->post(route('admin.articles.upload-galley', $article), [
                'galley_file' => $fakePdf,
                'label'       => 'PDF Resmi',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('article_galleys', [
            'article_id'   => $article->id,
            'label'        => 'PDF Resmi',
            'mime_type'    => 'application/pdf',
            'is_published' => 1,
        ]);

        $galley = ArticleGalley::where('article_id', $article->id)->first();
        $this->assertNotNull($galley);

        // Admin delete Galley
        $delResponse = $this->actingAs($admin)
            ->delete(route('admin.articles.delete-galley', [$article, $galley]));

        $delResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('article_galleys', [
            'id' => $galley->id,
        ]);
    }

    public function test_author_can_submit_revision_with_updated_abstract_and_keywords(): void
    {
        $journal = Journal::create([
            'title' => 'Jurnal Teknologi',
            'slug'  => 'jurnal-teknologi',
        ]);

        $author = User::factory()->create(['is_active' => true]);
        $author->assignRole('author');

        $article = $this->makeArticle($author, $journal, [
            'status' => Article::STATUS_REVISION_REQUIRED,
        ]);

        $revisionPdf = UploadedFile::fake()->create('revised_manuscript.pdf', 800, 'application/pdf');

        $response = $this->actingAs($author)
            ->post(route('author.articles.revision.store', $article), [
                'revision_file' => $revisionPdf,
                'author_note'   => 'Perbaikan telah dilakukan sesuai instruksi reviewer.',
                'abstract'      => 'Abstrak baru yang telah diperbarui sesuai saran penelaah.',
                'keywords'      => 'inovasi, kecerdasan buatan, edukasi',
            ]);

        $response->assertRedirect(route('author.articles.show', $article));

        $article->refresh();
        $this->assertEquals(Article::STATUS_UNDER_REVIEW, $article->status);
        $this->assertEquals('Abstrak baru yang telah diperbarui sesuai saran penelaah.', $article->abstract);
        $this->assertEquals(['inovasi', 'kecerdasan buatan', 'edukasi'], $article->keywords);
        $this->assertNotNull($article->revision_file);
    }

    public function test_public_can_download_published_article_galley(): void
    {
        $journal = Journal::create([
            'title' => 'Jurnal Teknologi',
            'slug'  => 'jurnal-teknologi',
        ]);

        $author = User::factory()->create();
        $author->assignRole('author');

        $article = $this->makeArticle($author, $journal, [
            'status'       => Article::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.articles.download', $article->slug));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
