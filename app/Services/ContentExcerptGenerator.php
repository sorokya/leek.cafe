<?php

declare(strict_types=1);

namespace App\Services;

final readonly class ContentExcerptGenerator
{
    public function generate(string $rendered, int $length = 200): string
    {
        $text = trim(strip_tags($rendered));
        if (strlen($text) <= $length) {
            return $text;
        }

        $excerpt = substr($text, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }

        return $excerpt . '…';
    }
}
