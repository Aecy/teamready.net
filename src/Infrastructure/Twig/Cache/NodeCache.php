<?php

namespace App\Infrastructure\Twig\Cache;

use App\Infrastructure\Twig\TwigCacheExtension;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class NodeCache extends Node
{

    private static int $count = 1;

    public function __construct(AbstractExpression $key, Node $body, int $lineno, string $tag = null)
    {
        parent::__construct(['key' => $key, 'body' => $body], [], $lineno, $tag);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Compiler $compiler)
    {
        $i = self::$count++;
        $extension = TwigCacheExtension::class;
        $compiler
            ->addDebugInfo($this)
            ->write("\$twigCacheExtension = \$this->env->getExtension('{$extension}');\n")
            ->write("\$twigCacheBody{$i} = \$twigCacheExtension->getCacheValue(")
            ->subcompile($this->getNode('key'))
            ->raw(");\n")
            ->write("if (\$twigCacheBody{$i} !== null) { echo \$twigCacheBody{$i}; } else {\n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("\$twigCacheBody{$i} = ob_get_clean();\n")
            ->write("\$twigCacheExtension->setCacheValue(")
            ->subcompile($this->getNode('key'))
            ->raw(',')
            ->raw("\$twigCacheBody{$i});\n")
            ->write("echo \$twigCacheBody{$i};\n")
            ->outdent()
            ->write("}\n");
    }

}