<?php
namespace Mw\MetamorphPibase\Persistence\Mapping\State;

use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\Metamorph\Persistence\Mapping\State\YamlStorable;
use Mw\MetamorphPibase\Domain\Model\State\PluginMappingContainer;
use TYPO3\Flow\Annotations as Flow;

class PluginMappingContainerProxy extends PluginMappingContainer {

	use YamlStorable;

	public function __construct(MorphConfiguration $configuration) {
		$this->configuration = $configuration;
	}

	public function initializeObject() {
		$this->initializeWorkingDirectory($this->configuration->getName());

		$data           = $this->readYamlFile('PluginMap', FALSE);
		$pluginMappings = [];

		foreach ($this->getArrayProperty($data, 'plugins', []) as $pluginData) {
			$pluginMappings[] = new PluginMappingProxy($pluginData['pluginClass'], $pluginData, $this->configuration);
		}

		$this->pluginMappings = $pluginMappings;
		$this->reviewed       = $this->getArrayProperty($data, 'reviewed', FALSE);
	}

}