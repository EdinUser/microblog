<?php

namespace MicroBlog\Interfaces;

interface ModelInterface
{
    function read(array $limiters, string $returnType);

    function save($data);
}