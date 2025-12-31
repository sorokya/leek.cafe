<?php

declare(strict_types=1);

use App\Services\ContentExcerptGenerator;
use App\Services\ContentRenderer;

test('generates excerpt from plain text', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $excerpt = $generator->generate('This is a short text');
    
    expect($excerpt)->toBe('This is a short text');
});

test('truncates long text at word boundary', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $longText = str_repeat('word ', 50); // Create a very long text
    $excerpt = $generator->generate($longText, 50);
    
    expect($excerpt)
        ->toEndWith('…')
        ->and(strlen($excerpt))->toBeLessThanOrEqual(51);
});

test('strips html tags from markdown', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $excerpt = $generator->generate('# Hello **World**');
    
    expect($excerpt)
        ->not->toContain('<h1>')
        ->not->toContain('<strong>')
        ->toContain('Hello World');
});

test('respects custom length parameter', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $longText = str_repeat('word ', 100);
    $excerpt = $generator->generate($longText, 100);
    
    expect(strlen($excerpt))->toBeLessThanOrEqual(101); // 100 + ellipsis
});

test('does not add ellipsis if text is shorter than length', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $shortText = 'Short text';
    $excerpt = $generator->generate($shortText, 200);
    
    expect($excerpt)
        ->not->toEndWith('…')
        ->toBe('Short text');
});

test('adds ellipsis when text is truncated', function () {
    $generator = new ContentExcerptGenerator(new ContentRenderer());
    $longText = 'This is a very long text that will definitely be truncated because it exceeds the maximum length parameter that we set for the excerpt generator and we want to make sure it ends with an ellipsis character';
    $excerpt = $generator->generate($longText, 50);
    
    expect($excerpt)->toEndWith('…');
});
