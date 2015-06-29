<?php
namespace Heilmann\JhKestatsExport\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Jonathan Heilmann <mail@jonathan-heilmann.de>
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

/**
 * FilelistController
 */
class FilelistController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * filelistRepository
	 *
	 * @var \Heilmann\JhKestatsExport\Domain\Repository\FilelistRepository
	 * @inject
	 */
	protected $filelistRepository = NULL;

	/**
	 * @var int Current page
	 */
	protected $pageId = NULL;

	protected $exportService = NULL;

	/**
	 * Action initializer
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$this->pageId = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$filelists = $this->filelistRepository->findAll();
		$this->view->assign('filelists', $filelists);
	}

	/**
	 * action new
	 *
	 * @return void
	 */
	public function newAction() {
		$formPrefill = array();
		$formStyle = array();
		// Get settings from BE-User session
		$modSettings = $GLOBALS['BE_USER']->getModuleData($this->MCONF['name'], 'ses');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($modSettings);
		if (!empty($modSettings)) {
			$modSettingsForm = $modSettings;
			foreach ($modSettingsForm as $key => $value) {
				if (!empty($value)) {
					$formPrefill['checkbox'][$key] = TRUE;
				} else {
					$formPrefill['checkbox'][$key] = FALSE;
				}
			}
		} else {
			// Default settings
			$formPrefill['checkbox']['overview'] = TRUE;
			$formPrefill['checkbox']['pageviews'] = TRUE;
			$formPrefill['checkbox']['time'] = TRUE;
			$formPrefill['checkbox']['referers'] = TRUE;
			$formPrefill['checkbox']['browserRobots'] = TRUE;
			$formPrefill['checkbox']['browserRobotsBrowsersImg'] = FALSE;
			$formPrefill['checkbox']['other'] = FALSE;
			$formPrefill['checkbox']['emailto'] = FALSE;
		}
		// Get domains
		$domains = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'sys_domain', 'hidden=0 AND pid=' . $this->pageId);
		if (empty($domains)) {
			$hostname = \TYPO3\CMS\Core\Utility\GeneralUtility::getHostname();
			$formPrefill['domain'] = array($hostname => $hostname);
		} else {
			foreach ($domains as $domain) {
				$formPrefill['domain'][$domain['domainName']] = $domain['domainName'];
			}
		}
		// Get months
		$row_first = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_kestats_statdata', 'type=\'pages\' AND category=\'pages\' AND year>0', '', 'uid');
		$row_last = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('*', 'tx_kestats_statdata', 'type=\'pages\' AND category=\'pages\' AND year>0', '', 'uid DESC');
		$fromToArray = array();
		$fromToArray['from_month'] = $row_first['month'];
		$fromToArray['from_year'] = $row_first['year'];
		$fromToArray['to_month'] = $row_last['month'];
		$fromToArray['to_year'] = $row_last['year'];
		for ($i = $fromToArray['from_year']; $i <= $fromToArray['to_year']; $i++) {
			for ($j = 1; $j <= 12; $j++) {
				if ($i == $fromToArray['from_year'] and $j == 1) {
					$j = $fromToArray['from_month'];
				}
				if ($i == $fromToArray['to_year'] and $j == $fromToArray['to_month']) {
					$formPrefill['month'][date('Y-m', mktime(0, 0, 0, $j, 10, $i))] = date('F Y', mktime(0, 0, 0, $j, 10, $i));
					//$option .= '<option value="'.date("Y-m", mktime(0, 0, 0, $j, 10, $i)).'" selected="selected">'.date("F Y", mktime(0, 0, 0, $j, 10, $i)).'</option>';
					break;
				}
				$formPrefill['month'][date('Y-m', mktime(0, 0, 0, $j, 10, $i))] = date('F Y', mktime(0, 0, 0, $j, 10, $i));
			}
		}
		// Get email
		$formPrefill['mailTo'] = $GLOBALS['BE_USER']->user['email'];
		if ($formPrefill['time'] === FALSE) {
			$formStyle['timeSubelements'] = 'display:none;';
		}
		if ($formPrefill['browserRobots'] === FALSE) {
			$formStyle['browserRobotsSubelements'] = 'display:none;';
		}
		$this->view->assign('formStyle', $formStyle);
		$this->view->assign('formPrefill', $formPrefill);
	}

	/**
	 * action create
	 *
	 * @return void
	 */
	public function createAction() {
		$post = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_jhkestatsexport_web_jhkestatsexportlist');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($post);
		// Get settings for the render-process
		if (!empty($post)) {
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
		$this->exportService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Heilmann\\JhKestatsExport\\Service\\ExportService');
		$pdfcontent = $this->exportService->renderStatistics($this->pageId, $post);
		$filename = $this->exportService->renderpdf($pdfcontent, $post);
		// Send email to given mail-address with attachment
		if (!empty($post)) {
			if (!empty($post['mailTo'])) {
				$mailToArray = array();
				$mailToArray = explode(',', $post['mailTo']);
				foreach ($mailToArray as $mailTo) {
					$mailTo = trim($mailTo);
					if (filter_var($mailTo, FILTER_VALIDATE_EMAIL)) {
						$this->exportService->sendEmail($mailTo, \TYPO3\CMS\Core\Utility\GeneralUtility::getHostname(), $filename);
					} else {

					}
				}
			} else {

			}
		}
		// Reset $GLOBALS['LANG']->lang
		/*$GLOBALS['LANG']->lang = $storedLang['lang'];
						\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']->lang);*/

		$this->addFlashMessage('The pdf has been created.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \Heilmann\JhKestatsExport\Domain\Model\Filelist $filelist
	 * @return void
	 */
	public function deleteAction(\Heilmann\JhKestatsExport\Domain\Model\Filelist $filelist) {
		// Unlink PDF
		if (is_file(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('uploads/tx_jhkestatsexport/') . $filelist->getFilename())) {
			unlink(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('uploads/tx_jhkestatsexport/') . $filelist->getFilename());
		}
		$this->addFlashMessage('The pdf has been deleted.', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
		$this->filelistRepository->remove($filelist);
		$this->redirect('list');
	}

}