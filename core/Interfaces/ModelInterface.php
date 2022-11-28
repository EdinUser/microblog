<?php

namespace MicroBlog\Interfaces;

interface ModelInterface
{
    function read(int $id);

    function save($data);
}