<?php
namespace Heilmann\JhKestatsExport\Task;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Jonathan Heilmann <mail@jonathan-heilmann.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

// Include locallang
$GLOBALS['LANG']->includeLLFile('EXT:jh_kestats_export/Resources/Private/Language/locallang.xlf');
$GLOBALS['LANG']->includeLLFile('EXT:setup/mod/locallang.xlf');

/**
 * Extend scheduler-form for the 'jh_kestats_export' extension.
 *
 * @author	Jonathan Heilmann <mail@jonathan-heilmann.de>
 * @package	TYPO3
 * @subpackage	tx_jhkestatsexport
 */
class ExportAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {
	/**
	 * Add additional fields
	 *
	 * @param array $taskInfo Reference to the array containing the info used in the add/edit form
	 * @param object $task When editing, reference to the current task object. Null when adding.
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return array Array containing all the information pertaining to the additional fields
	 * @throws \InvalidArgumentException
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		//preset domain
		if (empty($taskInfo['domain'])) {
			if($parentObject->CMD == 'edit') {
				$taskInfo['domain'] = $task->domain;
			} else {
			   $taskInfo['domain'] = '';
			}
		}
		//preset root-id
		if (empty($taskInfo['rootid'])) {
			if($parentObject->CMD == 'edit') {
				$taskInfo['rootid'] = $task->rootid;
			} else {
			   $taskInfo['rootid'] = '';
			}
		}
		//preset rederselection "overview"
		if (empty($taskInfo['overview'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_overview == 'overview') {
					$taskInfo['overview'] = 'checked="checked"';
				} else {
					$taskInfo['overview'] = '';
				}
			} else {
			    $taskInfo['overview'] = 'checked="checked"';
			}
		}
		//preset rederselection "pageviews"
		if (empty($taskInfo['pageviews'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_pageviews == 'pageviews') {
					$taskInfo['pageviews'] = 'checked="checked"';
				} else {
					$taskInfo['pageviews'] = '';
				}
			} else {
			    $taskInfo['pageviews'] = 'checked="checked"';
			}
		}
		//preset rederselection "time"
		if (empty($taskInfo['time'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_time == 'time') {
					$taskInfo['time'] = 'checked="checked"';
				} else {
					$taskInfo['time'] = '';
				}
			} else {
			    $taskInfo['time'] = 'checked="checked"';
			}
		}
			//preset rederselection "time_merge"
			if (empty($taskInfo['time_merge'])) {
				if($parentObject->CMD == 'edit') {
					if($task->render_time_merge == 'time_merge') {
						$taskInfo['time_merge'] = 'checked="checked"';
					} else {
						$taskInfo['time_merge'] = '';
					}
				} else {
				    $taskInfo['time_merge'] = 'checked="checked"';
				}
			}
			//preset rederselection "time_visits_dayofmonth_img"
			if (empty($taskInfo['time_visits_dayofmonth_img'])) {
				if($parentObject->CMD == 'edit') {
					if($task->render_time_visits_dayofmonth_img == 'time_visits_dayofmonth_img') {
						$taskInfo['time_visits_dayofmonth_img'] = 'checked="checked"';
					} else {
						$taskInfo['time_visits_dayofmonth_img'] = '';
					}
				} else {
				    $taskInfo['time_visits_dayofmonth_img'] = '';
				}
			}
			//preset rederselection "time_visits_dayofweek_img"
			if (empty($taskInfo['time_visits_dayofweek_img'])) {
				if($parentObject->CMD == 'edit') {
					if($task->render_time_visits_dayofweek_img == 'time_visits_dayofweek_img') {
						$taskInfo['time_visits_dayofweek_img'] = 'checked="checked"';
					} else {
						$taskInfo['time_visits_dayofweek_img'] = '';
					}
				} else {
				    $taskInfo['time_visits_dayofweek_img'] = '';
				}
			}
			//preset rederselection "time_visits_hourofday_img"
			if (empty($taskInfo['time_visits_hourofday_img'])) {
				if($parentObject->CMD == 'edit') {
					if($task->render_time_visits_hourofday_img == 'time_visits_hourofday_img') {
						$taskInfo['time_visits_hourofday_img'] = 'checked="checked"';
					} else {
						$taskInfo['time_visits_hourofday_img'] = '';
					}
				} else {
				    $taskInfo['time_visits_hourofday_img'] = '';
				}
			}
		//preset rederselection "referers"
		if (empty($taskInfo['referers'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_referers == 'referers') {
					$taskInfo['referers'] = 'checked="checked"';
				} else {
					$taskInfo['referers'] = '';
				}
			} else {
			    $taskInfo['referers'] = 'checked="checked"';
			}
		}
		//preset rederselection "browser_robots"
		if (empty($taskInfo['browser_robots'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_browser_robots == 'browser_robots') {
					$taskInfo['browser_robots'] = 'checked="checked"';
				} else {
					$taskInfo['browser_robots'] = '';
				}
			} else {
			    $taskInfo['browser_robots'] = 'checked="checked"';
			}
		}
			//preset rederselection "browser_robots"
			if (empty($taskInfo['browser_robots_browsers_img'])) {
				if($parentObject->CMD == 'edit') {
					if($task->render_browser_robots_browsers_img == 'browser_robots_browsers_img') {
						$taskInfo['browser_robots_browsers_img'] = 'checked="checked"';
					} else {
						$taskInfo['browser_robots_browsers_img'] = '';
					}
				} else {
				    $taskInfo['browser_robots_browsers_img'] = '';
				}
			}
		//preset rederselection "other"
		if (empty($taskInfo['other'])) {
			if($parentObject->CMD == 'edit') {
				if($task->render_other == 'other') {
					$taskInfo['other'] = 'checked="checked"';
				} else {
					$taskInfo['other'] = '';
				}
			} else {
			    $taskInfo['other'] = '';
			}
		}


		//preset month to be rendered
		if($parentObject->CMD == 'edit') {
			if($task->month == 'lastCompleteMonth') {
				$taskInfo['month_lastCompleteMonth'] = 'checked="checked"';
		   	$taskInfo['month_thisMonth'] = '';
			} else if ($task->month == 'thisMonth') {
				$taskInfo['month_lastCompleteMonth'] = '';
		   	$taskInfo['month_thisMonth'] = 'checked="checked"';
			} else {
			   $taskInfo['month_lastCompleteMonth'] = 'checked="checked"';
			   $taskInfo['month_thisMonth'] = '';
			}
		} else {
		   $taskInfo['month_lastCompleteMonth'] = 'checked="checked" ';
		   $taskInfo['month_thisMonth'] = '';
		}
		//preset emailto
		if($parentObject->CMD == 'edit') {
			$taskInfo['emailto'] = $task->emailto;
		} else {
		   $taskInfo['emailto'] = '';
		}
		//preset language
		/*if($parentObject->CMD == 'edit') {
			$taskInfo['language'] = $this->renderLanguageSelect($task->language);
		} else {
			$taskInfo['language'] = $this->renderLanguageSelect($GLOBALS['BE_USER']->uc['lang']);
		}*/

