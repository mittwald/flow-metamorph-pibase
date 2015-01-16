<?php
namespace Mw\MetamorphPibase\Domain\Model;

use Mw\MetamorphPibase\Domain\Model\State\PluginMappingContainer;

interface PibaseCapableMorphConfigurationInterface {

	/**
	 * @return PluginMappingContainer
	 */
	public function getPluginMappingContainer();

}