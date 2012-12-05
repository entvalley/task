<?php

namespace Entvalley\AppBundle\Service;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationRouterUrlGenerator implements PaginationUrlGenerator
{
    const PAGE_VARIABLE_NAME = 'page';
    private $router;
    private $route;
    private $params;

    public function __construct(RouterInterface $router, Request $request)
    {
        $this->router = $router;

        $this->route = $request->attributes->get('_route');
        $this->params = array_merge($request->query->all(), $request->attributes->all());
        foreach ($this->params as $key => $param) {
            if (strpos($key, '_') === 0) {
                unset($this->params[$key]);
            }
            // @todo get rid of it by moving to event listener
            if (is_object($param)) {
                $this->params[$key] = $param->getId();
            }
        }
    }

    public function generate($page)
    {
        return $this->router->generate($this->route, array_merge($this->params, array(
                    self::PAGE_VARIABLE_NAME => $page
                )));
    }
}