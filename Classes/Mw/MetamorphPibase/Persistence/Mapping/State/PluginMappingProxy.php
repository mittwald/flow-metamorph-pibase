<?php
namespace Mw\MetamorphPibase\Persistence\Mapping\State;

use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\MetamorphPibase\Domain\Model\State\PluginMapping;

class PluginMappingProxy extends PluginMapping {

	public function __construct($pluginClass, array $data, MorphConfiguration $configuration) {
		$this->pluginClass = $pluginClass;
		$this->package = $data['package'];
		$this->configuration = $data['configuration'];
		$this->controllerClass = $data['controllerClass'];
	}

}