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

use Alireza5014\Core\Hook;

/**
 * Class GetRecordingsResponse.
 */
class HooksListResponse extends BaseResponse
{
    /**
     * @var Hook[]
     */
    private $hooks;

    /**
     * @return Hook[]
     */
    public function getHooks()
    {
        if (null === $this->hooks) {
            $this->hooks = [];
            foreach ($this->rawXml->hooks->children() as $hookXml) {
                $this->hooks[] = new Hook($hookXml);
            }
        }

        return $this->hooks;
    }
}
