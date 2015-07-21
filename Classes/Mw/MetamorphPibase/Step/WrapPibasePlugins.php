<?php
namespace Mw\MetamorphPibase\Step;

use Mw\Metamorph\Domain\Model\Definition\ClassDefinition;
use Mw\Metamorph\Domain\Model\Definition\ClassDefinitionContainer;
use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\Metamorph\Domain\Service\MorphExecutionState;
use Mw\Metamorph\Transformation\AbstractTransformation;
use Mw\MetamorphPibase\Domain\Model\PibaseCapableMorphConfigurationInterface;
use Mw\MetamorphPibase\Annotations as MetamorphPibase;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Package\PackageManagerInterface;
use TYPO3\Fluid\View\StandaloneView;

/**
 * Special step class that creates a wrapping controller for each migrated plugin.
 *
 * @package Mw\MetamorphPibase
 * @subpackage Step
 *
 * @MetamorphPibase\RequirePluginReview
 */
class WrapPibasePlugins extends AbstractTransformation {

	/**
	 * @var ClassDefinitionContainer
	 * @Flow\Inject
	 */
	protected $classDefinitionContainer;

	/**
	 * @var PackageManagerInterface
	 * @Flow\Inject
	 */
	protected $packageManager;

	public function execute(MorphConfiguration $configuration, MorphExecutionState $state) {
		if ($configuration instanceof PibaseCapableMorphConfigurationInterface) {
			$view = new StandaloneView();
			$view->setTemplatePathAndFilename(
				'resource://Mw.MetamorphPibase/Private/Views/Standalone/PluginWrapperController.php.fluid'
			);

			$pluginContainer = $configuration->getPluginMappingContainer();
			foreach ($pluginContainer->getPluginMappings() as $plugin) {
				$pluginClass     = $this->classDefinitionContainer->get($plugin->getPluginClass());
				$controllerClass = ClassDefinition::fromFqdn($plugin->getControllerClass());

				$view->assign('plugin', $pluginClass);
				$view->assign('controller', $controllerClass);
				$view->assign('configuration', $plugin->getConfiguration());

				$package = $this->packageManager->getPackage($plugin->getPackage());
				$targetFile = $package->getClassesPath() . str_replace('\\', '/', $plugin->getControllerClass()) . '.php';

				$code = $view->render();

				// Code might contain whitespaces before open tag.
				$code = trim($code);

				file_put_contents($targetFile, $code);

				$this->classDefinitionContainer->add($controllerClass);
				$this->log('Created controller <comment>' . $controllerClass->getFullyQualifiedName() . '</comment>.');
			}

		}
	}

}