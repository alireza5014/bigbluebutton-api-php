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

use Alireza5014\Responses\UpdateRecordingsResponse;
use Alireza5014\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UpdateRecordingsResponseTest extends TestCase
{
    /**
     * @var \Alireza5014\Responses\UpdateRecordingsResponse
     */
    private $update;

    public function setUp(): void
    {
        parent::setUp();

        $xml = $this->loadXmlFile(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'update_recordings.xml');

        $this->update = new UpdateRecordingsResponse($xml);
    }

    public function testUpdateRecordingsResponseContent()
    {
        $this->assertEquals('SUCCESS', $this->update->getReturnCode());
        $this->assertEquals(true, $this->update->isUpdated());
    }

    public function testUpdateRecordingsResponseTypes()
    {
        $this->assertEachGetterValueIsString($this->update, ['getReturnCode']);
        $this->assertEachGetterValueIsBoolean($this->update, ['isUpdated']);
    }
}
