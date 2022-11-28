<?php

namespace MicroBlog\Controllers;

use Slim\Container;

abstract class BaseController
{
    /**
     * @var Container
     */
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

}