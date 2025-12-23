<?php

declare(strict_types=1);

namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Node\Node;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use Highlight\Highlighter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

final class PostRenderer
{
    private MarkdownConverter $markdown;

    public function __construct()
    {
        $environment = new Environment([
            // Do not allow raw HTML from post bodies (prevents XSS when rendering)
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $highlighter = new Highlighter();

        $renderCodeBlock = static function (string $code, ?string $language) use ($highlighter): \Stringable {
            $code = rtrim($code, "\n");

            try {
                $highlighted = ($language !== null && $language !== '')
                    ? $highlighter->highlight($language, $code)
                    : $highlighter->highlightAuto($code);

                $languageClass = $highlighted->language !== ''
                    ? ' language-' . htmlspecialchars($highlighted->language, ENT_QUOTES, 'UTF-8')
                    : '';

                return new class($highlighted->value, $languageClass) implements \Stringable {
                    public function __construct(
                        private readonly string $value,
                        private readonly string $languageClass,
                    ) {}

                    public function __toString(): string
                    {
                        return '<pre><code class="hljs' . $this->languageClass . '">' . $this->value . '</code></pre>';
                    }
                };
            } catch (\Throwable) {
                // Fallback: render as plain code, safely escaped.
                $escaped = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');

                return new class($escaped) implements \Stringable {
                    public function __construct(private readonly string $escaped) {}

                    public function __toString(): string
                    {
                        return '<pre><code>' . $this->escaped . '</code></pre>';
                    }
                };
            }
        };

        $environment->addRenderer(FencedCode::class, new class($renderCodeBlock) implements NodeRendererInterface {
            /** @var \Closure(string, string|null): \Stringable */
            private \Closure $renderCodeBlock;

            /** @param \Closure(string, string|null): \Stringable $renderCodeBlock */
            public function __construct(\Closure $renderCodeBlock)
            {
                $this->renderCodeBlock = $renderCodeBlock;
            }

            public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
            {
                \assert($node instanceof FencedCode);

                $code = $node->getLiteral();
                $info = trim($node->getInfo() ?? '');
                $languageToken = $info !== '' ? strtok($info, " \t") : false;
                $language = $languageToken === false ? null : $languageToken;

                return ($this->renderCodeBlock)($code, $language);
            }
        });

        $environment->addRenderer(IndentedCode::class, new class($renderCodeBlock) implements NodeRendererInterface {
            /** @var \Closure(string, string|null): \Stringable */
            private \Closure $renderCodeBlock;

            /** @param \Closure(string, string|null): \Stringable $renderCodeBlock */
            public function __construct(\Closure $renderCodeBlock)
            {
                $this->renderCodeBlock = $renderCodeBlock;
            }

            public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
            {
                \assert($node instanceof IndentedCode);

                $code = $node->getLiteral();

                return ($this->renderCodeBlock)($code, null);
            }
        });

        $this->markdown = new MarkdownConverter($environment);
    }

    public function render(string $markdown): RenderedContentInterface
    {
        return $this->markdown->convert($markdown);
    }
}
