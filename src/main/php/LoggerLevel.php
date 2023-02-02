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
 * Defines the minimum set of levels recognized by the system, that is
 * <i>OFF</i>, <i>FATAL</i>, <i>ERROR</i>,
 * <i>WARN</i>, <i>INFO</i>, <i>DEBUG</i> and
 * <i>ALL</i>.
 *
 * <p>The <i>LoggerLevel</i> class may be subclassed to define a larger
 * level set.</p>
 *
 * @version $Revision$
 * @package log4php
 * @since 0.5
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerLevel
{

    const OFF = 2147483647;
    const FATAL = 50000;
    const ERROR = 40000;
    const WARN = 30000;
    const INFO = 20000;
    const DEBUG = 10000;
    const TRACE = 5000;
    const ALL = -2147483647;

    /** Integer level value. */
    private int $level;

    /** Contains a list of instantiated levels. */
    private static mixed $levelMap;

    /** String representation of the level. */
    private string $levelStr;

    /**
     * Equivalent syslog level.
     * @var integer
     */
    private int $syslogEquivalent;

    /**
     * Constructor
     *
     * @param integer $level
     * @param string $levelStr
     * @param integer $syslogEquivalent
     */
    private function __construct(int $level, string $levelStr, int $syslogEquivalent)
    {
        $this->level            = $level;
        $this->levelStr         = $levelStr;
        $this->syslogEquivalent = $syslogEquivalent;
    }

    /**
     * Compares two logger levels.
     *
     * @param mixed $other
     * @return boolean
     */
    public function equals(mixed $other): bool
    {
        if ($other instanceof LoggerLevel) {
            if ($this->level == $other->level) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an Off Level
     * @return LoggerLevel
     */
    public static function getLevelOff(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::OFF])) {
            self::$levelMap[LoggerLevel::OFF] = new LoggerLevel(LoggerLevel::OFF, 'OFF', LOG_ALERT);
        }
        return self::$levelMap[LoggerLevel::OFF];
    }

    /**
     * Returns a Fatal Level
     * @return LoggerLevel
     */
    public static function getLevelFatal(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::FATAL])) {
            self::$levelMap[LoggerLevel::FATAL] = new LoggerLevel(LoggerLevel::FATAL, 'FATAL', LOG_ALERT);
        }
        return self::$levelMap[LoggerLevel::FATAL];
    }

    /**
     * Returns an Error Level
     * @return LoggerLevel
     */
    public static function getLevelError(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::ERROR])) {
            self::$levelMap[LoggerLevel::ERROR] = new LoggerLevel(LoggerLevel::ERROR, 'ERROR', LOG_ERR);
        }
        return self::$levelMap[LoggerLevel::ERROR];
    }

    /**
     * Returns a Warn Level
     * @return LoggerLevel
     */
    public static function getLevelWarn(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::WARN])) {
            self::$levelMap[LoggerLevel::WARN] = new LoggerLevel(LoggerLevel::WARN, 'WARN', LOG_WARNING);
        }
        return self::$levelMap[LoggerLevel::WARN];
    }

    /**
     * Returns an Info Level
     * @return LoggerLevel
     */
    public static function getLevelInfo(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::INFO])) {
            self::$levelMap[LoggerLevel::INFO] = new LoggerLevel(LoggerLevel::INFO, 'INFO', LOG_INFO);
        }
        return self::$levelMap[LoggerLevel::INFO];
    }

    /**
     * Returns a Debug Level
     * @return LoggerLevel
     */
    public static function getLevelDebug(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::DEBUG])) {
            self::$levelMap[LoggerLevel::DEBUG] = new LoggerLevel(LoggerLevel::DEBUG, 'DEBUG', LOG_DEBUG);
        }
        return self::$levelMap[LoggerLevel::DEBUG];
    }

    /**
     * Returns a Trace Level
     * @return LoggerLevel
     */
    public static function getLevelTrace(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::TRACE])) {
            self::$levelMap[LoggerLevel::TRACE] = new LoggerLevel(LoggerLevel::TRACE, 'TRACE', LOG_DEBUG);
        }
        return self::$levelMap[LoggerLevel::TRACE];
    }

    /**
     * Returns a Level
     * @return LoggerLevel
     */
    public static function getLevelAll(): LoggerLevel
    {
        if (!isset(self::$levelMap[LoggerLevel::ALL])) {
            self::$levelMap[LoggerLevel::ALL] = new LoggerLevel(LoggerLevel::ALL, 'ALL', LOG_DEBUG);
        }
        return self::$levelMap[LoggerLevel::ALL];
    }

    /**
     * Return the syslog equivalent of this level as an integer.
     * @return integer
     */
    public function getSyslogEquivalent(): int
    {
        return $this->syslogEquivalent;
    }

    /**
     * Returns <i>true</i> if this level has a higher or equal
     * level than the level passed as argument, <i>false</i>
     * otherwise.
     *
     * @param LoggerLevel $other
     * @return boolean
     */
    public function isGreaterOrEqual(LoggerLevel $other): bool
    {
        return $this->level >= $other->level;
    }

    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function toString(): string
    {
        return $this->levelStr;
    }

    /**
     * Returns the string representation of this level.
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Returns the integer representation of this level.
     * @return integer
     */
    public function toInt(): int
    {
        return $this->level;
    }

    /**
     * Convert the input argument to a level. If the conversion fails, then
     * this method returns the provided default level.
     *
     * @param mixed $arg The value to convert to level.
     * @param null $defaultLevel
     * @return LoggerLevel|null
     */
    public static function toLevel(mixed $arg, $defaultLevel = null): ?LoggerLevel
    {
        if (is_int($arg)) {
            return match ($arg) {
                self::ALL => self::getLevelAll(),
                self::TRACE => self::getLevelTrace(),
                self::DEBUG => self::getLevelDebug(),
                self::INFO => self::getLevelInfo(),
                self::WARN => self::getLevelWarn(),
                self::ERROR => self::getLevelError(),
                self::FATAL => self::getLevelFatal(),
                self::OFF => self::getLevelOff(),
                default => $defaultLevel,
            };
        } else {
            return match (strtoupper($arg)) {
                'ALL' => self::getLevelAll(),
                'TRACE' => self::getLevelTrace(),
                'DEBUG' => self::getLevelDebug(),
                'INFO' => self::getLevelInfo(),
                'WARN' => self::getLevelWarn(),
                'ERROR' => self::getLevelError(),
                'FATAL' => self::getLevelFatal(),
                'OFF' => self::getLevelOff(),
                default => $defaultLevel,
            };
        }
    }
}
