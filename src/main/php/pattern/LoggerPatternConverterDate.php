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
 * Returns the date/time of the logging request.
 *
 * Option: the datetime format, as used by the date() function. If
 * the option is not given, the default format 'c' will be used.
 *
 * There are several "special" values which can be given for this option:
 * 'ISO8601', 'ABSOLUTE' and 'DATE'.
 *
 * @package log4php
 * @subpackage pattern
 * @version $Revision$
 * @since 2.3
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerPatternConverterDate extends LoggerPatternConverter
{

    const DATE_FORMAT_ISO8601 = 'c';

    const DATE_FORMAT_ABSOLUTE = 'H:i:s';

    const DATE_FORMAT_DATE = 'd M Y H:i:s.u';

    private string $format = self::DATE_FORMAT_ISO8601;

    private array $specials
        = array(
            'ISO8601' => self::DATE_FORMAT_ISO8601,
            'ABSOLUTE' => self::DATE_FORMAT_ABSOLUTE,
            'DATE' => self::DATE_FORMAT_DATE,
        );

    private bool $useLocalDate = false;

    public function activateOptions(): void
    {

        // Parse the option (date format)
        if (!empty($this->option)) {
            if (isset($this->specials[$this->option])) {
                $this->format = $this->specials[$this->option];
            } else {
                $this->format = $this->option;
            }
        }

        // Check whether the pattern contains milliseconds (u)
        if (preg_match('/(?<!\\\\)u/', $this->format)) {
            $this->useLocalDate = true;
        }
    }

    public function convert(LoggerLoggingEvent $event): string
    {
        if ($this->useLocalDate) {
            return $this->date($this->format, $event->getTimeStamp());
        }
        return (new \DateTimeImmutable())->format($this->format);
    }

    /**
     * Currently, PHP date() function always returns zeros for milliseconds (u)
     * on Windows. This is a replacement function for date() which correctly
     * displays milliseconds on all platforms.
     *
     * It is slower than PHP date() so it should only be used if necessary.
     */
    private function date($format, $utimestamp): string
    {
        $timestamp = floor($utimestamp);
        $ms        = floor(($utimestamp - $timestamp) * 1000);
        $ms        = str_pad($ms, 3, '0', STR_PAD_LEFT);

        return date(preg_replace('`(?<!\\\\)u`', $ms, $format), $timestamp);
    }
}
