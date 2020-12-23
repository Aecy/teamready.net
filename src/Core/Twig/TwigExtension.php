<?php

namespace App\Core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'svgIcon'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [$this, 'menu_active'], ['is_safe' => ['html'], 'needs_context' => true])
        ];
    }

    /**
     * Generate the html code for an svg icon
     */
    public function svgIcon(string $name): string
    {
        return <<<HTML
          <svg class="icon">
              <use xlink:href="/sprite.svg#{$name}"></use>
          </svg>
        HTML;
    }

    /**
     * Add is-active for active element of menu
     * @param array<string,mixed> $context
     */
    public function menu_active(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return ' aria-current="page"';
        }
        return '';
    }

    /**
     * Return an excerpt of a text
     */
    public function excerpt(?string $content, int $characterLimit = 135): string
    {
        if ($content === null) {
            return '';
        }
        if (mb_strlen($content) <= $characterLimit) {
            return $content;
        }
        $lastSpace = strpos($content, ' ', $characterLimit);
        if ($lastSpace === false) {
            return $content;
        }
        return substr($content, 0, $lastSpace) . '...';
    }

}
