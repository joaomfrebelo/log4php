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
 * The internal representation of caller location information.
 *
 * @version $Revision$
 * @package log4php
 * @since 0.3
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerLocationInfo
{

    /** The value to return when the location information is not available. */
    const LOCATION_INFO_NA = 'NA';

    /**
     * Caller line number.
     * @var integer
     */
    protected mixed $lineNumber;

    /**
     * Caller file name.
     * @var string
     */
    protected mixed $fileName;

    /**
     * Caller class name.
     * @var string
     */
    protected mixed $className;

    /**
     * Caller method name.
     * @var string
     */
    protected mixed $methodName;

    /**
     * All the information combined.
     * @var string|null
     */
    protected ?string $fullInfo = null;

    /**
     * Instantiate location information based on a {@link PHP_MANUAL#debug_backtrace}.
     *
     * @param array $trace
     */
    public function __construct(array $trace)
    {
        $this->lineNumber = $trace['line'] ?? null;
        $this->fileName   = $trace['file'] ?? null;
        $this->className  = $trace['class'] ?? null;
        $this->methodName = $trace['function'] ?? null;
        $this->fullInfo   = $this->getClassName() . '.' . $this->getMethodName() .
            '(' . $this->getFileName() . ':' . $this->getLineNumber() . ')';
    }

    /** Returns the caller class name. */
    public function getClassName()
    {
        return ($this->className === null) ? self::LOCATION_INFO_NA : $this->className;
    }

    /** Returns the caller file name. */
    public function getFileName()
    {
        return ($this->fileName === null) ? self::LOCATION_INFO_NA : $this->fileName;
    }

    /** Returns the caller line number. */
    public function getLineNumber()
    {
        return ($this->lineNumber === null) ? self::LOCATION_INFO_NA : $this->lineNumber;
    }

    /** Returns the caller method name. */
    public function getMethodName()
    {
        return ($this->methodName === null) ? self::LOCATION_INFO_NA : $this->methodName;
    }

    /** Returns the full information of the caller.
     * @noinspection PhpUnused
     */
    public function getFullInfo(): string
    {
        return ($this->fullInfo === null) ? self::LOCATION_INFO_NA : $this->fullInfo;
    }

}
