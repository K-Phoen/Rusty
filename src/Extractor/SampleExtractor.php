<?php

declare(strict_types=1);

namespace Rusty\Extractor;

interface SampleExtractor
{
    function extractSamples(\SplFileInfo $file): \Traversable;
}
