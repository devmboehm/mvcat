<?php
namespace bomi\mvcat\context;

use bomi\mvcat\exceptions\MvcException;
use bomi\mvcat\manifest\Manifest;
use bomi\mvcat\manifest\entities\Route;
use bomi\mvcat\manifest\entities\Template;
use bomi\mvcat\manifest\entities\Data;
use bomi\mvcat\manifest\entities\I18N;
use bomi\mvcat\exceptions\MvcNullReferenceException;

class ManifestContext {
	
	private $_controller;
	public function getController() : string {
		if ($this->_action === null) {
			throw new MvcNullReferenceException("Controller");
		}
		return $this->_controller;
	}
	
	private $_action;
	public function getAction() : string {
		if ($this->_action === null) {
			throw new MvcNullReferenceException("Action");
		}
		return $this->_action;
	}
	
	private $_parameters;
	public function getParameters() : array {
		return $this->_parameters;
	}
	
	private $_requestContext;
	public function getRequestContext() : RequestContext {
		return $this->_requestContext;
	}
	
	private $_viewsDestination;
	public function getViewsDestination() : string {
		return $this->_viewsDestination;
	}
	
	private $_templates = [];
	public function getTemplates(): array {
		return $this->_templates;
	}
	
	private $_repositories = [];
	public function getRepositories(): array {
		return $this->_repositories;
	}
	
	private $_i18n;
	public function getI18N(): I18N {
		return $this->_i18n;
	} 
	
	private $_globals;
	public function getGlobals(): array {
	    return $this->_globals;
	}

	private function __construct(Route $route, Manifest $manifest, RequestContext $requestContext) {
		$this->_requestContext = $requestContext;
		$this->_controller = $route->getParameters()->getController();
		$this->_action = $route->getParameters()->getAction();
		$this->_parameters = $route->getParameters()->getParameters();
		$this->_viewsDestination = $manifest->getRouting()->getViewsPaht();
		$this->_setTemplates($manifest->getTemplates());
		$this->_setRepositories($manifest->getData());
		$this->_i18n = $manifest->getI18N();
		$this->_globals = $manifest->getGlobals();
	}
	
	public static function create(Manifest $manifest, RequestContext $requestContext) {
		foreach ($manifest->getRouting()->getRoutes() as $route) {
			if (in_array($requestContext->getMethod(), $route->getMethods())  && self::_match($requestContext->getUrlParameters(), $route)) {
				return new self($route, $manifest, $requestContext);
			}
		}
	 	throw new MvcException("Requested url '{$requestContext->getUrlParameters()}' is not part of routing.", 404);
	}
	
	private static function _match(string $urlParameters, Route $route) : bool {
		if (preg_match($route->getPath(), $urlParameters, $matches)) {
			foreach ($matches as $key => $match) {
				if (is_string($key)) {
					$route->getParameters()->$key = $match;
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * @param Template[] $templates
	 */
	private function _setTemplates(array $templates) {		
		/** @var Template $template */
		foreach ($templates as $template) {
			$this->_templates[$template->getName()] = $template;
		}
		
	}
	
	/**
	 * 
	 * @param Data $data
	 */
	private function _setRepositories($data) {
		if ($data->getRepositories() !== null && !empty($data->getRepositories())) {
			foreach ($data->getRepositories() as $key => $value) {
				$class = str_replace("/", "\\", $value);
				$this->_repositories[$key] = new $class($data->getConnection());
			}
		}
	}
}

