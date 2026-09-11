<?php

declare(strict_types=1);

namespace App\Support;

final class MediaPlayback
{
    /**
     * @return array{player: string, embed_url: ?string, kind: string}
     */
    public static function describe(string $fileType, string $url): array
    {
        $embed = self::embedUrl($url);

        $player = match ($fileType) {
            'image', 'voucher' => 'image',
            'pdf' => 'pdf',
            'audio' => $embed ? 'iframe' : 'audio',
            'video' => $embed ? 'iframe' : 'video',
            default => 'link',
        };

        return [
            'player' => $player,
            'embed_url' => $embed,
            'kind' => match ($fileType) {
                'image' => 'flyer',
                default => $fileType,
            },
        ];
    }

    public static function embedUrl(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\\.com/watch\\?v=|youtube\\.com/embed/|youtu\\.be/)([A-Za-z0-9_-]{6,})~', $url, $match) === 1) {
            return 'https://www.youtube.com/embed/'.$match[1];
        }

        if (preg_match('~vimeo\\.com/(?:video/)?(\\d+)~', $url, $match) === 1) {
            return 'https://player.vimeo.com/video/'.$match[1];
        }

        if (preg_match('~open\\.spotify\\.com/(episode|track|playlist|album)/([A-Za-z0-9]+)~', $url, $match) === 1) {
            return 'https://open.spotify.com/embed/'.$match[1].'/'.$match[2];
        }

        if (preg_match('~soundcloud\\.com/.+~', $url) === 1 && ! str_contains($url, 'w.soundcloud.com')) {
            return 'https://w.soundcloud.com/player/?url='.rawurlencode($url).'&color=%23ff5500&auto_play=false';
        }

        return null;
    }
}
