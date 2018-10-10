<?php

declare(strict_types=1);

namespace Rusty\Extractor;

interface SampleExtractor
{
    public static function supportedExtensions(): array;

    public function extractSamples(\SplFileInfo $file): \Traversable;
}
