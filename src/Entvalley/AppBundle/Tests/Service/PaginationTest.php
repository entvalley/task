<?php

namespace Entvalley\AppBundle\Tests\Service;

use Entvalley\AppBundle\Service\Pagination;

class PaginationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function calculatesOffsetBasedOnPerPageAndCurrentPage()
    {
        $paging = $this->_createPagination(5);
        $paging->setTotal(100);
        $paging->setCurrentPage(12);
        $this->assertEquals(5, $paging->getPerPage());
        $this->assertEquals(12, $paging->getCurrentPage());
        $this->assertEquals(55, $paging->getOffset()); // perpage * current page
    }

    /**
     * @test
     */
    public function ifCurrentPageOverflowsOffsetThenReturnsOffsetForCLosestExistingPage()
    {
        $paging = $this->_createPagination(10);
        $paging->setTotal(100);
        $paging->setCurrentPage(11);
        $this->assertEquals(10, $paging->getCurrentPage());
        $this->assertEquals(90, $paging->getOffset()); // perpage * current page

        $paging = $this->_createPagination(10);
        $paging->setTotal(101);
        $paging->setCurrentPage(12);
        $this->assertEquals(100, $paging->getOffset()); // perpage * current page
        $this->assertEquals(11, $paging->getCurrentPage());
    }

    /**
     * @test
     */
    public function ignoresNegativeValues()
    {
        $paging = $this->_createPagination(-1);
        $paging->setCurrentPage(-1);
        $paging->setTotal(-1);
        $this->assertEquals(0, $paging->getOffset());
        $this->assertEquals(0, $paging->getTotal());
        $this->assertEquals(0, $paging->getCurrentPage());
    }

    /**
     * @test
     */
    public function calculatesLastPage()
    {
        $paging = $this->_createPagination(5);
        $paging->setTotal(60);
        $paging->setCurrentPage(6);
        $this->assertEquals(12, $paging->getLastPage());
        $paging = $this->_createPagination(5);
        $paging->setTotal(62);
        $paging->setCurrentPage(6);
        $this->assertEquals(13, $paging->getLastPage());
        $paging = $this->_createPagination(5);
        $paging->setTotal(0);
        $paging->setCurrentPage(1);
        $this->assertEquals(0, $paging->getLastPage());
    }

    /**
     * @test
     */
    public function calculatesPreviousPage()
    {
        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(6);
        $paging->setTotal(60);
        $this->assertTrue($paging->hasPreviousPage());
        $this->assertEquals(5, $paging->getPreviousPage());

        $paging = $this->_createPagination(5);
        $paging->setTotal(60);
        $paging->setCurrentPage(1);
        $this->assertFalse($paging->hasPreviousPage());
        $this->assertEquals(1, $paging->getPreviousPage());
    }

    /**
     * @test
     */
    public function calculatesNextPage()
    {
        $paging = $this->_createPagination(10);
        $paging->setTotal(60);
        $paging->setCurrentPage(6);
        $this->assertFalse($paging->hasNextPage());
        $this->assertEquals(6, $paging->getNextPage());
        $paging = $this->_createPagination(5);
        $paging->setTotal(60);
        $paging->setCurrentPage(2);
        $this->assertTrue($paging->hasNextPage());
        $this->assertEquals(3, $paging->getNextPage());
    }

    /**
     * @test
     */
    public function offsetCannotBeNegative()
    {
        $paging = $this->_createPagination(10);
        $paging->setTotal(10);
        $paging->setCurrentPage(1);
        $this->assertEquals(0, $paging->getOffset()); // perpage * current page
    }

    /**
     * @test
     */
    public function daterminesWhetherPaginationHasAnyPages()
    {
        $paging = $this->_createPagination(10);
        $paging->setCurrentPage(1);
        $paging->setTotal(10);
        $this->assertFalse($paging->hasPages());

        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(1);
        $paging->setTotal(10);
        $this->assertTrue($paging->hasPages());

        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(2);
        $paging->setTotal(10);
        $this->assertTrue($paging->hasPages());

        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(1);
        $paging->setTotal(0);
        $this->assertFalse($paging->hasPages());
    }

    /**
     * @test
     */
    public function generatesUrlForPreviousAndNextPage()
    {
        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(2);
        $paging->setTotal(20);
        $this->assertEquals("3", $paging->getNextUrl());
        $this->assertEquals("1", $paging->getPreviousUrl());
    }

    /**
     * @test
     */
    public function doesnotGenerateUrlIfThereIsNoPage()
    {
        $paging = $this->_createPagination(5);
        $paging->setCurrentPage(1);
        $paging->setTotal(3);
        $this->assertNull($paging->getNextUrl());
        $this->assertNull($paging->getPreviousUrl());
    }

    /**
     * @return \Entvalley\AppBundle\Service\Pagination
     */
    private function _createPagination($perPage)
    {
        $urlGeneratorMock = $this->getMock('Entvalley\AppBundle\Service\PaginationUrlGenerator');

        $urlGeneratorMock->expects($this->any())
            ->method('generate')
            ->will($this->returnArgument(0));

        return new Pagination($perPage, $urlGeneratorMock);
    }
}
