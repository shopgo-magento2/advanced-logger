<?php
/**
 * Copyright © 2016 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ShopGo\AdvancedLogger\Model\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Monolog extends MonologLogger
{
    /**
     * Adds a log record
     *
     * @param int $level
     * @param string $message
     * @param array $context
     * @param resource|string $stream
     * @return bool
     */
    public function addRecord($level, $message, array $context = [], $stream = '')
    {
        $context['is_exception'] = $message instanceof \Exception;

        if (!$this->handlers && !$stream) {
            $this->pushHandler(new StreamHandler('php://stderr', static::DEBUG));
        }

        $levelName = static::getLevelName($level);

        if (!$stream) {
            // check if any handler will handle this message so we can return early and save cycles
            $handlerKey = null;
            foreach ($this->handlers as $key => $handler) {
                if ($handler->isHandling(['level' => $level])) {
                    $handlerKey = $key;
                    break;
                }
            }

            if (null === $handlerKey) {
                return false;
            }
        }

        if (!static::$timezone) {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ?: 'UTC');
        }

        $record = [
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'level_name' => $levelName,
            'channel' => $this->name,
            'datetime' => \DateTime::createFromFormat(
                'U.u',
                sprintf(
                    '%.6F',
                    microtime(true)), static::$timezone
                )->setTimezone(static::$timezone),
            'extra' => [],
        ];

        foreach ($this->processors as $processor) {
            $record = call_user_func($processor, $record);
        }

        if ($stream) {
            $handler = new StreamHandler($stream, $level);
            $handler->handle($record);
        } else {
            while (isset($this->handlers[$handlerKey]) &&
                false === $this->handlers[$handlerKey]->handle($record)) {
                $handlerKey++;
            }
        }

        return true;
    }

    /**
     * Adds a log record at an arbitrary level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @param resource|string $stream
     * @return bool
     */
    public function log($level, $message, array $context = [], $stream = '')
    {
        $level = static::toMonologLevel($level);
        return $this->addRecord($level, $message, $context, $stream);
    }
}
