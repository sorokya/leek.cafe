<?php

declare(strict_types=1);

namespace App\Services;

final class ContentExcerptGenerator
{
    public function __construct(
        private ContentRenderer $renderer,
    ) {}

    public function generate(string $body, int $length = 200): string
    {
        $rendered = $this->renderer->render($body);
        $text = strip_tags((string) $rendered);
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
