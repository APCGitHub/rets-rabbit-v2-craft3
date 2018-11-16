<?php
/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

namespace apc\retsrabbit\twigextensions;

use apc\retsrabbit\RetsRabbit;

use Craft;

/**
 * Twig can be extended in many ways; you can add extra tags, filters, tests, operators,
 * global variables, and functions. You can even extend the parser itself with
 * node visitors.
 *
 * http://twig.sensiolabs.org/doc/advanced.html
 *
 * @author    Anecka, LLC
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
