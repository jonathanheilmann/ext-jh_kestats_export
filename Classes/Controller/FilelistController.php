<?php
namespace Heilmann\JhKestatsExport\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014-2016 Jonathan Heilmann <mail@jonathan-heilmann.de>
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
use Heilmann\JhKestatsExport\Domain\Model\Filelist;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FilelistController
 */
class FilelistController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

	/**
	 * filelistRepository
	 *
	 * @var \Heilmann\JhKestatsExport\Domain\Repository\FilelistRepository
	 * @inject
	 */
	protected $filelistRepository = null;

	/**
	 * @var int Current page
	 */
	protected $pageId = null;

	/**
	 * @var null
	 */
	protected $exportService = null;

	/**
	 * Action initializer
	 *
	 * @return void
	 */
	protected function initializeAction()
    {
		$this->pageId = (int)GeneralUtility::_GP('id');
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction()
    {
		$filelists = $this->filelistRepository->findAll();
		$this->view->assign('filelists', $filelists);
	}

	/**
	 * action new
	 *
	 * @return void
	 */
	public function newAction()
    {
		$formPrefill = array();
		$formStyle = array();
		// Get settings from BE-User session
		$modSettings = $GLOBALS['BE_USER']->getModuleData($this->MCONF['name'], 'ses');
		if (!empty($modSettings))
        {
			$modSettingsForm = $modSettings;
			foreach ($modSettingsForm as $key => $value)
                $formPrefill['checkbox'][$key] = !empty($value) ? true : false;
		} else
        {
			// Default settings
			$formPrefill['checkbox']['overview'] = true;
			$formPrefill['checkbox']['pageviews'] = true;
			$formPrefill['checkbox']['time'] = true;
			$formPrefill['checkbox']['referers'] = true;
			$formPrefill['checkbox']['browserRobots'] = true;
			$formPrefill['checkbox']['browserRobotsBrowsersImg'] = false;
			$formPrefill['checkbox']['other'] = false;
			$formPrefill['checkbox']['emailto'] = false;
		}
		// Get domains
		$domains = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_domain', 'hidden=0 AND pid=' . $this->pageId);
		if (empty($domains))
        {
			$hostname = GeneralUtility::getHostname();
			$formPrefill['domain'] = array($hostname => $hostname);
		} else
        {
			foreach ($domains as $domain)
				$formPrefill['domain'][$domain['domainName']] = $domain['domainName'];
		}
		// Get months
		$row_first = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_kestats_statdata', 'type=\'pages\' AND category=\'pages\' AND year>0', '', 'uid');
		$row_last = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_kestats_statdata', 'type=\'pages\' AND category=\'pages\' AND year>0', '', 'uid DESC');
		$fromToArray = array();
		$fromToArray['from_month'] = $row_first['month'];
		$fromToArray['from_year'] = $row_first['year'];
		$fromToArray['to_month'] = $row_last['month'];
		$fromToArray['to_year'] = $row_last['year'];
		for ($i = $fromToArray['from_year']; $i <= $fromToArray['to_year']; $i++)
        {
			for ($j = 1; $j <= 12; $j++)
            {
				if ($i == $fromToArray['from_year'] and $j == 1)
					$j = $fromToArray['from_month'];

				if ($i == $fromToArray['to_year'] and $j == $fromToArray['to_month'])
                {
					$formPrefill['month'][date('Y-m', mktime(0, 0, 0, $j, 10, $i))] = date('F Y', mktime(0, 0, 0, $j, 10, $i));
					//$option .= '<option value="'.date("Y-m", mktime(0, 0, 0, $j, 10, $i)).'" selected="selected">'.date("F Y", mktime(0, 0, 0, $j, 10, $i)).'</option>';
					break;
				}
				$formPrefill['month'][date('Y-m', mktime(0, 0, 0, $j, 10, $i))] = date('F Y', mktime(0, 0, 0, $j, 10, $i));
			}
		}
		// Get email
		$formPrefill['mailTo'] = $GLOBALS['BE_USER']->user['email'];
		if ($formPrefill['time'] === false)
			$formStyle['timeSubelements'] = 'display:none;';

		if ($formPrefill['browserRobots'] === false)
			$formStyle['browserRobotsSubelements'] = 'display:none;';

		$this->view->assign('formStyle', $formStyle);
		$this->view->assign('formPrefill', $formPrefill);
	}

	/**
	 * action create
	 *
	 * @return void
	 */
	public function createAction()
    {
		$post = GeneralUtility::_GP('tx_jhkestatsexport_web_jhkestatsexportlist');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($post);
		// Get settings for the render-process
		if (!empty($post))
        {
			// Save settings for this BE-User session
			$GLOBALS['BE_USER']->pushModuleData($this->MCONF['name'], $post);
		}
		// Change $GLOBALS['LANG'] to render pdf in selected language
		/*$storedLang = array(
							'lang'	=>	$GLOBALS['LANG']->lang,
						);
						if (isset($post['lang'] && $post['lang'] == '')) $post['lang'] = 'default';
						$GLOBALS['LANG']->lang = (isset($post['lang']) ? $post['lang'] : $storedLang['lang']);
						\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']->lang);*/

		// Construct exportService
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var \Heilmann\JhKestatsExport\Service\ExportService $exportService */
        $exportService = $objectManager->get('Heilmann\\JhKestatsExport\\Service\\ExportService');

		$pdfcontent = $exportService->renderStatistics($this->pageId, $post);
		$filename = $exportService->renderpdf($pdfcontent, $post);

		// Send email to given mail-address with attachment
        if (is_array($post) && isset($post['mailTo']) && !empty($post['mailTo']))
        {
            $mailToArray = explode(',', $post['mailTo']);
            foreach ($mailToArray as $mailTo)
            {
                $mailTo = trim($mailTo);
                if (filter_var($mailTo, FILTER_VALIDATE_EMAIL))
                    $exportService->sendEmail($mailTo, GeneralUtility::getHostname(), $filename);
            }
        }
		// Reset $GLOBALS['LANG']->lang
		/*$GLOBALS['LANG']->lang = $storedLang['lang'];
						\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']->lang);*/

		$this->addFlashMessage('The pdf has been created.', '', AbstractMessage::OK);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Filelist $filelist
	 * @return void
	 */
	public function deleteAction(Filelist $filelist) {
		// Unlink PDF
		if (is_file(GeneralUtility::getFileAbsFileName('uploads/tx_jhkestatsexport/') . $filelist->getFilename()))
			unlink(GeneralUtility::getFileAbsFileName('uploads/tx_jhkestatsexport/') . $filelist->getFilename());

		$this->addFlashMessage('The pdf has been deleted.', '', AbstractMessage::OK);
		$this->filelistRepository->remove($filelist);
		$this->redirect('list');
	}

}