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

namespace Alireza5014\Responses;

/**
 * Class JoinMeetingResponse.
 */
class JoinMeetingResponse extends BaseResponse
{
    /**
     * @return string
     */
    public function getMeetingId()
    {
        return $this->rawXml->meeting_id->__toString();
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->rawXml->user_id->__toString();
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->rawXml->auth_token->__toString();
    }

    /**
     * @return string
     */
    public function getSessionToken()
    {
        return $this->rawXml->session_token->__toString();
    }

    /**
     * @return string
     */
    public function getGuestStatus()
    {
        return $this->rawXml->guestStatus->__toString();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->rawXml->url->__toString();
    }
}
