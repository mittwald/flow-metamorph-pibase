<?php
namespace Mw\MetamorphPibase\Domain\Aspect;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;

/**
 * @package    Mw\MetamorphPibase
 * @subpackage Domain\Aspect
 *
 * @Flow\Introduce(
 *     "class(Mw\Metamorph\Domain\Model\MorphConfiguration)",
 *     interfaceName="Mw\MetamorphPibase\Domain\Model\PibaseCapableMorphConfigurationInterface")
 * @Flow\Aspect
 */
class MorphConfigurationInterfaceIntroductionAspect {

	/**
	 * @param JoinPointInterface $joinPoint
	 * @return null
	 *
	 * @Flow\Around("method(Mw\Metamorph\Domain\Model\MorphConfiguration->getPluginMappingContainer())")
	 */
	public function getPluginMappingContainerImplementation(JoinPointInterface $joinPoint) {
		$joinPoint->getAdviceChain()->proceed($joinPoint);

		if (is_callable([$joinPoint->getProxy(), 'getContainer'])) {
			/** @noinspection PpUndefinedMethodInspection */
			$result = $joinPoint->getProxy()->getContainer('plugins');
			return $result;
		}

		return NULL;
	}

} 