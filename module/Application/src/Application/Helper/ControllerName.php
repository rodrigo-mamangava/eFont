<?php
namespace Application\Helper;

use \Zend\View\Helper\AbstractHelper;

class ControllerName extends AbstractHelper
{

    protected $routeMatch;

    public function __construct($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    public function __invoke()
    {
        if ($this->routeMatch) {
            $controller = $this->routeMatch->getParam('controller', 'index');
            $action = $this->routeMatch->getParam('action', 'index');
            return array(
                'controller' => $controller,
                'action' => $action
            );
        }
    }
}