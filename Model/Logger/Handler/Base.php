<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ShopGo\AdvancedLogger\Model\Logger\Handler;

use Magento\Framework\Filesystem\DriverInterface;
use Monolog\Logger;

class Base extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Default log base directory path
     */
    const DEFAULT_BASE_PATH = '/var/log/';

    /**
     * @param DriverInterface $filesystem
     * @param string $filePath
     * @param string $logNamespacePath
     * @param string $logModulePath
     * @param string $fileName
     * @param string $loggerType
     */
    public function __construct(
        DriverInterface $filesystem,
        $filePath = null,
        $logNamespacePath = '',
        $logModulePath = '',
        $fileName = '',
        $loggerType = ''
    ) {
        $loggerLevels = Logger::getLevels();
        $loggerType   = strtoupper($loggerType);

        if ($fileName) {
            $this->fileName = $fileName;
        }
        if (isset($loggerLevels[$loggerType])) {
            $this->loggerType = $loggerLevels[$loggerType];
        }

        $path = $logNamespacePath . $logModulePath;
        $this->fileName = $this->getPath($path . $this->fileName);

        parent::__construct($filesystem, $filePath);
    }

    /**
     * Get log directory/file full path
     *
     * @param array|string $path
     * @param bool $isRealPath
     * @return string
     */
    public function getPath($path = null, $isRealPath = false)
    {
        $rootPath = '';
        if ($isRealPath) {
            $rootPath = BP;
        }

        if (gettype($path) == 'array') {
            $path = implode('', $path);
        }

        return $rootPath . static::DEFAULT_BASE_PATH . $path;
    }
}
