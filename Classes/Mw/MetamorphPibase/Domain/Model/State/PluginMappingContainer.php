<?php
namespace Mw\MetamorphPibase\Domain\Model\State;

use Mw\Metamorph\Domain\Model\State\Reviewable;
use Mw\Metamorph\Domain\Model\State\ReviewableTrait;
use TYPO3\Flow\Annotations as Flow;

/**
 * @package    Mw\Metamorph
 * @subpackage Domain\Model\State
 *
 * @Flow\Scope("prototype")
 */
class PluginMappingContainer implements Reviewable {

	use ReviewableTrait;

	/**
	 * @var array<\Mw\MetamorphPibase\Domain\Model\State\PluginMapping>
	 */
	protected $pluginMappings = [];

	/**
	 * @return PluginMapping[]
	 */
	public function getPluginMappings() {
		return $this->pluginMappings;
	}

	/**
	 * @param string $pluginClass
	 * @return bool
	 */
	public function hasPluginMapping($pluginClass) {
		return NULL !== $this->getPluginMapping($pluginClass);
	}

	/**
	 * @param PluginMapping $classMapping
	 */
	public function addPluginMapping(PluginMapping $classMapping) {
		if (FALSE === $this->hasPluginMapping($classMapping->getPluginClass())) {
			$this->reviewed        = FALSE;
			$this->pluginMappings[] = $classMapping;
		}
	}

	/**
	 * @param string $className
	 * @return PluginMapping
	 */
	public function getPluginMapping($className) {
		return $this->getPluginMappingByFilter(
			function (PluginMapping $mapping) use ($className) {
				return $mapping->getPluginClass() === $className;
			}
		);
	}

	/**
	 * @param callable $filter
	 * @return PluginMapping
	 */
	public function getPluginMappingByFilter(callable $filter) {
		foreach ($this->pluginMappings as $classMapping) {
			if (call_user_func($filter, $classMapping)) {
				return $classMapping;
			}
		}
		return NULL;
	}

	/**
	 * @param callable $filter
	 * @return PluginMapping[]
	 */
	public function getPluginMappingsByFilter(callable $filter) {
		$found = [];
		foreach ($this->pluginMappings as $classMapping) {
			if (call_user_func($filter, $classMapping)) {
				$found[] = $classMapping;
			}
		}
		return $found;
	}

}
