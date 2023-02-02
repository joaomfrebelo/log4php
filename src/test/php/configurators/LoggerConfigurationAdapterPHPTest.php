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
 * @category   tests
 * @package       log4php
 * @subpackage configurators
 * @license       http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version    $Revision$
 * @link       http://logging.apache.org/log4php
 */

use PHPUnit\Framework\TestCase;

/**
 * @group configurators
 */
class LoggerConfigurationAdapterPHPTest extends TestCase
{

    private array $expected1
        = array(
            'rootLogger' => array(
                'level' => 'info',
                'appenders' => array('default')
            ),
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderEcho',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple'
                    )
                )
            )
        );

    /**
     * @throws LoggerException
     */
    public function testConfig()
    {
        $url     = PHPUNIT_CONFIG_DIR . '/adapters/php/config_valid.php';
        $adapter = new LoggerConfigurationAdapterPHP();
        $actual  = $adapter->convert($url);

        $this->assertSame($this->expected1, $actual);
    }

    /**
     * Test exception is thrown when file cannot be found.
     * File [you/will/never/find/me.conf] does not exist.
     */
    public function testNonExistentFileWarning()
    {
        $this->expectException(LoggerException::class);
        $this->expectExceptionMessage("File [you/will/never/find/me.conf] does not exist.");
        $adapter = new LoggerConfigurationAdapterPHP();
        $adapter->convert('you/will/never/find/me.conf');
    }

    /**
     * Test exception is thrown when file is not valid.
     * Error parsing configuration: syntax error
     * @throws LoggerException
     */
    public function testInvalidFileWarning()
    {
        $this->expectError();
        $this->expectErrorMessage('syntax error, unexpected token ";", expecting ")');
        $url     = PHPUNIT_CONFIG_DIR . '/adapters/php/config_invalid_syntax.php';
        $adapter = new LoggerConfigurationAdapterPHP();
        $adapter->convert($url);
    }

    /**
     * Test exception is thrown when the configuration is empty.
     * Invalid configuration: empty configuration array.
     */
    public function testEmptyConfigWarning()
    {
        $this->expectException(LoggerException::class);
        $this->expectExceptionMessage('Invalid configuration: empty configuration array.');
        $url     = PHPUNIT_CONFIG_DIR . '/adapters/php/config_empty.php';
        $adapter = new LoggerConfigurationAdapterPHP();
        $adapter->convert($url);
    }

    /**
     * Test exception is thrown when the configuration does not contain an array.
     * Invalid configuration: not an array.
     */
    public function testInvalidConfigWarning()
    {
        $this->expectException(LoggerException::class);
        $this->expectExceptionMessage('Invalid configuration: not an array.');
        $url     = PHPUNIT_CONFIG_DIR . '/adapters/php/config_not_an_array.php';
        $adapter = new LoggerConfigurationAdapterPHP();
        $adapter->convert($url);
    }
}
