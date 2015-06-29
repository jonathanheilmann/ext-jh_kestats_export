<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Jonathan Heilmann <mail@jonathan-heilmann.de>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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

/**
 * Class for updating jh_kestats_export
 */
class ext_update {

	/**
	 * Stub function for the extension manager
	 *
	 * @param	string	$what	What should be updated
	 * @return	boolean	true to allow access
	 */
	public function access($what = 'all') {
		$filelistCount = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows('filename', 'tx_jhkestatsexport_filelist', 'deleted=0');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($filelistCount);
		return ($filelistCount > 0 ? TRUE : FALSE);
	}

	/**
	 * Updates nested sets
	 *
	 * @return	string		HTML output
	 */
	public function main() {
		if (t3lib_div::_POST('nssubmit') != '') {
			$res = $this->adoptOldFilelist();
			$content =
				'<p>Update finished.<br/>' .
				'Successfully adopted <b>' . $res['success'] . '</b> file(s).<br/>'.
				'Failed for <b>' . $res['fail'] . '</b> file(s).</p>';
		}
		else {
			$content = $this->prompt();
		}
		return $content;
	}

	/**
	 * Shows a form to created nested sets data.
	 *
	 * @return	string
	 */
	protected function prompt() {
		return
			'<form action="' . t3lib_div::getIndpEnv('REQUEST_URI') . '" method="POST" style="margin-top: 300px;">' .
			'<p>This update will do the following:</p>' .
			'<ul>' .
			'<li>Adopt data from old filelist to new one</li>' .
			'</ul>' .
			'<br />' .
			'<input type="submit" name="nssubmit" value="Update" /></form>';
	}

	/**
	 * Adopt data from old filelist to new one
	 *
	 * @return	array
	 */
	protected function adoptOldFilelist() {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_jhkestatsexport_filelist', 'deleted=0');
		//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rows);
		$resCounter = array(
			'success' => 0,
			'fail' => 0,
		);
		foreach ($rows as $row) {
			$uid = $row['uid'];
			unset($row['uid'], $row['pid']);
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_jhkestatsexport_domain_model_filelist', $row);
			if ($res === TRUE) {
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_jhkestatsexport_filelist', 'uid='.$uid, array('deleted' => 1));
				$resCounter['success']++;
			} else {
				$resCounter['fail']++;
			}
		}
		return $resCounter;
	}
}

?>
