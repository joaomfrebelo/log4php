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
 * Extend this abstract class to create your own log layout format.
 *
 * @version $Revision$
 * @package log4php
 *
 * @note File changed by Joao M F Rebelo
 */
abstract class LoggerLayout extends LoggerConfigurable
{
    /**
     * Activates options for this layout.
     * Override this method if you have options to be activated.
     */
    public function activateOptions(): bool
    {
        return true;
    }

    /**
     * Override this method to create your own layout format.
     *
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event): string
    {
        return $event->getRenderedMessage();
    }

    /**
     * Returns the content type output by this layout.
     * @return string
     */
    public function getContentType(): string
    {
        return "text/plain";
    }

    /**
     * Returns the footer for the layout format.
     * @return string|null
     */
    public function getFooter(): ?string
    {
        return null;
    }

    /**
     * Returns the header for the layout format.
     * @return string|null
     */
    public function getHeader(): ?string
    {
        return null;
    }

    /** Triggers a warning for this layout with the given message. */
    protected function warn($message): void
    {
        trigger_error("log4php: [" . get_class($this) . "]: $message", E_USER_WARNING);
    }
}
