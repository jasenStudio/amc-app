<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class VideoEmbed
{
    public static function provider(string $url): ?string
    {
        if (preg_match('#^https?://(www\.)?(youtube\.com/watch\?v=|youtu\.be/)#', $url)) {
            return 'youtube';
        }

        if (preg_match('#^https?://(www\.)?vimeo\.com/\d+#', $url)) {
            return 'vimeo';
        }

        return null;
    }

    public static function id(string $url): ?string
    {
        if (preg_match('#youtube\.com/watch\?v=([a-zA-Z0-9_-]+)#', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('#youtu\.be/([a-zA-Z0-9_-]+)#', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('#vimeo\.com/(\d+)#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public static function thumbnailUrl(string $url): ?string
    {
        $provider = self::provider($url);
        $id = self::id($url);

        if ($provider === null || $id === null) {
            return null;
        }

        if ($provider === 'youtube') {
            return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
        }

        if ($provider === 'vimeo') {
            return self::vimeoThumbnail($id);
        }

        return null;
    }

    public static function embedUrl(string $url): ?string
    {
        $provider = self::provider($url);
        $id = self::id($url);

        if ($provider === null || $id === null) {
            return null;
        }

        if ($provider === 'youtube') {
            return "https://www.youtube-nocookie.com/embed/{$id}";
        }

        if ($provider === 'vimeo') {
            return "https://player.vimeo.com/video/{$id}";
        }

        return null;
    }

    private static function vimeoThumbnail(string $videoId): ?string
    {
        try {
            $response = Http::timeout(3)
                ->get("https://vimeo.com/api/oembed.json?url=https://vimeo.com/{$videoId}");

            if ($response->successful()) {
                $data = $response->json();

                return $data['thumbnail_url'] ?? null;
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }
}
