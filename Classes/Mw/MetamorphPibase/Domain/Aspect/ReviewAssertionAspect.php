<?php
namespace Mw\MetamorphPibase\Domain\Aspect;

use Mw\Metamorph\Domain\Model\MorphConfiguration;
use Mw\Metamorph\Domain\Model\State\ReviewableTrait;
use Mw\MetamorphPibase\Domain\Model\PibaseCapableMorphConfigurationInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;

/**
 * Aspect that asserts that the pibase plugin mapping is reviewed before a
 * migration step that requires it is executed.
 *
 * Note that this aspect works the other way than Metamorph's base
 * `ReviewAssertionAspect` class. There, class/package review is mandatory and
 * can be skipped with appropriate annotations. Since this package is itself
 * an extension to Metamorph, review of the plugin mapping cannot be mandatory
 * for all steps.
 *
 * @package    Mw\MetamorphPibase
 * @subpackage Domain\Aspect
 *
 * @Flow\Aspect
 */
class ReviewAssertionAspect {

	/**
	 * @Flow\Pointcut("within(Mw\Metamorph\Transformation\Transformation) && classAnnotatedWith(Mw\MetamorphPibase\Annotations\RequirePluginReview) && method(.*->execute())")
	 */
	public function pluginMapReviewPointcut() { }

	/**
	 * @param JoinPointInterface $joinPoint
	 * @throws \Mw\Metamorph\Domain\Exception\HumanInterventionRequiredException
	 *
	 * @Flow\Before("Mw\MetamorphPibase\Domain\Aspect\ReviewAssertionAspect->pluginMapReviewPointcut")
	 */
	public function validatePluginMapReview(JoinPointInterface $joinPoint) {
		$this->assertReviewableIsReviewed(
			$joinPoint,
			function (MorphConfiguration $config) {
				if ($config instanceof PibaseCapableMorphConfigurationInterface) {
					return $config->getPluginMappingContainer();
				} else {
					return TRUE;
				}
			}
		);
	}

	private function assertReviewableIsReviewed(JoinPointInterface $joinPoint, callable $getter) {
		$configuration = array_values($joinPoint->getMethodArguments())[0];
		if ($configuration instanceof MorphConfiguration) {
			/** @var ReviewableTrait $reviewable */
			$reviewable = call_user_func_array($getter, [$configuration]);
			$reviewable->assertReviewed();
		}
	}

}