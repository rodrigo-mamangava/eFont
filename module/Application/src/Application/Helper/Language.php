<?php

namespace Application\Helper;

use Zend\View\Helper\AbstractHelper;

class Language extends AbstractHelper {
	protected $routeMatch;
	public function __construct($routeMatch) {
		$this->routeMatch = $routeMatch;
	}
	public function __invoke() {
		if ($this->routeMatch) {
			return $this->routeMatch->getParam ( 'lang', 'en' );
		}
	}
}