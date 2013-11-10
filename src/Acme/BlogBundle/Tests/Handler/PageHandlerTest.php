<?php

namespace Acme\BlogBundle\Tests\Handler;

use Acme\BlogBundle\Handler\PageHandler;
use Acme\BlogBundle\Model\PageInterface;
use Acme\BlogBundle\Entity\Page;

class PageHandlerTest extends \PHPUnit_Framework_TestCase
{
    const PAGE_CLASS = 'Acme\BlogBundle\Tests\Handler\DummyPage';

    /** @var PageHandler */
    protected $pageHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::PAGE_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::PAGE_CLASS));


    }

    public function testGet()
    {
        $id = 1;
        $page = $this->getPage();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($page));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $this->pageHandler->get($id);
    }

    protected function createPageHandler($objectManager, $pageClass, $formFactory)
    {
        return new PageHandler($objectManager, $pageClass, $formFactory);
    }

    protected function getPage()
    {
        $pageClass = static::PAGE_CLASS;

        return new $pageClass();
    }
}

class DummyPage extends Page
{
}
