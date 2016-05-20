<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\AdvancedLogger\Model;

use Monolog\Logger as MonologLogger;
use ShopGo\AdvancedLogger\Model\Logger\Handler\Base as LoggerHandlerBase;

class Logger
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var LoggerHandlerBase
     */
    protected $loggerHandlerBase;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param LoggerHandlerBase $loggerHandlerBase
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        LoggerHandlerBase $loggerHandlerBase
    ) {
        $this->logger = $logger;
        $this->loggerHandlerBase = $loggerHandlerBase;
    }

    /**
    * Get Monolog
    *
    * @return \Psr\Log\LoggerInterface
    */
    public function getMonolog()
    {
        return $this->logger;
    }

    /**
     * Get log levels
     *
     * @return array
     */
    public function getLogLevels()
    {
        return MonologLogger::getLevels();
    }

    /**
     * Add a log record
     *
     * @param int|string $level
     * @param mixed $message
     * @param array $context
     * @param string $file
     * @param array|string $path
     * @return bool
     */
    public function log($level, $message, array $context = [], $file = '', $path = null)
    {
        if (gettype($level) == 'string') {
            $loggerLevels = $this->getLogLevels();
            $level = strtoupper($level);
            $level = isset($loggerLevels[$level])
                ? $loggerLevels[$level]
                : MonologLogger::DEBUG;
        }

        if (gettype($message) == 'array' || gettype($message) == 'object') {
            $message = print_r($message, true);
        }

        if ($this->logger instanceOf \ShopGo\AdvancedLogger\Model\Logger\Monolog) {
            $stream = $file
                ? $this->loggerHandlerBase->getPath($path, true) . $file
                : '';

            $result = $this->logger->addRecord($level, $message, $context, $stream);
        } else {
            $result = $this->logger->addRecord($level, $message, $context);
        }

        return $result;
    }
}
