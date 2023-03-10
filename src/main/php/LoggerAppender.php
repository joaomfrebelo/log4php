<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
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
 * Abstract class that defines output logs strategies.
 *
 * @version $Revision$
 * @package log4php
 *
 * @note File changed by Joao M F Rebelo
 */
abstract class LoggerAppender extends LoggerConfigurable
{

    /**
     * Set to true when the appender is closed. A closed appender will not
     * accept any logging requests.
     * @var boolean
     */
    protected bool $closed = false;

    /**
     * The first filter in the filter chain.
     * @var LoggerFilter|null
     */
    protected ?LoggerFilter $filter = null;

    /**
     * The appender's layout. Can be null if the appender does not use
     * a layout.
     * @var LoggerLayout|null
     */
    protected ?LoggerLayout $layout = null;

    /**
     * Appender name. Used by other components to identify this appender.
     * @var string
     */
    protected string $name;

    /**
     * Appender threshold level. Events whose level is below the threshold
     * will not be logged.
     * @var LoggerLevel|null
     */
    protected ?LoggerLevel $threshold = null;

    /**
     * Set to true if the appender requires a layout.
     *
     * True by default, appenders which do not use a layout should override
     * this property to false.
     *
     * @var boolean
     */
    protected bool $requiresLayout = true;

    /**
     * Default constructor.
     * @param string $name Appender name
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;

        if ($this->requiresLayout) {
            $this->layout = $this->getDefaultLayout();
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * Returns the default layout for this appender. Can be overriden by
     * derived appenders.
     *
     * @return LoggerLayout
     */
    public function getDefaultLayout(): LoggerLayout
    {
        return new LoggerLayoutSimple();
    }

    /**
     * Adds a filter to the end of the filter chain.
     * @param LoggerFilter $filter add a new LoggerFilter
     */
    public function addFilter(LoggerFilter $filter)
    {
        if ($this->filter === null) {
            $this->filter = $filter;
        } else {
            $this->filter->addNext($filter);
        }
    }

    /**
     * Clears the filter chain by removing all the filters in it.
     */
    public function clearFilters()
    {
        $this->filter = null;
    }

    /**
     * Returns the first filter in the filter chain.
     * The return value may be <i>null</i> if no is filter is set.
     * @return LoggerFilter|null
     */
    public function getFilter(): ?LoggerFilter
    {
        return $this->filter;
    }

    /**
     * Returns the first filter in the filter chain.
     * The return value may be <i>null</i> if no is filter is set.
     * @return LoggerFilter|null
     */
    public function getFirstFilter(): ?LoggerFilter
    {
        return $this->filter;
    }

    /**
     * Performs threshold checks and invokes filters before delegating logging
     * to the subclass' specific <i>append()</i> method.
     * @param LoggerLoggingEvent $event
     * @return mixed
     * @see LoggerAppender::append()
     */
    public function doAppend(LoggerLoggingEvent $event): mixed
    {
        if ($this->closed) {
            return null;
        }

        if (!$this->isAsSevereAsThreshold($event->getLevel())) {
            return null;
        }

        $filter = $this->getFirstFilter();
        while ($filter !== null) {
            switch ($filter->decide($event)) {
                case LoggerFilter::DENY:
                    return null;
                case LoggerFilter::ACCEPT:
                    return $this->append($event);
                case LoggerFilter::NEUTRAL:
                    $filter = $filter->getNext();
            }
        }
        $this->append($event);
        return null;
    }

    /**
     * Sets the appender layout.
     * @param LoggerLayout $layout
     */
    public function setLayout(LoggerLayout $layout)
    {
        if ($this->requiresLayout()) {
            $this->layout = $layout;
        }
    }

    /**
     * Returns the appender layout.
     * @return LoggerLayout|null
     */
    public function getLayout(): ?LoggerLayout
    {
        return $this->layout;
    }

    /**
     * Configurators call this method to determine if the appender
     * requires a layout.
     *
     * <p>If this method returns <i>true</i>, meaning that layout is required,
     * then the configurator will configure a layout using the configuration
     * information at its disposal.     If this method returns <i>false</i>,
     * meaning that a layout is not required, then layout configuration will be
     * skipped even if there is available layout configuration
     * information at the disposal of the configurator.</p>
     *
     * <p>In the rather exceptional case, where the appender
     * implementation admits a layout but can also work without it, then
     * the appender should return <i>true</i>.</p>
     *
     * @return boolean
     */
    public function requiresLayout(): bool
    {
        return $this->requiresLayout;
    }

    /**
     * Retruns the appender name.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the appender name.
     * @param string $name
     * @noinspection PhpUnused
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the appender's threshold level.
     * @return LoggerLevel
     */
    public function getThreshold(): LoggerLevel
    {
        return $this->threshold ?? LoggerLevel::getLevelAll();
    }

    /**
     * Sets the appender threshold.
     *
     * @param LoggerLevel|string $threshold Either a {@link LoggerLevel}
     *   object or a string equivalent.
     * @see LoggerOptionConverter::toLevel()
     */
    public function setThreshold(LoggerLevel|string $threshold)
    {
        $this->setLevel('threshold', $threshold);
    }

    /**
     * Checks whether the message level is below the appender's threshold.
     *
     * If there is no threshold set, then the return value is always <i>true</i>.
     *
     * @param LoggerLevel $level
     * @return boolean Returns true if level is greater or equal than
     *   threshold, or if the threshold is not set. Otherwise, returns false.
     */
    public function isAsSevereAsThreshold(LoggerLevel $level): bool
    {
        return $level->isGreaterOrEqual($this->getThreshold());
    }

    /**
     * Prepares the appender for logging.
     *
     * Derived appenders should override this method if option structure
     * requires it.
     */
    public function activateOptions()
    {
        $this->closed = false;
    }

    /**
     * Forwards the logging event to the destination.
     *
     * Derived appenders should implement this method to perform actual logging.
     *
     * @param LoggerLoggingEvent $event
     */
    abstract protected function append(LoggerLoggingEvent $event);

    /**
     * Releases any resources allocated by the appender.
     *
     * Derived appenders should override this method to perform proper closing
     * procedures.
     */
    public function close()
    {
        $this->closed = true;
    }

    /** Triggers a warning for this logger with the given message. */
    protected function warn($message): void
    {
        $id = get_class($this) . (empty($this->name) ? '' : ":$this->name");
        trigger_error("log4php: [$id]: $message", E_USER_WARNING);
    }

}
