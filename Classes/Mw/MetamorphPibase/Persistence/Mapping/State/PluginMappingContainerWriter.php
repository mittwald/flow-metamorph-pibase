<?php
namespace Mw\MetamorphPibase\Persistence\Mapping\State;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Mw.Metamorph".          *
 *                                                                        *
 * (C) 2014 Martin Helmich <m.helmich@mittwald.de>                        *
 *          Mittwald CM Service GmbH & Co. KG                             *
 *                                                                        */

use Mw\Metamorph\Domain\Event\MorphConfigurationFileModifiedEvent;
use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\Metamorph\Persistence\Mapping\State\ContainerWriterInterface;
use Mw\Metamorph\Persistence\Mapping\State\YamlStorable;
use Mw\MetamorphPibase\Domain\Model\PibaseCapableMorphConfigurationInterface;

class PluginMappingContainerWriter implements ContainerWriterInterface {

	use YamlStorable;

	public function writeMorphContainer(MorphConfiguration $morphConfiguration) {
		if ($morphConfiguration instanceof PibaseCapableMorphConfigurationInterface) {
			$this->initializeWorkingDirectory($morphConfiguration->getName());

			$pluginMappings = $morphConfiguration->getPluginMappingContainer();
			$data           = ['reviewed' => $pluginMappings->isReviewed(), 'plugins' => []];

			foreach ($pluginMappings->getPluginMappings() as $plugin) {
				$mapped = [
					'pluginClass'     => $plugin->getPluginClass(),
					'controllerClass' => $plugin->getControllerClass(),
					'package'         => $plugin->getPackage(),
					'configuration'   => $plugin->getConfiguration()
				];

				$data['plugins'][] = $mapped;
			}

			if (count($pluginMappings->getPluginMappings())) {
				$this->writeYamlFile('PluginMap', $data);
				$this->publishConfigurationFileModifiedEvent(
					new MorphConfigurationFileModifiedEvent(
						$morphConfiguration,
						$this->getWorkingFile('PluginMap.yaml'),
						'Updated plugin map.'
					)
				);
			}
		}
	}

}