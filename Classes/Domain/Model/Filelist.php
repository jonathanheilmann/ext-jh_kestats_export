<?php
namespace Heilmann\JhKestatsExport\Domain\Model;

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
 * Filelist
 */
class Filelist extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * crdate
	 *
	 * @var \DateTime
	 */
	protected $crdate = '';

	/**
	 * tstamp
	 *
	 * @var \DateTime
	 */
	protected $tstamp = '';

	/**
	 * filename
	 *
	 * @var string
	 */
	protected $filename = '';

	/**
	 * mailsendto
	 *
	 * @var string
	 */
	protected $mailsendto = '';

	/**
	 * content
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Returns the crdate
	 *
	 * @return \DateTime $crdate
	 */
	public function getCrdate() {
		return $this->crdate;
	}

	/**
	 * Returns the tstamp
	 *
	 * @return \DateTime $tstamp
	 */
	public function getTstamp() {
		return $this->tstamp;
	}

	/**
	 * Returns the filename
	 *
	 * @return string $filename
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * Sets the filename
	 *
	 * @param string $filename
	 * @return void
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * Returns the mailsendto
	 *
	 * @return string $mailsendto
	 */
	public function getMailsendto() {
		return $this->mailsendto;
	}

	/**
	 * Sets the mailsendto
	 *
	 * @param string $mailsendto
	 * @return void
	 */
	public function setMailsendto($mailsendto) {
		$this->mailsendto = $mailsendto;
	}

	/**
	 * Returns the content
	 *
	 * @return string $content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Sets the content
	 *
	 * @param string $content
	 * @return void
	 */
	public function setContent($content) {
		$this->content = $content;
	}

}