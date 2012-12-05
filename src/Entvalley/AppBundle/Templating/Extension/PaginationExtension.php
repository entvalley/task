<?php

namespace Entvalley\AppBundle\Templating\Extension;
use Entvalley\AppBundle\Service\Pagination;

class PaginationExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'pagination' => new \Twig_Function_Method($this, 'pagination', array('is_safe' => array('all'))),
        );
    }

    public function pagination(Pagination $pagination, $template = null, $additional_params = array(), $attrs = array())
    {
        if (null === $template) {
            $template = 'EntvalleyAppBundle:Pagination:_standard.html.twig';
        }

        $template = $this->environment->loadTemplate($template);
        return $template->render(array(
            'pagination' => $pagination,
            'additional_params' => $additional_params,
            'attrs' => $attrs
        ));
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'pagination';
    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
}
