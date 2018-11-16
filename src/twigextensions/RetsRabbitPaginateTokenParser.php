<?php


namespace apc\retsrabbit\twigextensions;


use Twig_Error_Syntax;
use Twig_Node;
use Twig_Token;

class RetsRabbitPaginateTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token
     * @return Twig_Node A Twig_Node instance
     *
     * @throws Twig_Error_Syntax
     */
    public function parse(Twig_Token $token): Twig_Node
    {
        $lineno            = $token->getLine();
        $nodes['criteria'] = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect('as');
        $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        if (count($targets) > 1) {
            $paginateTarget          = $targets->getNode(0);
            $nodes['paginateTarget'] = new \Twig_Node_Expression_AssignName($paginateTarget->getAttribute('name'), $paginateTarget->getTemplateLine());
            $elementsTarget          = $targets->getNode(1);
        } else {
            $nodes['paginateTarget'] = new \Twig_Node_Expression_AssignName('paginate', $lineno);
            $elementsTarget          = $targets->getNode(0);
        }
        $nodes['elementsTarget'] = new \Twig_Node_Expression_AssignName($elementsTarget->getAttribute('name'), $elementsTarget->getTemplateLine());

        return new RetsRabbitPaginateNode($nodes, [], $lineno, $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'rrPaginate';
    }
}