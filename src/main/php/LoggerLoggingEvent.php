<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * The internal representation of logging event.
 *
 * @version $Revision$
 * @package log4php
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerLoggingEvent
{

    private static float $startTime;

    /**
     * @var string Fully Qualified Class Name of the calling category class.
     */
    private string $fqcn;

    /**
     * @var Logger reference
     */
    private Logger $logger;

    /**
     * The category (logger) name.
     * This field will be marked as private in future
     */
    private string $categoryName;

    /**
     * Level of the logging event.
     * @var LoggerLevel
     */
    protected LoggerLevel $level;

    /**
     * The nested diagnostic context (NDC) of logging event.
     * @var mixed
     */
    private mixed $ndc = null;

    /**
     * Have we tried to do an NDC lookup? If we did, there is no need
     * to do it again.    Note that its value is always false when
     * serialized. Thus, a receiving SocketNode will never use its own
     * (incorrect) NDC. See also writeObject method.
     * @var boolean
     */
    private bool $ndcLookupRequired = true;

    /**
     * @var mixed The application supplied message of logging event.
     */
    private mixed $message;

    /**
     * The application supplied message rendered through the log4php
     * objet rendering mechanism. At present renderedMessage == message.
     * @var string|null
     */
    private ?string $renderedMessage = null;

    /**
     * The name of thread in which this logging event was generated.
     * log4php saves here the process id via {@link PHP_MANUAL#getmypid getmypid()}
     * @var mixed
     */
    private mixed $threadName = null;

    /**
     * The number of seconds elapsed from 1/1/1970 until logging event
     * was created plus microseconds if available.
     * @var string|int|float
     */
    public string|int|float $timeStamp;

    /**
     * @var LoggerLocationInfo|null Location information for the caller.
     */
    private ?LoggerLocationInfo $locationInfo = null;

    /**
     * @var LoggerThrowableInformation log4php internal representation of throwable
     */
    private LoggerThrowableInformation $throwableInfo;

    /**
     * Instantiate a LoggingEvent from the supplied parameters.
     *
     * Except {@link $timeStamp} all the other fields of
     * LoggerLoggingEvent are filled when actually needed.
     *
     * @param string $fqcn name of the caller class.
     * @param mixed $logger The {@link Logger} category of this event or the logger name.
     * @param LoggerLevel $level The level of this event.
     * @param mixed $message The message of this event.
     * @param int|float|null $timeStamp the timestamp of this logging event.
     * @param Exception|null $throwable The throwable associated with logging event
     */
    public function __construct(string $fqcn, mixed $logger, LoggerLevel $level, mixed $message, int|float|null $timeStamp = null, Exception $throwable = null)
    {
        $this->fqcn = $fqcn;
        if ($logger instanceof Logger) {
            $this->logger       = $logger;
            $this->categoryName = $logger->getName();
        } else {
            $this->categoryName = strval($logger);
        }
        $this->level   = $level;
        $this->message = $message;
        if (is_numeric($timeStamp)) {
            $this->timeStamp = $timeStamp;
        } else {
            $this->timeStamp = microtime(true);
        }

        if ($throwable instanceof Throwable) {
            $this->throwableInfo = new LoggerThrowableInformation($throwable);
        }
    }

    /**
     * Returns the full qualified classname.
     * TODO: PHP does contain namespaces in 5.3. Those should be returned too,
     * @noinspection PhpUnused
     */
    public function getFullQualifiedClassname(): string
    {
        return $this->fqcn;
    }

    /**
     * Set the location information for this logging event. The collected
     * information is cached for future use.
     *
     * <p>This method uses {@link PHP_MANUAL#debug_backtrace debug_backtrace()} function (if exists)
     * to collect informations about caller.</p>
     * <p>It only recognize information generated by {@link Logger} and its subclasses.</p>
     * @return LoggerLocationInfo
     */
    public function getLocationInformation(): LoggerLocationInfo
    {
        if ($this->locationInfo === null) {

            $locationInfo = array();
            $trace        = debug_backtrace();
            $prevHop      = null;
            // make a downsearch to identify the caller
            $hop = array_pop($trace);
            while ($hop !== null) {
                if (isset($hop['class'])) {
                    // we are sometimes in functions = no class available: avoid php warning here
                    $className = strtolower($hop['class']);
                    if (!empty($className) and ($className == 'logger' or
                            strtolower(get_parent_class($className)) == 'logger')) {
                        $locationInfo['line'] = $hop['line'];
                        $locationInfo['file'] = $hop['file'];
                        break;
                    }
                }
                $prevHop = $hop;
                $hop     = array_pop($trace);
            }
            $locationInfo['class'] = $prevHop['class'] ?? 'main';
            if (isset($prevHop['function']) and
                $prevHop['function'] !== 'include' and
                $prevHop['function'] !== 'include_once' and
                $prevHop['function'] !== 'require' and
                $prevHop['function'] !== 'require_once') {

                $locationInfo['function'] = $prevHop['function'];
            } else {
                $locationInfo['function'] = 'main';
            }

            $this->locationInfo = new LoggerLocationInfo($locationInfo);
        }
        return $this->locationInfo;
    }

    /**
     * Return the level of this event. Use this form instead of directly
     * accessing the {@link $level} field.
     * @return LoggerLevel
     */
    public function getLevel(): LoggerLevel
    {
        return $this->level;
    }

    /**
     * Returns the logger which created the event.
     * @return Logger
     * @noinspection PhpUnused
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * Return the name of the logger. Use this form instead of directly
     * accessing the {@link $categoryName} field.
     * @return string
     */
    public function getLoggerName(): string
    {
        return $this->categoryName;
    }

    /**
     * Return the message for this logging event.
     * @return mixed
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * This method returns the NDC for this event. It will return the
     * correct content even if the event was generated in a different
     * thread or even on a different machine. The {@link LoggerNDC::get()} method
     * should <b>never</b> be called directly.
     * @return string
     */
    public function getNDC(): string
    {
        if ($this->ndcLookupRequired) {
            $this->ndcLookupRequired = false;
            $this->ndc               = LoggerNDC::get();
        }
        return $this->ndc;
    }

    /**
     * Returns the context corresponding to the <code>key</code>
     * parameter.
     * @param $key
     * @return string
     */
    public function getMDC($key): string
    {
        return LoggerMDC::get($key);
    }

    /**
     * Returns the entire MDC context.
     * @return array
     */
    public function getMDCMap(): array
    {
        return LoggerMDC::getMap();
    }

    /**
     * Render message.
     * @return string|null
     */
    public function getRenderedMessage(): ?string
    {
        if ($this->renderedMessage === null and $this->message !== null) {
            if (is_string($this->message)) {
                $this->renderedMessage = $this->message;
            } else {
                $rendererMap           = Logger::getHierarchy()->getRendererMap();
                $this->renderedMessage = $rendererMap->findAndRender($this->message);
            }
        }
        return $this->renderedMessage;
    }

    /**
     * Returns the time when the application started, as a UNIX timestamp
     * with microseconds.
     * @return float
     */
    public static function getStartTime(): float
    {
        if (!isset(self::$startTime)) {
            self::$startTime = microtime(true);
        }
        return self::$startTime;
    }

    /**
     * @return float|int|string
     */
    public function getTimeStamp(): float|int|string
    {
        return $this->timeStamp;
    }

    /**
     * Returns the time in seconds passed from the beginning of execution to
     * the time the event was constructed.
     *
     * @return float|int|string Seconds with microseconds in decimals.
     */
    public function getRelativeTime(): float|int|string
    {
        return $this->timeStamp - self::$startTime;
    }

    /**
     * Returns the time in milliseconds passed from the beginning of execution
     * to the time the event was constructed.
     *
     * @return integer
     * @deprecated This method has been replaced by getRelativeTime which
     *        does not perform unnecessary multiplication and formatting.
     *
     * @noinspection PhpUnused
     */
    public function getTime(): int
    {
        $eventTime      = $this->getTimeStamp();
        $eventStartTime = LoggerLoggingEvent::getStartTime();
        return number_format(($eventTime - $eventStartTime) * 1000, 0, '', '');
    }

    /**
     * @return mixed
     */
    public function getThreadName(): mixed
    {
        if ($this->threadName === null) {
            $this->threadName = (string)getmypid();
        }
        return $this->threadName;
    }

    /**
     * @return LoggerThrowableInformation|null LoggerThrowableInformation
     */
    public function getThrowableInformation(): ?LoggerThrowableInformation
    {
        return $this->throwableInfo ?? null;
    }

    /**
     * Serialize this object
     * @return string
     * @noinspection PhpUnused
     */
    public function toString(): string
    {
        return serialize($this);
    }

    /**
     * Avoid serialization of the {@link $logger} object
     */
    public function __sleep()
    {
        return array(
            'fqcn',
            'categoryName',
            'level',
            'ndc',
            'ndcLookupRequired',
            'message',
            'renderedMessage',
            'threadName',
            'timeStamp',
            'locationInfo',
        );
    }

}

LoggerLoggingEvent::getStartTime();
