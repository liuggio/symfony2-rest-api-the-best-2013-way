<?php

namespace Acme\BlogBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Acme\BlogBundle\Model\PageInterface;

class PageHandler implements PageHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;

    public function __construct(ObjectManager $om, $entityClass)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
    }

    /**
     * Get a Page.
     *
     * @param mixed $id
     *
     * @return PageInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    private function createPage()
    {
         return new $this->entityClass();
    }

}