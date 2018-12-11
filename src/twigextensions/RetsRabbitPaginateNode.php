<?php


namespace apc\retsrabbit\twigextensions;


class RetsRabbitPaginateNode extends \Twig_Node
{
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('list(')
            ->subcompile($this->getNode('paginateTarget'))
            ->raw(', ')
            ->subcompile($this->getNode('elementsTarget'))
            ->raw(') = \apc\retsrabbit\helpers\TemplateHelper::paginateProperties(')
            ->subcompile($this->getNode('criteria'))
            ->raw(");\n");
    }
}