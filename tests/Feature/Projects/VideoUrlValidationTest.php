<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\ProjectForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VideoUrlValidationTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('validVideoUrlsProvider')]
    public function test_accepts_valid_video_urls(string $url): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'Test Project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('video_url', $url)
            ->call('save')
            ->assertHasNoErrors(['video_url']);
    }

    public static function validVideoUrlsProvider(): array
    {
        return [
            'youtube standard' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'youtube short' => ['https://youtu.be/dQw4w9WgXcQ'],
            'vimeo' => ['https://vimeo.com/123456789'],
        ];
    }

    #[DataProvider('invalidVideoUrlsProvider')]
    public function test_rejects_invalid_video_urls(string $url): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'Test Project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('video_url', $url)
            ->call('save')
            ->assertHasErrors(['video_url']);
    }

    public static function invalidVideoUrlsProvider(): array
    {
        return [
            'dailymotion' => ['https://www.dailymotion.com/video/x123456'],
            'direct mp4' => ['https://example.com/video.mp4'],
            'random site' => ['https://example.com/watch?v=123'],
            'not a url' => ['not-a-video-url'],
        ];
    }

    public function test_accepts_null_video_url(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'Test Project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('video_url', '')
            ->call('save')
            ->assertHasNoErrors(['video_url']);
    }

    public function test_saves_video_url_to_database(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProjectForm::class)
            ->set('title', 'Test Project')
            ->set('description', 'Test description')
            ->set('client', 'Test Client')
            ->set('date', '2024-01-15')
            ->set('video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('save');

        $this->assertDatabaseHas('projects', [
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
