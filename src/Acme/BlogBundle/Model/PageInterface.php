<?php

namespace Acme\BlogBundle\Model;

Interface PageInterface
{
    /**
     * Set title
     *
     * @param string $title
     * @return PageInterface
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle();

    /**
     * Set body
     *
     * @param string $body
     * @return PageInterface
     */
    public function setBody($body);

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody();
}
