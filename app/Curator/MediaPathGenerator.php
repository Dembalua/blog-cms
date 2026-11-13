<?php

namespace App\Curator;

use Awcodes\Curator\PathGenerators\Contracts\PathGenerator;


class MediaPathGenerator implements PathGenerator
{
    public function getPath(?string $baseDir = null): string
    {
        return ($baseDir ? $baseDir . '/' : '') . 'media';
    }
}
