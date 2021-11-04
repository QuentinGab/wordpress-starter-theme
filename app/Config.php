<?php

namespace App;

use Symfony\Component\Finder\Finder;

class Config extends \Illuminate\Config\Repository
{

    public function __construct($path)
    {
        $this->loadConfigurationFiles(
            $this->getConfigurationFiles($path)
        );
    }

    protected function loadConfigurationFiles(array $files)
    {
        foreach ($files as $key => $path) {
            $this->set($key, require_once $path);
        }
    }

    protected function getConfigurationFiles(string $path)
    {

        $files = [];
        $phpFiles = Finder::create()->files()->name('*.php')->in($path)->depth(0);

        foreach ($phpFiles as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }
}
