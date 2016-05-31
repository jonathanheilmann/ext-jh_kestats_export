<?php
namespace Heilmann\JhKestatsExport\Task;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2016 Jonathan Heilmann <mail@jonathan-heilmann.de>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Task 'Statistics export' for the 'jh_kestats_export' extension.
 *
 * @author    Jonathan Heilmann <mail@jonathan-heilmann.de>
 * @package    TYPO3
 * @subpackage    tx_jhkestatsexport
 */
class ExportTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{

    /**
     * @var string
     */
    protected $extName = 'JhKestatsExport';

    /**
     * @var string
     */
    protected $extKey = 'jh_kestats_export';

    /**
     *
     *
     * @return boolean TRUE on successful execution, FALSE on error
     */
    public function execute()
    {
        $id = intval($this->rootid);
        if (is_int($id))
        {
            $post = array();
            $post['domain'] = $this->domain;

            $post['overview'] = $this->render_overview;
            $post['pageviews'] = $this->render_pageviews;
            $post['time'] = $this->render_time;
            $post['timeMerge'] = $this->render_time_merge;
            $post['timeVisitsDayofmonthImg'] = $this->render_time_visits_dayofmonth_img;
            $post['timeVisitsDayofweekImg'] = $this->render_time_visits_dayofweek_img;
            $post['timeVisitsHourofdayImg'] = $this->render_time_visits_hourofday_img;
            $post['referers'] = $this->render_referers;
            $post['browserRobots'] = $this->render_browser_robots;
            $post['browserRobotsBrowsersImg'] = $this->render_browser_robots_browsers_img;
            $post['other'] = $this->render_other;

            $post['month'] = $this->month;
            $post['mailTo'] = $this->emailto;

            $post['month'] = $post['month'] == 'lastCompleteMonth' ? date("Y-m", strtotime("last month")) : date("Y-m");
            $post['lang'] = $this->language;

            // Change $GLOBALS['LANG'] to render pdf in selected language
            /*$storedLang = array(
                'lang'	=>	$GLOBALS['LANG']->lang,
            );
            if (isset($post['lang']) && $post['lang'] == '') $post['lang'] = 'default';
            $GLOBALS['LANG']->lang = (isset($post['lang']) ? $post['lang'] : $storedLang['lang']);*/
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']);

            if (($post['timeVisitsDayofmonthImg'] || $post['timeVisitsDayofweekImg'] || $post['timeVisitsHourofdayImg'] || $post['browserRobotsBrowsersImg'] ) && !ini_get('allow_url_fopen'))
            {
                $GLOBALS['BE_USER']->simplelog('Please allow "allow_url_fopen" in php ini to enable embedding of images in pdf', $this->extKey, '1');
            }

            // instantiate the shared library of ke_stats
            /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
            $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            /** @var \Heilmann\JhKestatsExport\Service\ExportService $exportService */
            $exportService = $objectManager->get('Heilmann\\JhKestatsExport\\Service\\ExportService');

            $pdfcontent = $exportService->renderStatistics($id, $post);
            $filename = $exportService->renderpdf($pdfcontent, $post);

            //send email to given mail-address with attachment
            if (!empty($post['mailTo']))
            {
                $mailToArray = explode(',', $post['mailTo']);
                foreach ($mailToArray as $mailTo) 
                {
                    $mailTo = trim($mailTo);
                    if (filter_var($mailTo, FILTER_VALIDATE_EMAIL)) 
                    {
                        $exportService->sendEmail($mailTo, $post['domain'], $filename);
                        $GLOBALS['BE_USER']->simplelog('email send to "' . $mailTo . '"', $this->extKey, '0');
                    } else 
                    {
                        $GLOBALS['BE_USER']->simplelog('no valid email-address "' . $mailTo . '"', $this->extKey, '2');
                    }
                }
            } else {
                $GLOBALS['BE_USER']->simplelog('no email set', $this->extKey, '0');
            }

            // Reset $GLOBALS['LANG']->lang
            //$GLOBALS['LANG']->lang = $storedLang['lang'];
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($GLOBALS['LANG']->lang);

            return true;
        } else
        {
            $GLOBALS['BE_USER']->simplelog('no root-id set', $this->extKey, '2');
            return false;
        }
    }

    /**
     *
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        return LocalizationUtility::translate('tx_jhkestatsexport_new.form.domain',
            $this->extName) . ': ' . $this->domain . ' | ' . LocalizationUtility::translate('tx_jhkestatsexport_new.form.mailTo',
            $this->extName) . ': ' . $this->emailto;
    }
}