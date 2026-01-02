<?php

declare(strict_types=1);

use App\Services\ContentRenderer;

test('renders basic markdown to html', function (): void {
    $renderer = new ContentRenderer;
    $result = $renderer->render('# Hello World');

    expect((string) $result)->toContain('<h1>Hello World</h1>');
});

test('renders markdown with bold text', function (): void {
    $renderer = new ContentRenderer;
    $result = $renderer->render('This is **bold** text');

    expect((string) $result)->toContain('<strong>bold</strong>');
});

test('renders markdown with italic text', function (): void {
    $renderer = new ContentRenderer;
    $result = $renderer->render('This is *italic* text');

    expect((string) $result)->toContain('<em>italic</em>');
});

test('renders markdown with links', function (): void {
    $renderer = new ContentRenderer;
    $result = $renderer->render('[Link](https://example.com)');

    expect((string) $result)
        ->toContain('<a href="https://example.com">Link</a>');
});

test('renders code blocks with syntax highlighting', function (): void {
    $renderer = new ContentRenderer;
    $code = "```php\n<?php echo 'Hello';\n```";
    $result = $renderer->render($code);

    expect((string) $result)
        ->toContain('<pre><code class="hljs')
        ->toContain('language-php');
});

test('strips html when configured', function (): void {
    $renderer = new ContentRenderer(strip: true);
    $result = $renderer->render('Hello <script>alert("xss")</script>');

    expect((string) $result)->not->toContain('<script>');
});

test('allows html when not configured to strip', function (): void {
    $renderer = new ContentRenderer(strip: false);
    $result = $renderer->render('Hello <strong>world</strong>');

    expect((string) $result)->toContain('<strong>world</strong>');
});
