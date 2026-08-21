<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk($this->disk)->makeDirectory('blog/webp');
    }

    public function test_guest_is_rejected(): void
    {
        $this->postJson(route('dashboard.images.store'), [
            'upload' => $this->uploadedPng(),
            'path' => 'blog/webp',
        ])->assertStatus(401);
    }

    public function test_user_without_role_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => 'blog/webp',
            ])
            ->assertForbidden();
    }

    public function test_editor_can_upload_valid_image(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => 'blog/webp',
            ])
            ->assertOk()
            ->assertJsonStructure(['thumb', 'full', 'url']);
    }

    public function test_admin_can_upload_valid_image(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => 'blog/webp',
            ])
            ->assertOk()
            ->assertJsonStructure(['thumb', 'full', 'url']);
    }

    public function test_file_above_size_limit_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('cover.png', 1600, 900)->size(3072);

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $file,
                'path' => 'blog/webp',
            ])
            ->assertUnprocessable();
    }

    public function test_file_above_dimensions_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->image('big.png', 4000, 4000)->size(100);

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $file,
                'path' => 'blog/webp',
            ])
            ->assertUnprocessable();
    }

    public function test_disallowed_mime_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $file,
                'path' => 'blog/webp',
            ])
            ->assertUnprocessable();
    }

    public function test_gif_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->create('animation.gif', 100, 'image/gif');

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $file,
                'path' => 'blog/webp',
            ])
            ->assertUnprocessable();
    }

    public function test_uploaded_file_is_stored_as_webp(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => 'blog/webp',
            ])
            ->assertOk();

        $files = Storage::disk($this->disk)->allFiles('blog/webp/full');
        $this->assertNotEmpty($files);

        $storedFile = collect($files)->first(fn (string $f) => str_ends_with($f, '.webp'));
        $this->assertNotNull($storedFile);
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($storedFile));
    }

    public function test_stored_filename_does_not_include_client_name(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => 'blog/webp',
            ])
            ->assertOk();

        $files = Storage::disk($this->disk)->allFiles('blog/webp/full');
        foreach ($files as $file) {
            $this->assertStringNotContainsString('cover', $file);
            $this->assertStringNotContainsString('evil', $file);
        }
    }

    public function test_path_must_be_valid(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
                'path' => '../../../etc/passwd',
            ])
            ->assertUnprocessable();
    }

    public function test_missing_upload_field_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'path' => 'blog/webp',
            ])
            ->assertUnprocessable();
    }

    public function test_missing_path_field_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('dashboard.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertUnprocessable();
    }

    private function uploadedPng(int $width = 1600, int $height = 900): UploadedFile
    {
        return UploadedFile::fake()->image('cover.png', $width, $height)->size(100);
    }
}
