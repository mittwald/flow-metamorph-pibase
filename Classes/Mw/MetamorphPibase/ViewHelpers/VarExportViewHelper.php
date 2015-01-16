<?php
namespace Mw\MetamorphPibase\ViewHelpers;

use TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper;

class VarExportViewHelper extends AbstractViewHelper {

	public function render() {
		$content = $this->renderChildren();
		return var_export($content, TRUE);
	}

}