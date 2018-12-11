<?php

namespace apc\retsrabbit\twigextensions;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author APC, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class RetsRabbitTwigExtension extends \Twig_Extension
{
    public function getTokenParsers(): array
    {
        return [
            new RetsRabbitPaginateTokenParser()
        ];
    }
}
