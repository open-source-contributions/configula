<?php

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Class IniFileLoader
 * @package Configula\Loader
 */
class IniFileLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var bool
     */
    private $processSections;

    /**
     * IniFileLoader constructor.
     * @param string $filePath
     * @param bool $processSections
     */
    public function __construct(string $filePath, bool $processSections = true)
    {
        $this->filePath = $filePath;
        $this->processSections = $processSections;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        try {
            $values = parse_ini_file($this->filePath, $this->processSections, INI_SCANNER_TYPED) ?: [];
        }
        catch (\Throwable $e) {
            throw new ConfigLoaderException("Error parsing INI file: " . $this->filePath);
        }

        return new ConfigValues($values ?? []);
    }
}