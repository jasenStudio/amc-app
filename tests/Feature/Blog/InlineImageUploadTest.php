<?php

namespace Tests\Feature\Blog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InlineImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private string $disk = 'public';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::disk($this->disk)->makeDirectory('blog/webp/body');
    }

    public function test_guest_is_rejected(): void
    {
        $this->postJson(route('blog.images.store'), [
            'upload' => $this->uploadedPng(),
        ])->assertStatus(401);
    }

    public function test_pending_user_is_redirected_to_pending_approval(): void
    {
        $user = User::factory()->pending()->create();

        $this->actingAs($user)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertRedirect(route('pending.approval'));
    }

    public function test_editor_can_upload_valid_image(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk()
            ->assertJsonStructure(['url']);
    }

    public function test_admin_can_upload_valid_image(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk()
            ->assertJsonStructure(['url']);
    }

    public function test_file_above_size_limit_fails(): void
    {
        $admin = User::factory()->admin()->create();

        // 3 MB file exceeds the 2 MB limit.
        $file = UploadedFile::fake()->image('cover.png', 1600, 900)->size(3072);

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $file,
            ])
            ->assertUnprocessable();
    }

    public function test_file_above_dimensions_fails(): void
    {
        $admin = User::factory()->admin()->create();

        // 4000x4000 exceeds the 3000x3000 limit.
        $file = UploadedFile::fake()->image('big.png', 4000, 4000)->size(100);

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $file,
            ])
            ->assertUnprocessable();
    }

    public function test_disallowed_mime_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $file,
            ])
            ->assertUnprocessable();
    }

    public function test_gif_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $file = UploadedFile::fake()->create('animation.gif', 100, 'image/gif');

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $file,
            ])
            ->assertUnprocessable();
    }

    public function test_uploaded_file_is_stored_as_webp(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk();

        // Find the stored file.
        $files = Storage::disk($this->disk)->allFiles('blog/webp/body/full');
        $this->assertNotEmpty($files);

        $storedFile = collect($files)->first(fn (string $f) => str_ends_with($f, '.webp'));
        $this->assertNotNull($storedFile);
        $this->assertSame('image/webp', Storage::disk($this->disk)->mimeType($storedFile));
    }

    public function test_stored_filename_does_not_include_client_name(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk();

        $files = Storage::disk($this->disk)->allFiles('blog/webp/body/full');
        foreach ($files as $file) {
            $this->assertStringNotContainsString('cover', $file);
            $this->assertStringNotContainsString('evil', $file);
        }
    }

    public function test_path_belongs_to_expected_directory(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk();

        $url = $response->json('url');
        $this->assertStringContainsString('blog/webp/body/full/', $url);
    }

    public function test_endpoint_returns_url_compatible_with_tiptap(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk();

        $payload = $response->json();
        $this->assertArrayHasKey('url', $payload);
        $this->assertIsString($payload['url']);
        $this->assertNotEmpty($payload['url']);
    }

    public function test_endpoint_url_uses_storage_disk(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $this->uploadedPng(),
            ])
            ->assertOk();

        $url = $response->json('url');

        // The URL should be resolvable via Storage::disk('public')->url().
        $this->assertStringStartsWith(
            rtrim(config('filesystems.disks.public.url'), '/').'/',
            $url,
        );
    }

    public function test_missing_upload_field_fails(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [])
            ->assertUnprocessable();
    }

    public function test_small_image_is_accepted(): void
    {
        $admin = User::factory()->admin()->create();

        // 400x300 is below the 1200x675 minimum that applies to covers/gallery,
        // but inline images are exempt from that constraint.
        $file = $this->uploadedPng(400, 300);

        $this->actingAs($admin)
            ->postJson(route('blog.images.store'), [
                'upload' => $file,
            ])
            ->assertOk()
            ->assertJsonStructure(['url']);
    }

    private function uploadedPng(int $width = 1600, int $height = 900): UploadedFile
    {
        return UploadedFile::fake()->image('cover.png', $width, $height)->size(100);
    }
}
