<?php

namespace Rusty\Tests\Extractor;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Rusty\CodeSample;
use Rusty\Extractor;

class RstTest extends TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $fs;

    public static function documents()
    {
        return [
            'with_no_sample.rst' => self::documentWithNoSample(),
            'with_pragma.rst' => self::documentWithASampleAndPragma(),
            'with_two_samples.rst' => self::documentWithTwoSamples(),
            'with_no_php_blocks.rst' => self::documentWithNoPHPBlocks(),
        ];
    }

    /**
     * set up test environmemt
     */
    public function setUp()
    {
        $documents = self::documents();

        $this->fs = vfsStream::setup('rusty', null, array_combine(array_keys($documents), array_column($documents, 'content')));
    }

    /**
     * @dataProvider documentsProvider
     */
    public function testItExtractsSamplesFromFencedCodeBlocks($documentFile, array $documentData)
    {
        $file = $this->getFileMock($this->fs->url().'/'.$documentFile);
        $extractor = new Extractor\Rst();

        $samples = iterator_to_array($extractor->extractSamples($file));
        $this->assertCount(count($documentData['samples']), $samples);

        foreach ($documentData['samples'] as $i => $expectedSample) {
            /** @var CodeSample $sample */
            $sample = $samples[$i];

            $this->assertEquals($expectedSample['pragma'], $sample->getPragmaDirectives());
            $this->assertEquals($expectedSample['content'], $sample->getCode());
        }
    }

    public function documentsProvider()
    {
        foreach (self::documents() as $file => $document) {
            yield [$file, $document];
        }
    }

    private function getFileMock($path): \SplFileInfo
    {
        $file = $this->createMock(\SplFileInfo::class);
        $file->method('getRealPath')->willReturn($path);

        return $file;
    }

    private static function documentWithNoSample()
    {
        $document = <<<'MD'
# This is a RST document

It has no code sample.
MD;

        return [
            'content' => $document,
            'samples' => [],
        ];
    }

    private static function documentWithNoPHPBlocks()
    {
        $document = <<<'MD'
# This is a RST document

.. code-block:: bash
    rusty check -v ./src/
MD;

        return [
            'content' => $document,
            'samples' => [],
        ];
    }

    private static function documentWithASampleAndPragma()
    {
        $document = <<<'MD'
# This is a RST document

.. code-block:: php
    # ignore

    function foo() { }
MD;

        return [
            'content' => $document,
            'samples' => [
                [
                    'pragma' => ['ignore'],
                    'content' => "# ignore\n\nfunction foo() { }",
                ],
            ],
        ];
    }

    private static function documentWithTwoSamples()
    {
        $document = <<<'MD'
# This is a RST document

With a code sample:

.. code-block:: php
    echo 'hello world!';

And another one:

.. code-block:: php
    function answer() { return 42; }
MD;

        return [
            'content' => $document,
            'samples' => [
                [
                    'pragma' => [],
                    'content' => "echo 'hello world!';",
                ],
                [
                    'pragma' => [],
                    'content' => 'function answer() { return 42; }',
                ],
            ],
        ];
    }
}
