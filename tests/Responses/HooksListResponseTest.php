<?php

/*
 * Alireza5014 open source conferencing system - https://www.Alireza5014.org/.
 *
 * Copyright (c) 2016-2022 Alireza5014 Inc. and by respective authors (see below).
 *
 * This program is free software; you can redistribute it and/or modify it under the
 * terms of the GNU Lesser General Public License as published by the Free Software
 * Foundation; either version 3.0 of the License, or (at your option) any later
 * version.
 *
 * Alireza5014 is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with Alireza5014; if not, see <http://www.gnu.org/licenses/>.
 */

namespace Alireza5014\Parameters;

use Alireza5014\Responses\HooksListResponse;
use Alireza5014\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HooksListResponseTest extends TestCase
{
    /**
     * @var \Alireza5014\Responses\HooksListResponse
     */
    private $listResponse;

    public function setUp(): void
    {
        parent::setUp();

        $xml = $this->loadXmlFile(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'hooks_list.xml');

        $this->listResponse = new HooksListResponse($xml);
    }

    public function testHooksListResponseContent()
    {
        $this->assertEquals('SUCCESS', $this->listResponse->getReturnCode());
        $this->assertCount(2, $this->listResponse->getHooks());

        $aHook = $this->listResponse->getHooks()[0];

        $this->assertEquals('my-meeting', $aHook->getMeetingId());
        $this->assertEquals('http://postcatcher.in/catchers/abcdefghijk', $aHook->getCallbackUrl());
        $this->assertEquals(1, $aHook->getHookId());
        $this->assertEquals(false, $aHook->isPermanentHook());
        $this->assertEquals(false, $aHook->hasRawData());
    }

    public function testHooksListResponseTypes()
    {
        $this->assertEachGetterValueIsString($this->listResponse, ['getReturnCode']);

        $aHook = $this->listResponse->getHooks()[0];

        $this->assertEachGetterValueIsString($aHook, ['getCallbackUrl', 'getMeetingId']);
        $this->assertEachGetterValueIsInteger($aHook, ['getHookId']);
        $this->assertEachGetterValueIsBoolean($aHook, ['hasRawData', 'isPermanentHook']);
    }
}
