<?php

namespace App\Support\PageContent;

class AboutPageContent
{
    public static function fields(): array
    {
        return [
            'hero_title',
            'story_title',
            'story_text_1',
            'story_text_2',
            'story_text_3',
            'mission_title',
            'mission_text',
            'quote_text',
            'quote_author',
            'image_1',
            'image_2',
        ];
    }

    public static function fromRequest(array $data, string $locale): array
    {
        return [
            'hero_title'    => trim((string) ($data["hero_title_{$locale}"] ?? '')),
            'story_title'   => trim((string) ($data["story_title_{$locale}"] ?? '')),
            'story_text_1'  => trim((string) ($data["story_text_1_{$locale}"] ?? '')),
            'story_text_2'  => trim((string) ($data["story_text_2_{$locale}"] ?? '')),
            'story_text_3'  => trim((string) ($data["story_text_3_{$locale}"] ?? '')),
            'mission_title' => trim((string) ($data["mission_title_{$locale}"] ?? '')),
            'mission_text'  => trim((string) ($data["mission_text_{$locale}"] ?? '')),
            'quote_text'    => trim((string) ($data["quote_text_{$locale}"] ?? '')),
            'quote_author'  => trim((string) ($data["quote_author_{$locale}"] ?? '')),
            'image_1'       => trim((string) ($data["image_1_{$locale}"] ?? 'images/about-01.jpg')),
            'image_2'       => trim((string) ($data["image_2_{$locale}"] ?? 'images/about-02.jpg')),
        ];
    }

    public static function encode(array $data, string $locale): string
    {
        return json_encode(
            self::fromRequest($data, $locale),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    public static function decode(?string $content): array
    {
        if (! is_string($content) || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }
}
