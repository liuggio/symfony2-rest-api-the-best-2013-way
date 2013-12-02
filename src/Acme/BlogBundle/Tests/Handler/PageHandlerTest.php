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

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $pages = $this->getPages(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($pages));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);

        $all = $this->pageHandler->all($limit, $offset);

        $this->assertEquals($pages, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $pageObject = $this->pageHandler->post($parameters);

        $this->assertEquals($pageObject, $page);
    }

    /**
     * @expectedException Acme\BlogBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $this->pageHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $pageObject = $this->pageHandler->put($page, $parameters);

        $this->assertEquals($pageObject, $page);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $page = $this->getPage();
        $page->setTitle($title);
        $page->setBody($body);

        $form = $this->getMock('Acme\BlogBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($page));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->pageHandler = $this->createPageHandler($this->om, static::PAGE_CLASS,  $this->formFactory);
        $pageObject = $this->pageHandler->patch($page, $parameters);

        $this->assertEquals($pageObject, $page);
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

    protected function getPages($maxPages = 5)
    {
        $pages = array();
        for($i = 0; $i < $maxPages; $i++) {
            $pages[] = $this->getPage();
        }

        return $pages;
    }
}

class DummyPage extends Page
{
}
