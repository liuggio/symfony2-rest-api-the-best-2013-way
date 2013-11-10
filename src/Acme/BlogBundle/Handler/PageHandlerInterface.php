<?php

namespace Acme\BlogBundle\Handler;

use Acme\BlogBundle\Model\PageInterface;

interface PageHandlerInterface
{
    /**
     * Get a Page given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return PageInterface
     */
    public function get($id);
}