<?php

namespace Acme\BlogBundle\Handler;

interface PageHandlerInterface
{
    /**
     * Get a Page.
     *
     * @param mixed $id
     *
     * @return PageInterface
     */
    public function get($id);
}