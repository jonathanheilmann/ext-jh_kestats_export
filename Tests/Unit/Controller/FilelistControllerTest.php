<?php
namespace Heilmann\JhKestatsExport\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Jonathan Heilmann <mail@jonathan-heilmann.de>
 *  			
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

/**
 * Test case for class Heilmann\JhKestatsExport\Controller\FilelistController.
 *
 * @author Jonathan Heilmann <mail@jonathan-heilmann.de>
 */
class FilelistControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Heilmann\JhKestatsExport\Controller\FilelistController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('Heilmann\\JhKestatsExport\\Controller\\FilelistController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllFilelistsFromRepositoryAndAssignsThemToView() {

		$allFilelists = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$filelistRepository = $this->getMock('Heilmann\\JhKestatsExport\\Domain\\Repository\\FilelistRepository', array('findAll'), array(), '', FALSE);
		$filelistRepository->expects($this->once())->method('findAll')->will($this->returnValue($allFilelists));
		$this->inject($this->subject, 'filelistRepository', $filelistRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('filelists', $allFilelists);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function newActionAssignsTheGivenFilelistToView() {
		$filelist = new \Heilmann\JhKestatsExport\Domain\Model\Filelist();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('newFilelist', $filelist);
		$this->inject($this->subject, 'view', $view);

		$this->subject->newAction($filelist);
	}

	/**
	 * @test
	 */
	public function createActionAddsTheGivenFilelistToFilelistRepository() {
		$filelist = new \Heilmann\JhKestatsExport\Domain\Model\Filelist();

		$filelistRepository = $this->getMock('Heilmann\\JhKestatsExport\\Domain\\Repository\\FilelistRepository', array('add'), array(), '', FALSE);
		$filelistRepository->expects($this->once())->method('add')->with($filelist);
		$this->inject($this->subject, 'filelistRepository', $filelistRepository);

		$this->subject->createAction($filelist);
	}

	/**
	 * @test
	 */
	public function deleteActionRemovesTheGivenFilelistFromFilelistRepository() {
		$filelist = new \Heilmann\JhKestatsExport\Domain\Model\Filelist();

		$filelistRepository = $this->getMock('Heilmann\\JhKestatsExport\\Domain\\Repository\\FilelistRepository', array('remove'), array(), '', FALSE);
		$filelistRepository->expects($this->once())->method('remove')->with($filelist);
		$this->inject($this->subject, 'filelistRepository', $filelistRepository);

		$this->subject->deleteAction($filelist);
	}
}
