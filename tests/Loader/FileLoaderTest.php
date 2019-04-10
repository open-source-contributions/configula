<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

class FileLoaderTest extends TestCase
{
    public const EXPECTED_EXTENSION = '.txt';


    public function testLoadReturnsConfigValuesIfFileExists(): void
    {
        $values = $this->getLoader()->load();
        $this->assertGreaterThan(0, count($values));
    }

    public function testLoadReturnsEmptyConfigValuesIfFileNotExistsAndNotRequired(): void
    {
        @unlink($this->getTestFilePath()); // remove the file
        $values = $this->getLoader(false)->load();
        $this->assertEquals(0, count($values));
    }

    public function testLoadThowsExceptionIfFileIsNotReadableAndIsRequired(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('Could not read');
        chmod($this->getTestFilePath(), 0200); // set to write only
        $this->getLoader(true)->load();
    }

    public function testLoadThrowsExceptionIfFileNotExistsAndIsRequired(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('not found');
        @unlink($this->getTestFilePath()); // remove the file
        $this->getLoader(true)->load();
    }

    protected function getFileContents(): string
    {
        return 'a: Apple, b: Banana';
    }

    /**
     * Get test file path
     */
    final protected static function getTestFilePath()
    {
        return sys_get_temp_dir() . '/__php_configula_test_config.' . ltrim(static::EXPECTED_EXTENSION, '.');
    }

    protected function setUp(): void
    {
        file_put_contents($this->getTestFilePath(), $this->getFileContents());
    }

    protected function tearDown(): void
    {
        @unlink($this->getTestFilePath());
    }

    /**
     * @param  bool $required
     * @return FileLoader
     */
    protected function getLoader(bool $required = true): FileLoader
    {
        return new class($this->getTestFilePath(), $required) extends FileLoader {

            /**
             * Parse the contents
             *
             * @param  string $rawFileContents
             * @return array
             */
            protected function parse(string $rawFileContents): array
            {
                return ['a' => 'Apple', 'b' => 'Banana'];
            }
        };
    }
}
