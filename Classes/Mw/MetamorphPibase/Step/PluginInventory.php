<?php
namespace Mw\MetamorphPibase\Step;

use Mw\Metamorph\Domain\Model\Definition\ClassDefinition;
use Mw\Metamorph\Domain\Model\Definition\ClassDefinitionContainer;
use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\Metamorph\Domain\Repository\MorphConfigurationRepository;
use Mw\Metamorph\Domain\Service\MorphExecutionState;
use Mw\Metamorph\Transformation\AbstractTransformation;
use Mw\MetamorphPibase\Domain\Model\PibaseCapableMorphConfigurationInterface;
use Mw\MetamorphPibase\Domain\Model\State\PluginMapping;
use TYPO3\Flow\Annotations as Flow;

class PluginInventory extends AbstractTransformation {

	/**
	 * @var ClassDefinitionContainer
	 * @Flow\Inject
	 */
	protected $classDefinitionContainer;

	/**
	 * @var MorphConfigurationRepository
	 * @Flow\Inject
	 */
	protected $morphConfigurationRepository;

	public function execute(MorphConfiguration $configuration, MorphExecutionState $state) {
		if ($configuration instanceof PibaseCapableMorphConfigurationInterface) {
			$pluginContainer = $configuration->getPluginMappingContainer();
			if (count($pluginContainer->getPluginMappings()) === 0) {
				$pluginClasses = $this->classDefinitionContainer->findByFact('isPibasePlugin', TRUE);

				foreach ($pluginClasses as $pluginClass) {
					$mapping = new PluginMapping(
						$pluginClass->getFullyQualifiedName(),
						$this->getControllerClassForPluginClass($pluginClass),
						$pluginClass->getClassMapping()->getPackage(),
						[]
					);

					$pluginContainer->addPluginMapping($mapping);
				}

				$this->morphConfigurationRepository->update($configuration);
			}
		}
	}

	private function getControllerClassForPluginClass(ClassDefinition $pluginClass) {
		$namespace           = str_replace('.', '\\', $pluginClass->getClassMapping()->getPackage()) . '\\';
		$controllerNamespace = $namespace . 'Controller\\';

		$controllerClass = str_replace('Plugin', 'Controller', $pluginClass->getFullyQualifiedName());

		if (strstr($controllerClass, $controllerNamespace) === FALSE) {
			$controllerClass = str_replace($namespace, $controllerNamespace, $controllerClass);
		}

		return $controllerClass;
	}

}