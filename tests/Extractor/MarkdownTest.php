<?php

namespace Rusty\Tests\Extractor;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Rusty\CodeSample;
use Rusty\Extractor;

class MarkdownTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  vfsStreamDirectory
     */
    private $fs;

    public static function documents()
    {
        return [
            'with_no_sample.md' => self::documentWithNoSample(),
            'with_pragma.md' => self::documentWithASampleAndPragma(),
            'with_two_samples.md' => self::documentWithTwoSamples(),
            'with_no_php_blocks.md' => self::documentWithNoPHPBlocks(),
            'with_indented_code_blocks.md' => self::documentWithIndentedCodeBlocks(),
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
        $file = $this->getFileMock($this->fs->url() . '/' . $documentFile);
        $extractor = new Extractor\Markdown();

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

    private function getFileMock($path)
    {
        $file = $this->getMockBuilder(\SplFileInfo::class)->disableOriginalConstructor()->getMock();
        $file
            ->expects($this->once())
            ->method('getRealPath')
            ->will($this->returnValue($path));

        return $file;
    }

    private static function documentWithNoSample()
    {
        $document = <<<MD
# This is a markdown document

It has no code sample.
MD;

        return [
            'content' => $document,
            'samples' => [],
        ];
    }

    private static function documentWithNoPHPBlocks()
    {
        $document = <<<MD
# This is a markdown document

```bash
rusty check -v ./src/
```
MD;

        return [
            'content' => $document,
            'samples' => [],
        ];
    }

    private static function documentWithASampleAndPragma()
    {
        $document = <<<MD
# This is a markdown document

```php
# ignore

function foo() { }
```
MD;

        return [
            'content' => $document,
            'samples' => [
                [
                    'pragma' => ['ignore'],
                    'content' => "# ignore\n\nfunction foo() { }",
                ]
            ],
        ];
    }

    private static function documentWithTwoSamples()
    {
        $document = <<<MD
# This is a markdown document

With a code sample:

```php
echo 'hello world!';
```

And another one:

```php
function answer() { return 42; }
```
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
                    'content' => "function answer() { return 42; }",
                ],
            ],
        ];
    }

    private static function documentWithIndentedCodeBlocks()
    {
        $document = <<<MD
# This is a markdown document

With a code sample:

    echo 'hello world!';

And another one:

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
                    'content' => "function answer() { return 42; }",
                ],
            ],
        ];
    }
}