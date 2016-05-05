<?php

declare(strict_types=1);

namespace Rusty\Extractor;

interface SampleExtractor
{
    static function supportedExtensions(): array;
    function extractSamples(\SplFileInfo $file): \Traversable;
}
