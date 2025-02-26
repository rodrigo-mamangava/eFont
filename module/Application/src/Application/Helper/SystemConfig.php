<?php
namespace Application\Helper;

use \Zend\View\Helper\AbstractHelper;
use \Zend\View\HelperPluginManager as ServiceManager;

class SystemConfig extends AbstractHelper
{

    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function __invoke()
    {
        $config = $this->serviceManager->getServiceLocator()->get('Config');
        return $config;
    }
}