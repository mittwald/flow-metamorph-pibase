<?php
namespace Mw\MetamorphPibase\Domain\Model\State;

use TYPO3\Flow\Annotations as Flow;

/**
 * @package    Mw\MetamorphPibase
 * @subpackage Domain\Model\State
 *
 * @Flow\Scope("prototype")
 */
class PluginMapping {

	/**
	 * @var string
	 */
	protected $pluginClass;

	/**
	 * @var string
	 * @Flow\Validate(type="Mw.Metamorph:PackageKey")
	 */
	protected $package;

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var string
	 */
	protected $controllerClass;

	public function __construct($pluginClass, $controllerClass, $package, $configuration = []) {
		$this->pluginClass     = $pluginClass;
		$this->package         = $package;
		$this->configuration   = $configuration;
		$this->controllerClass = $controllerClass;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @return string
	 */
	public function getPackage() {
		return $this->package;
	}

	/**
	 * @return string
	 */
	public function getPluginClass() {
		return $this->pluginClass;
	}

	/**
	 * @return string
	 */
	public function getControllerClass() {
		return $this->controllerClass;
	}

} 