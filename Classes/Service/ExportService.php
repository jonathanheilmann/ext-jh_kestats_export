<?php
namespace Heilmann\JhKestatsExport\Service;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Library for the 'jh_kestats_export' extension.
 *
 * @author    Jonathan Heilmann <mail@jonathan-heilmann.de>
 * @package    TYPO3
 * @subpackage    tx_jhkestatsexport
 */
class ExportService
{

    /**
     * @var string
     */
    protected $uploadfolderPath = 'uploads/tx_jhkestatsexport/';

    /**
     * instantiate the shared library of ke_stats
     *
     * @var \tx_kestats_lib
     * @inject
     */
    protected $kestatslib = null;

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var array
     */
    protected $post = array();

    /**
     * @var int
     */
    protected $actTime = 0;

    /**
     * @var array
     */
    protected $fromToArray = array();

    /**
     * @var int
     */
    protected $elementsCounter = 0;

    /**
     * @var array
     */
    protected $indexArray = array();

    /**
     * @var string
     */
    protected $decimalChar = '.';

    /**
     * @var array
     */
    protected $overviewPageData = array();

    /**
     * ExportService constructor.
     */
    function __construct()
    {
        GeneralUtility::requireOnce(ExtensionManagementUtility::extPath('ke_stats', 'inc/constants.inc.php'));
        // Include locallang
        $GLOBALS['LANG']->includeLLFile('EXT:jh_kestats_export/Resources/Private/Language/locallang_exportService.xlf');
        // Check if uploadfolder exist, otherwise create
        if (!file_exists($this->uploadfolderPath)) {
            GeneralUtility::mkdir(GeneralUtility::getFileAbsFileName($this->uploadfolderPath));
        }
    }

    /**
     * Render the statistics as html-content.
     *
     * @param    string $id : id of the website-root
     * @param    array $post : _POST data
     * @return    string        html-formated statistics
     */
    public function renderStatistics($id, $post)
    {
        $this->id = $id;
        $this->post = $post;
        $this->actTime = time();
        $this->setTimeformat();

        //write fromToArray
        $this->fromToArray = $this->fromToArrayFunc($this->post['month']);

        // introduce the backend module to the shared library
        $this->kestatslib->backendModule_obj = $this;

        $this->overviewPageData = $this->refreshOverviewPageData($id); //use own function to fix problem when rendering past months

        $content = '';
        $this->indexArray = array();

        //OVERVIEW (chart and table)
        if ($this->post['overview'])
        {
            $this->elementsCounter++;
            $content .= $this->headerSpaceDiv();
            $content .= $this->renderOverview();
            $this->indexArray[$this->elementsCounter][0]['header'] = $GLOBALS['LANG']->getLL('headerOverview');
            $this->indexArray[$this->elementsCounter][0]['pageCounter'] = 2;
        }

        //PAGEVIEWS
        if ($this->post['pageviews'])
        {
            $this->elementsCounter++;
            if ($this->elementsCounter != 1)
                $content .= '<div style="page-break-before: always;"></div>';

            $content .= $this->headerSpaceDiv();
            $resultPageviews = $this->renderTableFlexible('pages', 0, 'counter DESC');
            $content .= $resultPageviews['content'];
            $this->indexArray[$this->elementsCounter][0]['header'] = $resultPageviews['header'];
            $this->indexArray[$this->elementsCounter][0]['pageCounter'] = $resultPageviews['pagecount'];
        }

        //TIME
        if ($this->post['time'])
        {
            if ($this->post['timeMerge'])
            {
                //merge PAGEVIEWS and VISITS in one table
                $this->elementsCounter++;
                //TIME - PAGEVIEWS/VISITS - DAY
                if ($this->elementsCounter != 1)
                    $content .= '<div style="page-break-before: always;"></div>';

                $content .= $this->headerSpaceDiv();
                $resultTimeMerged = $this->renderTimeMerged('overall_day_of_month');
                $content .= $resultTimeMerged['content'];
                $this->indexArray[$this->elementsCounter][1]['pageCounter'] = $resultTimeMerged['pagecount'];
                $this->indexArray[$this->elementsCounter][1]['header'] = $resultTimeMerged['header'];
                //TIME - PAGEVIEWS/VISITS - WEEKDAY
                $content .= '<div style="page-break-before: always;"></div>';
                $content .= $this->headerSpaceDiv();
                $resultTimeMerged = $this->renderTimeMerged('overall_day_of_week');
                $content .= $resultTimeMerged['content'];
                $this->indexArray[$this->elementsCounter][2]['pageCounter'] = $resultTimeMerged['pagecount'];
                $this->indexArray[$this->elementsCounter][2]['header'] = $resultTimeMerged['header'];
                //TIME - PAGEVIEWS/VISITS - DAYTIME
                $content .= '<div style="page-break-before: always;"></div>';
                $content .= $this->headerSpaceDiv();
                $resultTimeMerged = $this->renderTimeMerged('overall_hour_of_day');
                $content .= $resultTimeMerged['content'];
                $this->indexArray[$this->elementsCounter][3]['pageCounter'] = $resultTimeMerged['pagecount'];
                $this->indexArray[$this->elementsCounter][3]['header'] = $resultTimeMerged['header'];
            } else
            {
                //TIME - PAGEVIEWS - DAY
                //TIME - PAGEVIEWS - WEEKDAY
                //TIME - PAGEVIEWS - DAYTIME
                $this->elementsCounter++;
                $categoryArray = array(
                    0 => 'pages_overall_day_of_month',
                    1 => 'pages_overall_day_of_week',
                    2 => 'pages_overall_hour_of_day'
                );
                $content .= $this->threeTablesPack($categoryArray);

                //TIME - VISITS - DAY
                //TIME - VISITS - WEEKDAY
                //TIME - VISITS - DAYTIME
                $this->elementsCounter++;
                $categoryArray = array(
                    0 => 'visits_overall_day_of_month',
                    1 => 'visits_overall_day_of_week',
                    2 => 'visits_overall_hour_of_day'
                );
                $content .= $this->threeTablesPack($categoryArray);
            }
        }

        //REFERRED - WEBSITES
        //REFERRED - SERACHENGINES
        //REFERRED - SEARCHSTRINGS
        if ($this->post['referers'])
        {
            $this->elementsCounter++;
            $categoryArray = array(
                0 => 'referers_external_websites',
                1 => 'referers_searchengines',
                2 => 'search_strings'
            );
            $linkElementTitleArray[0] = 1;
            $content .= $this->threeTablesPack($categoryArray, $linkElementTitleArray);
        }

        //BROWSER/BOTS - BROWSER
        //BROWSER/BOTS - BOTS
        //BROWSER/BOTS - UNKNOWN USER AGENS
        if ($this->post['browserRobots'])
        {
            $this->elementsCounter++;
            $categoryArray = array(0 => 'browsers', 1 => 'robots', 2 => 'referers_searchengines');
            $content .= $this->threeTablesPack($categoryArray);
        }

        //OTHER - OS
        //OTHER - IP
        //OTHER - HOSTS
        if ($this->post['other'])
        {
            $this->elementsCounter++;
            $categoryArray = array(0 => 'operating_systems', 1 => 'ip_addresses', 2 => 'ip_addresses');
            $content .= $this->threeTablesPack($categoryArray);
        }

        //Index of Content
        return $this->renderContentIndex() . '<div style="page-break-before: always;"></div>' . $content;
    }

