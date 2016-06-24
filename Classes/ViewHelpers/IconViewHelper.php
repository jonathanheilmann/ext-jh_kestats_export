<?php
namespace Heilmann\JhKestatsExport\ViewHelpers;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Jonathan Heilmann <mail@jonathan-heilmann.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class IconViewHelper
 * @package Heilmann\JhKestatsExport\ViewHelpers
 */
class IconViewHelper extends AbstractViewHelper
{

    /**
     * @param string $identifier
     * @param string $size
     * @param null $overlay
     * @param string $state
     * @param string $alternativeMarkupIdentifier
     * @return string
     */
    public function render($identifier, $size = 'small', $overlay = null, $state = 'default', $alternativeMarkupIdentifier = null)
    {
        if (VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getNumericTypo3Version()) < 7000000)
        {
            /** @var \TYPO3\CMS\Fluid\ViewHelpers\Be\Buttons\IconViewHelper $iconViewHelper */
            $iconViewHelper = $this->objectManager->get('TYPO3\\CMS\\Fluid\\ViewHelpers\\Be\\Buttons\\IconViewHelper');
            $iconViewHelper->setRenderingContext($this->renderingContext);
            return $iconViewHelper->render('', $identifier);
        } else
        {
            /** @var \TYPO3\CMS\Core\ViewHelpers\IconViewHelper $iconViewHelper */
            $iconViewHelper = $this->objectManager->get('TYPO3\\CMS\\Core\\ViewHelpers\\IconViewHelper');
            $iconViewHelper->setRenderingContext($this->renderingContext);
            return $iconViewHelper->render($identifier, $size, $overlay, $state, $alternativeMarkupIdentifier);
        }
    }
}