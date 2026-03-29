<?php

namespace App\Support\PageContent;

class ContactPageContent
{
    public static function fields(): array
    {
        return [
            'hero_title',
            'form_title',
            'address_title',
            'address_text',
            'phone_title',
            'phone_text',
            'email_title',
            'email_text',
            'map_x',
            'map_y',
            'map_zoom',
        ];
    }

    public static function fromRequest(array $data, string $locale): array
    {
        return [
            'hero_title'    => trim((string) ($data["hero_title_{$locale}"] ?? 'Contact')),
            'form_title'    => trim((string) ($data["form_title_{$locale}"] ?? 'Send Us A Message')),
            'address_title' => trim((string) ($data["address_title_{$locale}"] ?? 'Address')),
            'address_text'  => trim((string) ($data["address_text_{$locale}"] ?? '')),
            'phone_title'   => trim((string) ($data["phone_title_{$locale}"] ?? "Let's Talk")),
            'phone_text'    => trim((string) ($data["phone_text_{$locale}"] ?? '')),
            'email_title'   => trim((string) ($data["email_title_{$locale}"] ?? 'Support Email')),
            'email_text'    => trim((string) ($data["email_text_{$locale}"] ?? '')),
            'map_x'         => trim((string) ($data["map_x_{$locale}"] ?? '24.7136')),
            'map_y'         => trim((string) ($data["map_y_{$locale}"] ?? '46.6753')),
            'map_zoom'      => trim((string) ($data["map_zoom_{$locale}"] ?? '11')),
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