    /**
     * Render the overview-statistics as html-content.
     *
     * @return    string        html-formated overview-statistics
     */
    protected function renderOverview()
    {
        $content = '<div align="center"><strong>' . $GLOBALS['LANG']->getLL('headerOverview') . '</strong></div><br />';
        $overviewPageData = $this->overviewPageData['pageviews_and_visits'];
        $tableRows = '';
        foreach ($overviewPageData as $key => $data) {
            $tableRows .= '<tr><td>' . strftime("%B", mktime(0, 0, 0, $key + $this->fromToArray['from_month'],
                    10)) . $data['element_title'] . '</td><td>' . $data['pageviews'] . '</td><td>' . $data['visits'] . '</td><td>' . $data['pages_per_visit'] . '</td></tr>';
        }

        $content .= '<img src="' . $this->renderOverviewGraph() . '" />';
        $content .= '<div style="page-break-before: always;"></div>';
        $content .= $this->headerSpaceDiv();
        $content .= '<table style="border-collapse:collapse">' .
            '<thead>' .
            '<tr>' .
            '<th>' . $GLOBALS['LANG']->getLL('name') . '</th>' .
            '<th>' . $GLOBALS['LANG']->getLL('pageviews') . '</th>' .
            '<th>' . $GLOBALS['LANG']->getLL('visits') . '</th>' .
            '<th>' . $GLOBALS['LANG']->getLL('pages_per_visit') . '</th>' .
            '</tr>' .
            '</thead>' .
            '<tbody>' .
            $tableRows .
            '</tbody>' .
            '</table>';
        $content .= '<div class="page-break"></div>';

        return $content;
    }