		$additionalFields = array();
		//write the code for the field "domain"
		$fieldID = 'domain';
		$fieldCode = '<input type="input" name="tx_scheduler[domain]" id="'.$fieldID.'" value="'.$taskInfo['domain'].'">';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.domain')
		);
		//write the code for the field "rootid"
		$fieldID = 'rootid';
		$fieldCode = '<input type="input" name="tx_scheduler[rootid]" id="'.$fieldID.'" value="'.$taskInfo['rootid'].'">';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.rootid')
		);

		//write the code for the field rederselection "overview"
		$fieldID = 'overview';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_overview]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['overview'].'>';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.overview')
		);
		//write the code for the field rederselection "pageviews"
		$fieldID = 'pageviews';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_pageviews]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['pageviews'].'">';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.pageviews')
		);
		//write the code for the field rederselection "time"
		$fieldID = 'time';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_time]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['time'].'">';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.time')
		);
			//write the code for the SUB-field rederselection "time_merge"
			$fieldID = 'time_merge';
			$fieldCode = '<input type="checkbox" name="tx_scheduler[render_time_merge]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['time_merge'].'">';
			$additionalFields[$fieldID] = array(
			   'code'     => $fieldCode,
			   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.timeMerge')
			);
			//write the code for the SUB-field rederselection "time_visits_dayofmonth_img"
			$fieldID = 'time_visits_dayofmonth_img';
			$fieldCode = '<input type="checkbox" name="tx_scheduler[render_time_visits_dayofmonth_img]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['time_visits_dayofmonth_img'].'">';
			$additionalFields[$fieldID] = array(
			   'code'     => $fieldCode,
			   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.timeVisitsDayofmonthImg')
			);
			//write the code for the SUB-field rederselection "time_visits_dayofweek_img"
			$fieldID = 'time_visits_dayofweek_img';
			$fieldCode = '<input type="checkbox" name="tx_scheduler[render_time_visits_dayofweek_img]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['time_visits_dayofweek_img'].'">';
			$additionalFields[$fieldID] = array(
			   'code'     => $fieldCode,
			   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.timeVisitsDayofweekImg')
			);
			//write the code for the SUB-field rederselection "time_visits_hourofday_img"
			$fieldID = 'time_visits_hourofday_img';
			$fieldCode = '<input type="checkbox" name="tx_scheduler[render_time_visits_hourofday_img]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['time_visits_hourofday_img'].'">';
			$additionalFields[$fieldID] = array(
			   'code'     => $fieldCode,
			   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.timeVisitsHourofdayImg')
			);
		//write the code for the field rederselection "referers"
		$fieldID = 'referers';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_referers]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['referers'].'>';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.referers')
		);
		//write the code for the field rederselection "browser_robots"
		$fieldID = 'browser_robots';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_browser_robots]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['browser_robots'].'>';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.browserRobots')
		);
			//write the code for the SUB-field rederselection "browser_robots_browsers_img"
			$fieldID = 'browser_robots_browsers_img';
			$fieldCode = '<input type="checkbox" name="tx_scheduler[render_browser_robots_browsers_img]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['browser_robots_browsers_img'].'">';
			$additionalFields[$fieldID] = array(
			   'code'     => $fieldCode,
			   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.browserRobotsBrowsersImg')
			);
		//write the code for the field rederselection "other"
		$fieldID = 'other';
		$fieldCode = '<input type="checkbox" name="tx_scheduler[render_other]" id="'.$fieldID.'" value="'.$fieldID.'" '.$taskInfo['other'].'>';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.other')
		);

		//write the code for the field "selectmonth"
		$fieldID = 'month';
		$fieldCode  = '<input type="radio" name="tx_scheduler[month]" id="'.$fieldID.'"  '.$taskInfo['month_lastCompleteMonth'].' value="lastCompleteMonth"> '.$GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.monthLast').'</br>';
		$fieldCode .= '<input type="radio" name="tx_scheduler[month]" id="'.$fieldID.'"  '.$taskInfo['month_thisMonth'].' value="thisMonth"> '.$GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.monthThis');
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.month')
		);
		//write the code for the field "emailto"
		$fieldID = 'emailto';
		$fieldCode = '<input type="input" name="tx_scheduler[emailto]" id="'.$fieldID.'" value="'.$taskInfo['emailto'].'">';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.mailTo')
		);
		//write the code for the field "language"
		/*$fieldID = 'language';
		$fieldCode = '<select name="tx_scheduler[language]" id="'.$fieldID.'">'.$taskInfo['language'].'</select>';
		$additionalFields[$fieldID] = array(
		   'code'     => $fieldCode,
		   'label'    => $GLOBALS['LANG']->getLL('tx_jhkestatsexport_new.form.language')
		);*/

		return $additionalFields;
	}

	/**
	 * Validate additional fields
	 *
	 * @param array $submittedData Reference to the array containing the data submitted by the user
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return boolean True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
		$submittedData['domain'] = trim($submittedData['domain']);
		$submittedData['rootid'] = trim($submittedData['rootid']);
		$submittedData['render_overview'] = trim($submittedData['render_overview']);
		$submittedData['render_pageviews'] = trim($submittedData['render_pageviews']);
		$submittedData['render_time'] = trim($submittedData['render_time']);
			$submittedData['render_time_merge'] = trim($submittedData['render_time_merge']);
			$submittedData['render_time_visits_dayofmonth_img'] = trim($submittedData['render_time_visits_dayofmonth_img']);
			$submittedData['render_time_visits_dayofweek_img'] = trim($submittedData['render_time_visits_dayofweek_img']);
			$submittedData['render_time_visits_hourofday_img'] = trim($submittedData['render_time_visits_hourofday_img']);
		$submittedData['render_referers'] = trim($submittedData['render_referers']);
		$submittedData['render_browser_robots'] = trim($submittedData['render_browser_robots']);
			$submittedData['render_browser_robots_browsers_img'] = trim($submittedData['render_browser_robots_browsers_img']);
		$submittedData['render_other'] = trim($submittedData['render_other']);
		$submittedData['month'] = trim($submittedData['month']);
		$submittedData['emailto'] = trim($submittedData['emailto']);
		//$submittedData['language'] = trim($submittedData['language']);
		return true;
	}

	/**
	 * Save additional field in task
	 *
	 * @param array $submittedData Contains data submitted by the user
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the current task object
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->domain = $submittedData['domain'];
		$task->rootid = $submittedData['rootid'];
		$task->render_overview = $submittedData['render_overview'];
		$task->render_pageviews = $submittedData['render_pageviews'];
		$task->render_time = $submittedData['render_time'];
			$task->render_time_merge = $submittedData['render_time_merge'];
			$task->render_time_visits_dayofmonth_img = $submittedData['render_time_visits_dayofmonth_img'];
			$task->render_time_visits_dayofweek_img = $submittedData['render_time_visits_dayofweek_img'];
			$task->render_time_visits_hourofday_img = $submittedData['render_time_visits_hourofday_img'];
		$task->render_referers = $submittedData['render_referers'];
		$task->render_browser_robots = $submittedData['render_browser_robots'];
			$task->render_browser_robots_browsers_img = $submittedData['render_browser_robots_browsers_img'];
		$task->render_other = $submittedData['render_other'];
		$task->month = $submittedData['month'];
		$task->emailto = $submittedData['emailto'];
		//$task->language = $submittedData['language'];
	}

	/**
	 * Return a select with available languages
	 * Source: TYPO3\CMS\Setup\Controller\SetupModuleController
	 *
	 * @return string Complete select as HTML string or warning box if something went wrong.
	 */
	public function renderLanguageSelect($selectedLanguage) {
		$languageOptions = array();
		// Compile the languages dropdown
		$langDefault = $GLOBALS['LANG']->getLL('lang_default', TRUE);
		$languageOptions[$langDefault] = '<option value=""' . ($selectedLanguage === '' ? ' selected="selected"' : '') . '>' . $langDefault . '</option>';
		// Traverse the number of languages
		/** @var $locales \TYPO3\CMS\Core\Localization\Locales */
		$locales = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Locales');
		$languages = $locales->getLanguages();
		foreach ($languages as $locale => $name) {
			if ($locale !== 'default') {
				$defaultName = isset($GLOBALS['LOCAL_LANG']['default']['lang_' . $locale]) ? $GLOBALS['LOCAL_LANG']['default']['lang_' . $locale][0]['source'] : $name;
				$localizedName = $GLOBALS['LANG']->getLL('lang_' . $locale, TRUE);
				if ($localizedName === '') {
					$localizedName = htmlspecialchars($name);
				}
				$localLabel = '  -  [' . htmlspecialchars($defaultName) . ']';
				$available = is_dir(PATH_typo3conf . 'l10n/' . $locale) ? TRUE : FALSE;
				if ($available) {
					$languageOptions[$defaultName] = '<option value="' . $locale . '"' . ($selectedLanguage === $locale ? ' selected="selected"' : '') . '>' . $localizedName . $localLabel . '</option>';
				}
			}
		}
		ksort($languageOptions);
		return implode('', $languageOptions);
	}


}
?>