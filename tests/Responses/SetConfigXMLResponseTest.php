<?php
/**
 * BigBlueButton open source conferencing system - https://www.bigbluebutton.org/.
 *
 * Copyright (c) 2016-2018 BigBlueButton Inc. and by respective authors (see below).
 *
 * This program is free software; you can redistribute it and/or modify it under the
 * terms of the GNU Lesser General Public License as published by the Free Software
 * Foundation; either version 3.0 of the License, or (at your option) any later
 * version.
 *
 * BigBlueButton is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with BigBlueButton; if not, see <http://www.gnu.org/licenses/>.
 */
namespace Alireza5014\Parameters;

use Alireza5014\Responses\SetConfigXMLResponse;
use Alireza5014\TestCase;

class SetConfigXMLResponseTest extends TestCase
{
    /**
     * @var \Alireza5014\Responses\SetConfigXMLResponse
     */
    private $config;

    public function setUp()
    {
        parent::setUp();

        $xml = $this->loadXmlFile(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'set_config_xml.xml');

        $this->config = new SetConfigXMLResponse($xml);
    }

    public function testSetConfigXMLResponseContent()
    {
        $this->assertEquals('SUCCESS', $this->config->getReturnCode());
        $this->assertEquals('TETDApIC', $this->config->getToken());
    }

    public function testSetConfigXMLResponseTypes()
    {
        $this->assertEachGetterValueIsString($this->config, ['getReturnCode']);
        $this->assertEachGetterValueIsString($this->config, ['getToken']);
    }
}