    /**
     * Render the merged table of pageviews and visits by a time
     *
     * @param    string $category : category of the data to be rendered
     * @param    int $linktitle : set to 1 to short a title and link to a website (used for referers)
     * @param    string $orderBy : SQL-query
     * @return    array            result-data
     */
    protected function renderTimeMerged($category, $linktitle = 0, $orderBy = 'element_title ASC')
    {
        $rowsPages = $this->kestatslib->getStatResults('pages', 'pages_' . $category, 'element_title,counter', 0,
            $orderBy, '', 0, $this->fromToArray, 0, 0);
        $rowsPages_sum = $this->kestatslib->getStatResults('pages', 'pages_' . $category, 'element_title,counter', 1,
            $orderBy, '', 0, $this->fromToArray, 0, 0);

        $rowsVisits = $this->kestatslib->getStatResults('pages', 'visits_' . $category, 'element_title,counter', 0,
            $orderBy, '', 0, $this->fromToArray, 0, 0);
        $rowsVisits_sum = $this->kestatslib->getStatResults('pages', 'visits_' . $category, 'element_title,counter', 1,
            $orderBy, '', 0, $this->fromToArray, 0, 0);

        $i = 0;
        $pages = 1;
        $tableRows = array();
        foreach ($rowsPages as $keyPages => $dataPages) 
        {
            $i++;
            $dataVisits = array('counter' => 0);
            foreach ($rowsVisits as $keyVisits => $dataVisits)
                if ($dataVisits['element_title'] == $dataPages['element_title'])
                    break;
            $percentPages = 100 * intval($dataPages['counter']) / $rowsPages_sum[0]['counter'];
            $percentPages = number_format($percentPages, 2, $this->decimalChar, ' ');
            $percentVisits = 100 * intval($dataVisits['counter']) / $rowsPages_sum[0]['counter'];
            $percentVisits = number_format($percentVisits, 2, $this->decimalChar, ' ');
            if (strstr($category, '_day_of_week')) {
                //change element_title to readable day of week
                $dataPages['element_title'] = strftime('%A',
                    strtotime('Sunday +' . $dataPages['element_title'] . ' days'));
            }
            $tableRows[$pages] .= '<tr><td>' . $i . '</td><td>' . $dataPages['element_title'] . '</td><td>' . $dataPages['counter'] . '</td><td>' . $percentPages . ' %</td><td>' . $dataVisits['counter'] . '</td><td>' . $percentVisits . ' %</td></tr>';
            if ($i % 23 == 0)
                $pages++;
        }
        //add row with sum at the end
        $tableRows[$pages] .= '<tr><td>' . $GLOBALS['LANG']->getLL('sum') . '</td><td></td><td>' . $rowsPages_sum[0]['counter'] . '</td><td>100 %</td><td>' . $rowsVisits_sum[0]['counter'] . '</td><td>100 %</td></tr>';

        //render table
        $result = array();
        $result['pagecount'] = $pages;
        $result['rowcount'] = $i;
        $result['rowcount_lastpage'] = $i >= 23 ? $pages % $i : $i;
        $result['header'] = $GLOBALS['LANG']->getLL('header_' . $category) . ' (' . strftime("%B %Y",
                mktime(0, 0, 0, $this->fromToArray['to_month'], 10, $this->fromToArray['to_year'])) . ')';
        for ($page = 1; $page <= $pages; $page++)
        {
            if ($page != 1) 
                $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            
            $result['content'] .= '<br /><div align="center"><strong>' . $result['header'] . '</strong> (' . $GLOBALS['LANG']->getLL('page') . ' ' . $page . '/' . $pages . ')</div><br />';
            $result['content'] .= '<table style="border-collapse:collapse">' .
                '<thead>' .
                '<tr>' .
                '<th>' . $GLOBALS['LANG']->getLL('line') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('name') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('pageviews') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('pageviews') . ' [%]</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('visits') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('visits') . ' [%]</th>' .
                '</tr>' .
                '</thead>' .
                '<tbody>' .
                $tableRows[$page] .
                '</tbody>' .
                '</table>';
        }

        if ($this->post['timeVisitsDayofmonthImg'] && $category == 'overall_day_of_month')
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderTimeMergedChart('pages_' . $category, $rowsPages, $rowsVisits, $result['header']) . '" />';
            $result['pagecount']++;
        }
        if ($this->post['timeVisitsDayofweekImg'] && $category == 'overall_day_of_week')
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderTimeMergedChart('pages_' . $category, $rowsPages, $rowsVisits, $result['header']) . '" />';
            $result['pagecount']++;
        }
        if ($this->post['timeVisitsHourofdayImg'] && $category == 'overall_hour_of_day')
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderTimeMergedChart('pages_' . $category, $rowsPages, $rowsVisits, $result['header']) . '" />';
            $result['pagecount']++;
        }

        return $result;
    }

    /**
     * Render a chart with pageviews and visits by a time
     *
     * @param    string $type : type (category) of the chart to be rendered
     * @param    array $rowsPages : pageviews-data
     * @param    array $rowsVisits : visits-data
     * @param    string $header : header for the chart
     * @return string absolute path to created image file
     */
    protected function renderTimeMergedChart($type, $rowsPages, $rowsVisits, $header)
    {
        //delete old images
        $uploadfolder_filelist = scandir(GeneralUtility::getFileAbsFileName($this->uploadfolderPath));
        foreach ($uploadfolder_filelist as $filename)
            if (strstr($filename, $type . '_') AND strpos($filename, '.png') !== false)
                unlink(GeneralUtility::getFileAbsFileName($this->uploadfolderPath . $filename));

        //Create the graph. These two calls are always required
        /** @var \Graph $graph */
        $graph = GeneralUtility::makeInstance('Graph', 780, 500);
        $graph->title->Set($header);
        $graph->SetScale('textlin', 0, 0, 0, 0);
        $graph->SetMargin(50, 50, 20, 0);
        $graph->xgrid->Show();
        $graph->xgrid->SetWeight(2);

        //disable anti-aliasing if required php-function does not exist
        if (!function_exists('imageantialias'))
            $graph->img->SetAntiAliasing(false);

        $tickLabels = array();
        $ydata_pageviews = array();
        $ydata_visits = array();
        if (!empty($rowsPages)) 
        {
            foreach ($rowsPages as $key => $row) 
            {
                if (strstr($type, '_day_of_week')) 
                {
                    //change element_title to readable day of week
                    $row['element_title'] = strftime('%A', strtotime('Sunday +' . $row['element_title'] . ' days'));
                }
                array_push($tickLabels, $row['element_title']);
                array_push($ydata_pageviews, $row['counter']);
            }
        }
        if (!empty($rowsVisits)) 
        {
            foreach ($rowsVisits as $key => $row) 
            {
                if (empty($rowsPages)) 
                {
                    if (strstr($type, '_day_of_week')) 
                    {
                        //change element_title to readable day of week
                        $row['element_title'] = strftime('%A', strtotime('Sunday +' . $row['element_title'] . ' days'));
                    }
                }
                array_push($ydata_visits, $row['counter']);
            }
        }
        $graph->xaxis->SetTextLabelInterval(1);
        $graph->xaxis->SetTickLabels($tickLabels);

        //Draw the pageviews
        if (!empty($rowsPages)) 
        {
            /** @var \LinePlot $lineplot */
            $lineplot = GeneralUtility::makeInstance('LinePlot', $ydata_pageviews);
            $lineplot->SetLegend($GLOBALS['LANG']->getLL('pageviews'));
            $graph->Add($lineplot);
            $lineplot->SetWeight(4);
            $lineplot->SetColor('blue');
            $lineplot->value->SetFormat('%d');
            $lineplot->value->Show();
            $lineplot->value->SetColor("black", "black");
            $lineplot->value->SetMargin(14);
        }

        //Draw the visits
        if (!empty($rowsVisits)) 
        {
            /** @var \LinePlot $lineplot */
            $lineplot = GeneralUtility::makeInstance('LinePlot', $ydata_visits);
            $lineplot->SetLegend($GLOBALS['LANG']->getLL('visits'));
            $graph->Add($lineplot);
            $lineplot->SetWeight(4);
            $lineplot->SetColor('darkgreen');
            $lineplot->value->SetFormat('%d');
            $lineplot->value->Show();
            $lineplot->value->SetColor("black", "black");
            $lineplot->value->SetMargin(-14);
        }

        //Save the graph
        $fileName = GeneralUtility::getFileAbsFileName($this->uploadfolderPath . $type . '_' . $this->actTime . '.png');
        $graph->img->SetImgFormat('png', 100);
        $graph->Stroke($fileName);

        return $fileName;
    }

    /**
     * [Describe function...]
     *
     * @param    array $categoryArray : category of the data to be rendered
     * @param    array $linkElementTitleArray : set to 1 to short a title and link to a website (used for referers)
     * @return    string    html-content
     * @access    private
     */
    protected function threeTablesPack($categoryArray, $linkElementTitleArray = array())
    {
        $resultOne = $this->renderTableFlexible($categoryArray[0], $linkElementTitleArray[0]);
        $resultTwo = $this->renderTableFlexible($categoryArray[1], $linkElementTitleArray[1]);
        $resultThree = $this->renderTableFlexible($categoryArray[2], $linkElementTitleArray[2]);

        $content = $this->elementsCounter != 1 ? '<div style="page-break-before: always;"></div>' : '';
        $content .= $this->headerSpaceDiv();
        if ($resultOne['rowcount_lastpage'] + $resultTwo['rowcount_lastpage'] + $resultThree['rowcount_lastpage'] <= 14) {
            $content .= $resultOne['content'] . $resultTwo['content'] . $resultThree['content'];
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = 0;
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = 0;
            $this->indexArray[$this->elementsCounter][3]['pageCounter'] = 1;
            //t3lib_utility_Debug::debug($resultThree);
        } elseif ($resultOne['rowcount_lastpage'] + $resultTwo['rowcount_lastpage'] <= 14) {
            $content .= $resultOne['content'] . $resultTwo['content'];
            $content .= '<div style="page-break-before: always;"></div>';
            $content .= $this->headerSpaceDiv();
            $content .= $resultThree['content'];
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = 0;
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = 1;
            $this->indexArray[$this->elementsCounter][3]['pageCounter'] = $resultThree['pagecount'];
        } elseif ($resultTwo['rowcount_lastpage'] + $resultThree['rowcount_lastpage'] <= 14) {
            $content .= $resultOne['content'];
            $content .= '<div style="page-break-before: always;"></div>';
            $content .= $this->headerSpaceDiv();
            $content .= $resultTwo['content'] . $resultThree['content'];
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = $resultOne['pagecount'];
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = 0;
            $this->indexArray[$this->elementsCounter][3]['pageCounter'] = 1;
        } else {
            $content .= $resultOne['content'];
            $content .= '<div style="page-break-before: always;"></div>';
            $content .= $this->headerSpaceDiv();
            $content .= $resultTwo['content'];
            $content .= '<div style="page-break-before: always;"></div>';
            $content .= $this->headerSpaceDiv();
            $content .= $resultThree['content'];
            //$this->indexArray[$this->elementsCounter]['pageCounter'] = $resultOne['pagecount'] + $resultTwo['pagecount'] + $resultThree['pagecount'];
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = $resultOne['pagecount'];
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = $resultTwo['pagecount'];
            $this->indexArray[$this->elementsCounter][3]['pageCounter'] = $resultThree['pagecount'];
        }
        if ($resultOne['pagecount'] != 1) {
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = $resultOne['pagecount'];
        }
        if ($resultTwo['pagecount'] != 1) {
            $this->indexArray[$this->elementsCounter][1]['pageCounter'] = 1;
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = $resultTwo['pagecount'];
        }
        if ($resultThree['pagecount'] != 1) {
            $this->indexArray[$this->elementsCounter][2]['pageCounter'] = 1;
            $this->indexArray[$this->elementsCounter][3]['pageCounter'] = $resultThree['pagecount'];
        }

        $this->indexArray[$this->elementsCounter][1]['header'] = $resultOne['header'];
        $this->indexArray[$this->elementsCounter][2]['header'] = $resultTwo['header'];
        $this->indexArray[$this->elementsCounter][3]['header'] = $resultThree['header'];

        return $content;
    }

    /**
     * Render a flexible table for a category
     *
     * @param    string $category : category of the data to be rendered
     * @param    int $linktitle : set to 1 to short a title and link to a website (used for referers)
     * @param    string $orderBy : SQL-query
     * @return    array            result-data
     */
    protected function renderTableFlexible($category, $linktitle = 0, $orderBy = 'element_title ASC')
    {
        $rows = $this->kestatslib->getStatResults('pages', $category, 'element_title,counter', 0, $orderBy, '', 0,
            $this->fromToArray, 0, 0);
        $rowSum = $this->kestatslib->getStatResults('pages', $category, 'element_title,counter', 1, $orderBy, '',
            0, $this->fromToArray, 0, 0);
        
        $i = 0;
        $pages = 1;
        $tableRows = array();
        foreach ($rows as $key => $data)
        {
            $i++;
            $percent = 100 * intval($data['counter']) / $rowSum[0]['counter'];
            $percent = number_format($percent, 2, $this->decimalChar, ' ');
            if ($linktitle == 1)
            {
                //modifie element_title to a valid link
                $data['element_title'] = '<a href="' . $data['element_title'] . '" target="_blank">' . GeneralUtility::fixed_lgd_cs($data['element_title'],
                        50) . '</a>';
            }
            if (strstr($category, '_day_of_week'))
            {
                //change element_title to readable day of week
                $data['element_title'] = strftime('%A', strtotime('Sunday +' . $data['element_title'] . ' days'));
            }
            $tableRows[$pages] .= '<tr><td>' . $i . '</td><td>' . $data['element_title'] . '</td><td>' . $data['counter'] . '</td><td>' . $percent . ' %</td></tr>';
            if ($i % 23 == 0)
                $pages++;
        }
        //add row with sum at the end
        $tableRows[$pages] .= '<tr><td>' . $GLOBALS['LANG']->getLL('sum') . '</td><td></td><td>' . $rowSum[0]['counter'] . '</td><td>100 %</td></tr>';

        //render table
        $result = array();
        $result['pagecount'] = $pages;
        $result['rowcount'] = $i;
        $result['rowcount_lastpage'] = $i >= 23 ? $pages % $i: $i;
        $result['header'] = $GLOBALS['LANG']->getLL('header_' . $category) . ' (' . strftime("%B %Y",
                mktime(0, 0, 0, $this->fromToArray['to_month'], 10, $this->fromToArray['to_year'])) . ')';
        for ($page = 1; $page <= $pages; $page++) {
            if ($page != 1 OR $pages > 1) {
                $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            }
            $result['content'] .= '<br /><div align="center"><strong>' . $result['header'] . '</strong> (' . $GLOBALS['LANG']->getLL('page') . ' ' . $page . '/' . $pages . ')</div><br />';
            $result['content'] .= '<table style="border-collapse:collapse">' .
                '<thead>' .
                '<tr>' .
                '<th>' . $GLOBALS['LANG']->getLL('line') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('name') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('counter') . '</th>' .
                '<th>' . $GLOBALS['LANG']->getLL('percentage') . '</th>' .
                '</tr>' .
                '</thead>' .
                '<tbody>' .
                $tableRows[$page] .
                '</tbody>' .
                '</table>';
        }
        if ($category == 'browsers' && $this->post['browserRobotsBrowsersImg'])
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderGraphPie($category, $rows, $result['header']) . '">';
            $result['pagecount']++;
        }
        if ($category == 'visits_overall_day_of_month' && $this->post['timeVisitsDayofmonthImg'])
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderTimeMergedChart($category, '', $rows, $result['header']) . '">';
            $result['pagecount']++;
        }
        if ($category == 'visits_overall_day_of_week' && $this->post['timeVisitsDayofweekImg'])
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderGraphPie($category, $rows, $result['header']) . '">';
            $result['pagecount']++;
        }
        if ($category == 'visits_overall_hour_of_day' && $this->post['timeVisitsHourofdayImg'])
        {
            $result['content'] .= '<div style="page-break-before: always;"></div>' . $this->headerSpaceDiv();
            $result['content'] .= '<img src="' . $this->renderTimeMergedChart($category, '', $rows, $result['header']) . '">';
            $result['pagecount']++;
        }

        return $result;
    }

    /**
     * Render the index of the pdf-file
     *
     * @return    string        html-content
     */
    protected function renderContentIndex()
    {
        $pagePointer = 2;
        $tableRows = '';
        foreach ($this->indexArray as $element) {
            foreach ($element as $keySubelement => $subelement) {
                $tableRows .= '<tr><td>' . $subelement['header'] . '</td><td>' . $pagePointer . '</td></tr>';
                $pagePointer = $pagePointer + $subelement['pageCounter'];
            }
        }
        //t3lib_utility_Debug::debug($tableRows);
        $content = $this->headerSpaceDiv() . '<br /><div align="center"><strong>' . $GLOBALS['LANG']->getLL('headerIndex') . '</strong></div><br />';
        $content .= '<table style="border-collapse:collapse">' .
            '<tbody>' .
            $tableRows .
            '</tbody>' .
            '</table>';
        return $content;
    }

    /**
     * Render the statistcs to pdf-file.
     *
     * @param    string $content : html-formated content to be rendered to a pdf-file
     * @param    array $post : _POST data
     * @return    string        filename of the saved pdf
     */
    public function renderpdf($content, $post)
    {
        $this->post = $post;
        $this->actTime = time();
        $this->setTimeformat();
        //create event in database or update existing event
        $tce = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');
        $filename = 'statistics_' . $this->post['month'] . '_' . GeneralUtility::md5int(substr($this->renderElements(),
                0, -1)) . '.pdf';

        //get the selected domain
        if (empty($this->post['domain']))
            $this->post['domain'] = GeneralUtility::getHostname();

        //delete pdf with same filename
        if (is_file(GeneralUtility::getFileAbsFileName($this->uploadfolderPath) . $filename)) {
            unlink(GeneralUtility::getFileAbsFileName($this->uploadfolderPath) . $filename);
            $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('uid, mailsendto',
                'tx_jhkestatsexport_domain_model_filelist',
                'filename=\'' . $filename . '\' AND deleted=0 AND hidden=0');
        }

        //html2pdf
        GeneralUtility::requireOnce(ExtensionManagementUtility::extPath('jh_kestats_export',
            'Classes/Contrib/dompdf/dompdf_config.inc.php'));
        GeneralUtility::requireOnce(ExtensionManagementUtility::extPath('jh_kestats_export',
            'Classes/Contrib/dompdf/dompdf_config.custom.inc.php'));

        /** @var \DOMPDF $dompdf */
        $dompdf = GeneralUtility::makeInstance('DOMPDF');
        $dompdf->set_paper('A4', 'landscape');
        $dompdf->load_html('<html><head><style type="text/css">' . $this->getTableCSS() . '</style></head><body>' . $this->addPdfHeader($this->post['domain']) . nl2br($content) . '</body></html>');
        $dompdf->render();
        $pdfoutput = $dompdf->output();
        $fp = fopen(GeneralUtility::getFileAbsFileName($this->uploadfolderPath) . $filename, "a");
        fwrite($fp, $pdfoutput);
        fclose($fp);

        if (empty($row) || $row === false) {
            $dbArray = array();
            $dbArray['pid'] = '0';
            $dbArray['tstamp'] = $this->actTime;
            $dbArray['crdate'] = $this->actTime;
            $dbArray['cruser_id'] = '';
            $dbArray['deleted'] = '0';
            $dbArray['hidden'] = '0';
            $dbArray['filename'] = $filename;
            //$dbArray['mailsendto'] = $post['mailTo'];
            $dbArray['content'] = str_replace('-', '<br/>', $this->renderElements());

            $data['tx_jhkestatsexport_domain_model_filelist']['NEW0001'] = $dbArray;
        } else {
            $dbArray = array();
            $dbArray['tstamp'] = $this->actTime;
            $dbArray['deleted'] = '0';
            /*$mailTo = $row['mailsendto'];
            if (!empty($post['mailTo'])) $mailTo = (empty($row['mailsendto']) ? $post['mailTo'] : $mailTo . '<br/>' . $post['mailTo']);
            $dbArray['mailsendto'] = $mailTo;*/
            $dbArray['content'] = str_replace('-', '<br/>', $this->renderElements());
            $data['tx_jhkestatsexport_domain_model_filelist'][$row['uid']] = $dbArray;
        }
        // INSERT OR UPDATE RECORD(S)
        $tce->start($data, array());
        $tce->process_datamap();

        return $filename;
    }

    /**
     * Render the statistics overview image.
     *
     * @return string absolute path to created image file
     */
    protected function renderOverviewGraph()
    {
        //delete old images
        $uploadfolder_filelist = scandir(GeneralUtility::getFileAbsFileName($this->uploadfolderPath));
        foreach ($uploadfolder_filelist as $filename)
            if (strpos($filename, 'overview_') == 0 AND strpos($filename, '.png') !== false)
                unlink(GeneralUtility::getFileAbsFileName($this->uploadfolderPath . $filename));

        //Create the graph. These two calls are always required
        /** @var \Graph $graph */
        $graph = GeneralUtility::makeInstance('Graph', 780, 400);
        $graph->SetScale('textlin', 0, 0, 0, 0);
        $graph->SetMargin(50, 50, 20, 0);
        $graph->xgrid->Show();
        $graph->xgrid->SetWeight(2);

        //disable anti-aliasing if required php-function does not exist
        if (!function_exists('imageantialias'))
            $graph->img->SetAntiAliasing(false);

        $tickLabels = array();
        $ydata_pageviews = array();
        $ydata_visits = array();
        foreach ($this->overviewPageData['pageviews_and_visits'] as $key => $row)
        {
            array_push($tickLabels,
                strftime("%B", mktime(0, 0, 0, $key + $this->fromToArray['from_month'], 10)) . $row['element_title']);
            array_push($ydata_pageviews, $row['pageviews']);
            array_push($ydata_visits, $row['visits']);
        }
        $graph->xaxis->SetTextLabelInterval(2);
        $graph->xaxis->SetTickLabels($tickLabels);

        //Draw the pageviews
        /** @var \LinePlot $lineplot */
        $lineplot = GeneralUtility::makeInstance('LinePlot', $ydata_pageviews);
        $lineplot->SetLegend($GLOBALS['LANG']->getLL('pageviews'));
        $graph->Add($lineplot);
        $lineplot->SetWeight(4);
        $lineplot->SetColor('blue');
        $lineplot->value->SetFormat('%d');
        $lineplot->value->Show();
        $lineplot->value->SetColor("black", "black");
        $lineplot->value->SetMargin(14);

        //Draw the visits
        /** @var \LinePlot $lineplot */
        $lineplot = GeneralUtility::makeInstance('LinePlot', $ydata_visits);
        $lineplot->SetLegend($GLOBALS['LANG']->getLL('visits'));
        $graph->Add($lineplot);
        $lineplot->SetWeight(4);
        $lineplot->SetColor('darkgreen');
        $lineplot->value->SetFormat('%d');
        $lineplot->value->Show();
        $lineplot->value->SetColor("black", "black");
        $lineplot->value->SetMargin(-14);

        //Save the graph
        //$graph->img->SetImgFormat('jpeg');
        $fileName = GeneralUtility::getFileAbsFileName($this->uploadfolderPath . 'overview_' . $this->actTime . '.png');
        $graph->img->SetImgFormat('png', 100);
        $graph->Stroke($fileName);

        return $fileName;
    }

    /**
     * Render a pie-graph with given data
     *
     * @param    string $type : type (category) of the chart to be rendered
     * @param    array $rows : data to be displayed as graph
     * @param    string $header : header for the graph
     * @return string absolute path to created image file
     */
    protected function renderGraphPie($type, $rows, $header)
    {
        //delete old images
        $uploadfolder_filelist = scandir(GeneralUtility::getFileAbsFileName($this->uploadfolderPath));
        foreach ($uploadfolder_filelist as $filename)
            if (strstr($filename, $type . '_') AND strpos($filename, '.png') !== false)
                unlink(GeneralUtility::getFileAbsFileName($this->uploadfolderPath . $filename));

        $data = array();
        $labels = array();

        foreach ($rows as $key => $row)
        {
            if (strstr($type, '_day_of_week')) {
                //change element_title to readable day of week
                $row['element_title'] = strftime('%A',
                        strtotime('Sunday +' . $row['element_title'] . ' days')) . "\n%.1f%%";
            }
            array_unshift($labels, $row['element_title']);//
            array_unshift($data, $row['counter']);
        }

        //Create the graph.
        /** @var \PieGraph $graph */
        $graph = GeneralUtility::makeInstance('PieGraph', 800, 500);
        $graph->title->Set($header);

        //disable anti-aliasing if required php-function does not exist
        if (!function_exists('imageantialias'))
            $graph->img->SetAntiAliasing(false);

        // Create
        /** @var \PiePlot3D $p1 */
        $p1 = GeneralUtility::makeInstance('PiePlot3D', $data);
        $p1->SetStartAngle(90);
        $p1->SetTheme('earth');
        $p1->SetSize("200");
        $p1->SetLabelType(PIE_VALUE_PER);
        $p1->SetLabels($labels, "1.1");
        $p1->SetLabelMargin(30);
        $p1->ShowBorder();
        $p1->SetColor('black');
        $graph->Add($p1);

        //Save the graph
        $fileName = GeneralUtility::getFileAbsFileName($this->uploadfolderPath . $type . '_' . $this->actTime . '.png');
        $graph->img->SetImgFormat('png', 100);
        $graph->Stroke($fileName);

        return $fileName;
    }

    /**
     * Add a div element to html-content to create a spacer for the header.
     *
     * @return    string        html-formated div
     * @access    private
     */
    protected function headerSpaceDiv()
    {
        return '<div id="headerspace">&nbsp;</div>';
    }

    /**
     * Create the pdf-header displayed on each page.
     *
     * @param    string $hostname : hostname of website
     * @return    string        script for pdf-file
     */
    protected function addPdfHeader($hostname)
    {
        return '
	<script type="text/php">
	if (isset($pdf)) {
	  $obj = $pdf->open_object();

	  $font = Font_Metrics::get_font("helvetica", "normal");
	  $size = 10;

	  $text_left = "' . $hostname . ' - ' . $GLOBALS['LANG']->getLL('created') . ': ' . strftime('%c', time()) . '";
	  $pdf->page_text(35, 35, $text_left, $font, $size, array(0,0,0));

	  $width_right = Font_Metrics::get_text_width("' . $GLOBALS['LANG']->getLL('page') . ' 00 ' . $GLOBALS['LANG']->getLL('of') . ' 00", $font, $size);
	  $pdf->page_text(841 - 35 - $width_right, 35, "' . $GLOBALS['LANG']->getLL('page') . ' {PAGE_NUM} ' . $GLOBALS['LANG']->getLL('of') . ' {PAGE_COUNT}", $font, $size, array(0,0,0));

	  $pdf->line(35, 48, 806, 48, array(0,0,0), 1);

	  $pdf->close_object();
	$pdf->add_object($obj, \'all\');
	}
	</script>
	  ';
    }

    /**
     * Contains the CSS used to style the statistics-tables.
     *
     * @return    string        css for pdf-file
     * @access    public
     */
    public function getTableCSS()
    {
        return '
	#typo3-funcmenu {
	border: none;
	}

	#headerspace {
	height: 18px;
	width: 600px;
	}

	table {
	width: 100%;
	border: 1px solid #B0B0B0;
	}
	table tbody {
	/* Kind of irrelevant unless your .css is alreadt doing something else */
	margin: 0;
	padding: 0;
	border: 0;
	outline: 0;
	font-size: 100%;
	vertical-align: baseline;
	background: transparent;
	}
	table thead {
	text-align: left;
	}
	table thead th {
	background: -moz-linear-gradient(top, #F0F0F0 0, #DBDBDB 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #F0F0F0), color-stop(100%, #DBDBDB));
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#F0F0F0\', endColorstr=\'#DBDBDB\', GradientType=0);
	border: 1px solid #B0B0B0;
	color: #444;
	font-size: 16px;
	font-weight: bold;
	padding: 3px 10px;
	}
	table td {
	padding: 3px 10px;
	}
	table tr:nth-child(even) {
	background: #F2F2F2;
	}
	  ';
    }

    /**
     * Send mail with attachment to given email-address.
     *
     * @param    string $mailTo : reciever of the email
     * @param    string $hostname : hostname of the website
     * @param    string $filename : filename of pdf-file to be attached
     * @return    void
     * @access    public
     */
    public function sendEmail($mailTo, $hostname, $filename)
    {
        $emailSubject = $GLOBALS['LANG']->getLL('emailSubject') . ' ' . $hostname;
        $bodyContent = sprintf($GLOBALS['LANG']->getLL('emailBodytext'), $hostname);
        if ($hostname == '')
            $hostname = GeneralUtility::getHostname();

        $mail = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $mail->setFrom('statistics@' . $hostname);
        $mail->setTo($mailTo);
        $mail->setSubject($emailSubject);
        $mail->setBody($bodyContent);
        $mail->attach(\Swift_Attachment::fromPath(GeneralUtility::getFileAbsFileName($this->uploadfolderPath) . $filename)->setFilename($filename));
        $mail->send();
    }


    /**
     * Create a string with elements to be rendered.
     *
     * @return    string        all elements connected by "-"
     */
    protected function renderElements()
    {
        $elements = '';
        foreach ($this->post as $key => $value)
            if ($value && $key != '__referrer' && $key != '__trustedProperties' && $key != 'month' && $key != 'domain' && $key != 'mailTo')
                $elements .= $value . '-';

        return $elements;
    }

    /**
     * Create the fromToArray.
     *
     * @param    string $input : from-to - format: "YYYY-mm"
     * @return    array            new fromToArray
     */
    protected function fromToArrayFunc($input)
    {
        $fromToArray = array();
        if (!empty($input))
        {
            $selectedDate = explode('-', $input);
            if (strpos($selectedDate['1'], '0') == 0)
                $selectedDate['1'] = str_replace('0', '', $selectedDate['1']);

            $fromToArray['from_year'] = $selectedDate['0'];
            $fromToArray['from_month'] = $selectedDate['1'];
            $fromToArray['to_year'] = $selectedDate['0'];
            $fromToArray['to_month'] = $selectedDate['1'];
        } else
        {
            $fromToArray['from_year'] = date('Y');
            $fromToArray['from_month'] = date('m');
            $fromToArray['to_year'] = date('Y');
            $fromToArray['to_month'] = date('m');
        }
        return $fromToArray;
    }

    /**
     * Set 'setlocale' to configured language
     *
     * @return    void
     */
    protected function setTimeformat()
    {
        setlocale(LC_ALL, $GLOBALS['LANG']->getLL('setlocale'));
    }

    /**
     * refreshOverviewPageData
     *
     * ke_stats function; modified to display time-range selectend by cronjob or within the backend-module
     *
     * In future versions there will be an overview page with more data. This
     * page will then stay in a cache and will be updated by a cron-cli-script.
     * Right now, there are only visitors and pageviews in order to keep the
     *
     * @param int $pageUid
     * @return array
     */
    function refreshOverviewPageData($pageUid = 0)
    {
        $overviewPageData = array();

        // all languages and types will be shown in the overview page
        $element_language = -1;
        $element_type = -1;

        // get the subpages list
        if ($pageUid) {
            $this->kestatslib->pagelist = strval($pageUid);
            $this->kestatslib->getSubPages($pageUid);
        }

        if ($pageUid) {
            $fromToArray = $this->fromToArray;
            $fromToArray['from_year'] = $fromToArray['from_year'] - 1;

            // monthly process of pageviews
            $columns = 'element_title,counter';
            $pageviews = $this->kestatslib->getStatResults(STAT_TYPE_PAGES, CATEGORY_PAGES, $columns, STAT_ONLY_SUM,
                'counter DESC', '', 0, $fromToArray, $element_language, $element_type);
            //$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_monthly'),$columns,$resultArray,'no_line_numbers','counter','');
            // monthly process of visitors
            $visits = $this->kestatslib->getStatResults(STAT_TYPE_PAGES, CATEGORY_VISITS_OVERALL, $columns,
                STAT_ONLY_SUM, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);

            // combine visits and pageviews
            $resultArray = array();
            for ($i = 0; $i < 13; $i++) {
                $pages_per_visit = $visits[$i]['counter'] ? round(floatval($pageviews[$i]['counter'] / $visits[$i]['counter']),
                    1) : '';
                $resultArray[$i] = array(
                    'element_title' => $pageviews[$i]['element_title'],
                    'pageviews' => $pageviews[$i]['counter'],
                    'visits' => $visits[$i]['counter'],
                    'pages_per_visit' => $pages_per_visit
                );
            }

            $overviewPageData['pageviews_and_visits'] = $resultArray;
            unset($pageviews);
            unset($visits);
            unset($resultArray);
        }
        return $overviewPageData;
    }
}