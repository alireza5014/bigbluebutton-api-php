<?php

/*
 * BigBlueButton open source conferencing system - https://www.bigbluebutton.org/.
 *
 * Copyright (c) 2016-2022 BigBlueButton Inc. and by respective authors (see below).
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

use Illuminate\Support\Facades\Log;

/**
 * Class UpdateRecordingsParameters.
 */
class UpdateRecordingsParameters extends MetaParameters
{
    /**
     * @var string
     */
    private $recordingId;
    private $metadata=[];

    /**
     * UpdateRecordingsParameters constructor.
     *
     * @param $recordingId
     */
    public function __construct($recordingId)
    {
        $this->recordingId = $recordingId;
    }

    /**
     * @return string
     */
    public function getRecordingId()
    {
        return $this->recordingId;
    }

    public function setMetadata($key, $value)
    {
        $data = ['meta_' . $key => $value];
        $this->metadata = array_merge($data, $this->metadata);

        return $this;
    }

    /**
     * @param string $recordingId
     *
     * @return UpdateRecordingsParameters
     */
    public function setRecordingId($recordingId)
    {
        $this->recordingId = $recordingId;

        return $this;
    }

    /**
     * @return string
     */
    public function getHTTPQuery()
    {
        $queries = [
            'recordID' => $this->recordingId,
        ];
        $queries=array_merge($queries,$this->metadata);
        $this->buildMeta($queries);

        return $this->buildHTTPQuery($queries);
    }
}
