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
 * The root logger.
 *
 * @version $Revision$
 * @package log4php
 * @see Logger
 *
 * @note File changed by Joao M F Rebelo
 */
class LoggerRoot extends Logger
{
    /**
     * Constructor
     *
     * @param LoggerLevel|null $level initial log level
     */
    public function __construct(?LoggerLevel $level = null)
    {
        parent::__construct('root');

        if ($level == null) {
            $level = LoggerLevel::getLevelAll();
        }
        $this->setLevel($level);
    }

    /**
     * @return LoggerLevel the level
     */
    public function getEffectiveLevel(): LoggerLevel
    {
        return $this->getLevel();
    }

    /**
     * Override level setter to prevent setting the root logger's level to
     * null. Root logger must always have a level.
     *
     * @param LoggerLevel|null $level
     */
    public function setLevel(LoggerLevel $level = null): void
    {
        if (isset($level)) {
            parent::setLevel($level);
        } else {
            trigger_error("log4php: Cannot set LoggerRoot level to null.", E_USER_WARNING);
        }
    }

    /**
     * Override parent setter. Root logger cannot have a parent.
     * @param Logger $logger
     */
    public function setParent(Logger $logger): void
    {
        trigger_error("log4php: LoggerRoot cannot have a parent.", E_USER_WARNING);
    }
}
