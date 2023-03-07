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

use Alireza5014\Responses\JoinMeetingResponse;
use Alireza5014\TestCase;

/**
 * @internal
 * @coversNothing
 */
class JoinMeetingResponseTest extends TestCase
{
    /**
     * @var \Alireza5014\Responses\JoinMeetingResponse
     */
    private $joinMeeting;

    public function setUp(): void
    {
        parent::setUp();

        $xml = $this->loadXmlFile(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'join_meeting.xml');

        $this->joinMeeting = new JoinMeetingResponse($xml);
    }

    public function testJoinMeetingResponseContent()
    {
        $this->assertEquals('SUCCESS', $this->joinMeeting->getReturnCode());
        $this->assertEquals('successfullyJoined', $this->joinMeeting->getMessageKey());
        $this->assertEquals('You have joined successfully.', $this->joinMeeting->getMessage());
        $this->assertEquals('fa51ae0c65adef7fe3cf115421da8a6a25855a20-1464618262714', $this->joinMeeting->getMeetingId());
        $this->assertEquals('ao6ehbtvbmhz', $this->joinMeeting->getUserId());
        $this->assertEquals('huzbpgthac7s', $this->joinMeeting->getAuthToken());
        $this->assertEquals('rbe7bbkjzx5mnoda', $this->joinMeeting->getSessionToken());
        $this->assertEquals('ALLOW', $this->joinMeeting->getGuestStatus());
        $this->assertEquals('https://bigblubutton-server.sample/client/Alireza5014.html?sessionToken=0wzsph6uaelwc68z', $this->joinMeeting->getUrl());
    }

    public function testJoinMeetingResponseTypes()
    {
        $this->assertEachGetterValueIsString($this->joinMeeting, ['getReturnCode', 'getMessageKey', 'getMessage', 'getMeetingId', 'getUserId', 'getAuthToken', 'getSessionToken', 'getGuestStatus', 'getUrl']);
    }
}
