<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Journal;
use App\Models\User;
use Database\Seeders\NavigationSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorArticleSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles/permissions (Spatie) harus ada agar middleware `permission:`
        // dan menu sidebar berbasis `can()` dapat dievaluasi.
        $this->seed([
            RolePermissionSeeder::class,
            NavigationSeeder::class,
        ]);
    }

    private function makeAuthor(): User
    {
        $author = User::factory()->create([
            'role' => 'author',
            'is_active' => true,
        ]);
        $author->syncRoles(['author']);

        return $author;
    }

    public function test_author_can_view_create_article_page(): void
    {
        $author = $this->makeAuthor();

        $response = $this->actingAs($author)->get(route('author.articles.create'));
        $response->assertStatus(200);
        $response->assertSee('Kirim Manuskrip Baru');
    }

    public function test_author_can_submit_article_successfully(): void
    {
        Notification::fake();
        Storage::fake('public');

        $author = $this->makeAuthor();

        $journal = Journal::factory()->create([
            'is_active' => true,
        ]);

        $payload = [
            'journal_id'       => $journal->id,
            'title'            => 'Penerapan Machine Learning untuk Klasifikasi Teks Ilmiah',
            'abstract'         => 'Penelitian ini bertujuan untuk menganalisis efektivitas model klasifikasi naskah ilmiah berbasis machine learning dengan akurasi tinggi dan kinerja optimal pada sistem jurnal ilmiah.',
            'keywords'         => 'machine learning, klasifikasi teks, ojs',
            'language'         => 'id',
            'manuscript_file'  => UploadedFile::fake()->create('manuscript.pdf', 500, 'application/pdf'),
            'cover_letter'     => UploadedFile::fake()->create('cover_letter.pdf', 200, 'application/pdf'),
            'author_note'      => 'Mohon diproses untuk edisi mendatang.',
        ];

        $response = $this->actingAs($author)->post(route('author.articles.store'), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'author_id'  => $author->id,
            'journal_id' => $journal->id,
            'title'      => 'Penerapan Machine Learning untuk Klasifikasi Teks Ilmiah',
            'status'     => Article::STATUS_SUBMITTED,
        ]);

        $article = Article::where('title', 'Penerapan Machine Learning untuk Klasifikasi Teks Ilmiah')->first();
        $this->assertNotNull($article);
        $this->assertIsArray($article->keywords);
        $this->assertContains('machine learning', $article->keywords);
        $this->assertContains('klasifikasi teks', $article->keywords);
        $this->assertContains('ojs', $article->keywords);
    }

    public function test_submit_fails_when_required_fields_are_missing(): void
    {
        $author = $this->makeAuthor();

        $response = $this->actingAs($author)->post(route('author.articles.store'), []);

        $response->assertSessionHasErrors([
            'journal_id',
            'title',
            'abstract',
            'keywords',
            'language',
            'manuscript_file',
        ]);
    }
}
