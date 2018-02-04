<?php

use Symfony\Component\Config\FileLocatorInterface;

class FileLocatorStub implements FileLocatorInterface
{
    public function locate($name, $currentPath = null, $first = true)
    {
        if (substr($name, 0, 4) === 'http') {
            return $name;
        }

        return rtrim($currentPath, '/') . '/' . $name;
    }
}