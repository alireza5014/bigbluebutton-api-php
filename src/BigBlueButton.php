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

namespace Alireza5014;

use Alireza5014\Core\ApiMethod;
use Alireza5014\Exceptions\BadResponseException;
use Alireza5014\Parameters\CreateMeetingParameters;
use Alireza5014\Parameters\DeleteRecordingsParameters;
use Alireza5014\Parameters\EndMeetingParameters;
use Alireza5014\Parameters\GetMeetingInfoParameters;
use Alireza5014\Parameters\GetRecordingsParameters;
use Alireza5014\Parameters\HooksCreateParameters;
use Alireza5014\Parameters\HooksDestroyParameters;
use Alireza5014\Parameters\IsMeetingRunningParameters;
use Alireza5014\Parameters\JoinMeetingParameters;
use Alireza5014\Parameters\PublishRecordingsParameters;
use Alireza5014\Parameters\UpdateRecordingsParameters;
use Alireza5014\Responses\ApiVersionResponse;
use Alireza5014\Responses\CreateMeetingResponse;
use Alireza5014\Responses\DeleteRecordingsResponse;
use Alireza5014\Responses\EndMeetingResponse;
use Alireza5014\Responses\GetMeetingInfoResponse;
use Alireza5014\Responses\GetMeetingsResponse;
use Alireza5014\Responses\GetRecordingsResponse;
use Alireza5014\Responses\HooksCreateResponse;
use Alireza5014\Responses\HooksDestroyResponse;
use Alireza5014\Responses\HooksListResponse;
use Alireza5014\Responses\IsMeetingRunningResponse;
use Alireza5014\Responses\JoinMeetingResponse;
use Alireza5014\Responses\PublishRecordingsResponse;
use Alireza5014\Responses\UpdateRecordingsResponse;
use Alireza5014\Util\UrlBuilder;
use SimpleXMLElement;

/**
 * Class BigBlueButton.
 */
class BigBlueButton
{
    protected $securitySecret;
    protected $bbbServerBaseUrl;
    protected $urlBuilder;
    protected $jSessionId;
    protected $timeOut = 10;
    protected $curlopts = [];

    /**
     * BigBlueButton constructor.
     *
     * @param null       $baseUrl
     * @param null       $secret
     * @param null|mixed $opts
     */
    public function __construct($baseUrl = null, $secret = null, $opts = null)
    {
        // Keeping backward compatibility with older deployed versions
        // BBB_SECRET is the new variable name and have higher priority against the old named BBB_SECURITY_SALT
        $this->securitySecret   = $secret ?: getenv('BBB_SECRET') ?: getenv('BBB_SECURITY_SALT');
        $this->bbbServerBaseUrl = $baseUrl ?: getenv('BBB_SERVER_BASE_URL');
        $this->urlBuilder       = new UrlBuilder($this->securitySecret, $this->bbbServerBaseUrl);
        $this->curlopts         = $opts['curl'] ?? [];
    }

    /**
     * @throws \RuntimeException
     *
     * @return ApiVersionResponse
     */
    public function getApiVersion()
    {
        $xml = $this->processXmlResponse($this->urlBuilder->buildUrl());

        return new ApiVersionResponse($xml);
    }

    // __________________ BBB ADMINISTRATION METHODS _________________
    /* The methods in the following section support the following categories of the BBB API:
    -- create
    -- join
    -- end
    */

    /**
     * @param CreateMeetingParameters $createMeetingParams
     *
     * @return string
     */
    public function getCreateMeetingUrl($createMeetingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::CREATE, $createMeetingParams->getHTTPQuery());
    }

    /**
     * @param CreateMeetingParameters $createMeetingParams
     *
     * @throws \RuntimeException
     *
     * @return CreateMeetingResponse
     */
    public function createMeeting($createMeetingParams)
    {
        $xml = $this->processXmlResponse($this->getCreateMeetingUrl($createMeetingParams), $createMeetingParams->getPresentationsAsXML());

        return new CreateMeetingResponse($xml);
    }

    /**
     * @param $joinMeetingParams JoinMeetingParameters
     *
     * @return string
     */
    public function getJoinMeetingURL($joinMeetingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::JOIN, $joinMeetingParams->getHTTPQuery());
    }

    /**
     * @param $joinMeetingParams JoinMeetingParameters
     *
     * @throws \RuntimeException
     *
     * @return JoinMeetingResponse
     */
    public function joinMeeting($joinMeetingParams)
    {
        $xml = $this->processXmlResponse($this->getJoinMeetingURL($joinMeetingParams));

        return new JoinMeetingResponse($xml);
    }

    /**
     * @param $endParams EndMeetingParameters
     *
     * @return string
     */
    public function getEndMeetingURL($endParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::END, $endParams->getHTTPQuery());
    }

    /**
     * @param $endParams EndMeetingParameters
     *
     * @throws \RuntimeException
     *
     * @return EndMeetingResponse
     * */
    public function endMeeting($endParams)
    {
        $xml = $this->processXmlResponse($this->getEndMeetingURL($endParams));

        return new EndMeetingResponse($xml);
    }

    // __________________ BBB MONITORING METHODS _________________
    /* The methods in the following section support the following categories of the BBB API:
    -- isMeetingRunning
    -- getMeetings
    -- getMeetingInfo
    */

    /**
     * @param $meetingParams IsMeetingRunningParameters
     *
     * @return string
     */
    public function getIsMeetingRunningUrl($meetingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::IS_MEETING_RUNNING, $meetingParams->getHTTPQuery());
    }

    /**
     * @param $meetingParams
     *
     * @throws \RuntimeException
     *
     * @return IsMeetingRunningResponse
     */
    public function isMeetingRunning($meetingParams)
    {
        $xml = $this->processXmlResponse($this->getIsMeetingRunningUrl($meetingParams));

        return new IsMeetingRunningResponse($xml);
    }

    /**
     * @return string
     */
    public function getMeetingsUrl()
    {
        return $this->urlBuilder->buildUrl(ApiMethod::GET_MEETINGS);
    }

    /**
     * @throws \RuntimeException
     *
     * @return GetMeetingsResponse
     */
    public function getMeetings()
    {
        $xml = $this->processXmlResponse($this->getMeetingsUrl());

        return new GetMeetingsResponse($xml);
    }

    /**
     * @param $meetingParams GetMeetingInfoParameters
     *
     * @return string
     */
    public function getMeetingInfoUrl($meetingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::GET_MEETING_INFO, $meetingParams->getHTTPQuery());
    }

    /**
     * @param $meetingParams GetMeetingInfoParameters
     *
     * @throws \RuntimeException
     *
     * @return GetMeetingInfoResponse
     */
    public function getMeetingInfo($meetingParams)
    {
        $xml = $this->processXmlResponse($this->getMeetingInfoUrl($meetingParams));

        return new GetMeetingInfoResponse($xml);
    }

    // __________________ BBB RECORDING METHODS _________________
    /* The methods in the following section support the following categories of the BBB API:
    -- getRecordings
    -- publishRecordings
    -- deleteRecordings
    */

    /**
     * @param $recordingsParams GetRecordingsParameters
     *
     * @return string
     */
    public function getRecordingsUrl($recordingsParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::GET_RECORDINGS, $recordingsParams->getHTTPQuery());
    }

    /**
     * @param $recordingParams
     *
     * @throws \RuntimeException
     *
     * @return GetRecordingsResponse
     */
    public function getRecordings($recordingParams)
    {
        $xml = $this->processXmlResponse($this->getRecordingsUrl($recordingParams));

        return new GetRecordingsResponse($xml);
    }

    /**
     * @param $recordingParams PublishRecordingsParameters
     *
     * @return string
     */
    public function getPublishRecordingsUrl($recordingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::PUBLISH_RECORDINGS, $recordingParams->getHTTPQuery());
    }

    /**
     * @param $recordingParams PublishRecordingsParameters
     *
     * @throws \RuntimeException
     *
     * @return PublishRecordingsResponse
     */
    public function publishRecordings($recordingParams)
    {
        $xml = $this->processXmlResponse($this->getPublishRecordingsUrl($recordingParams));

        return new PublishRecordingsResponse($xml);
    }

    /**
     * @param $recordingParams DeleteRecordingsParameters
     *
     * @return string
     */
    public function getDeleteRecordingsUrl($recordingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::DELETE_RECORDINGS, $recordingParams->getHTTPQuery());
    }

    /**
     * @param $recordingParams DeleteRecordingsParameters
     *
     * @throws \RuntimeException
     *
     * @return DeleteRecordingsResponse
     */
    public function deleteRecordings($recordingParams)
    {
        $xml = $this->processXmlResponse($this->getDeleteRecordingsUrl($recordingParams));

        return new DeleteRecordingsResponse($xml);
    }

    /**
     * @param $recordingParams UpdateRecordingsParameters
     *
     * @return string
     */
    public function getUpdateRecordingsUrl($recordingParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::UPDATE_RECORDINGS, $recordingParams->getHTTPQuery());
    }

    /**
     * @param $recordingParams UpdateRecordingsParameters
     *
     * @throws \RuntimeException
     *
     * @return UpdateRecordingsResponse
     */
    public function updateRecordings($recordingParams)
    {
        $xml = $this->processXmlResponse($this->getUpdateRecordingsUrl($recordingParams));

        return new UpdateRecordingsResponse($xml);
    }

    // ____________________ WEB HOOKS METHODS ___________________

    /**
     * @param $hookCreateParams HooksCreateParameters
     *
     * @return string
     */
    public function getHooksCreateUrl($hookCreateParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::HOOKS_CREATE, $hookCreateParams->getHTTPQuery());
    }

    /**
     * @param $hookCreateParams
     *
     * @return HooksCreateResponse
     */
    public function hooksCreate($hookCreateParams)
    {
        $xml = $this->processXmlResponse($this->getHooksCreateUrl($hookCreateParams));

        return new HooksCreateResponse($xml);
    }

    /**
     * @return string
     */
    public function getHooksListUrl()
    {
        return $this->urlBuilder->buildUrl(ApiMethod::HOOKS_LIST);
    }

    /**
     * @return HooksListResponse
     */
    public function hooksList()
    {
        $xml = $this->processXmlResponse($this->getHooksListUrl());

        return new HooksListResponse($xml);
    }

    /**
     * @param $hooksDestroyParams HooksDestroyParameters
     *
     * @return string
     */
    public function getHooksDestroyUrl($hooksDestroyParams)
    {
        return $this->urlBuilder->buildUrl(ApiMethod::HOOKS_DESTROY, $hooksDestroyParams->getHTTPQuery());
    }

    /**
     * @param $hooksDestroyParams
     *
     * @return HooksDestroyResponse
     */
    public function hooksDestroy($hooksDestroyParams)
    {
        $xml = $this->processXmlResponse($this->getHooksDestroyUrl($hooksDestroyParams));

        return new HooksDestroyResponse($xml);
    }

    // ____________________ SPECIAL METHODS ___________________
    /**
     * @return string
     */
    public function getJSessionId()
    {
        return $this->jSessionId;
    }

    /**
     * @param string $jSessionId
     */
    public function setJSessionId($jSessionId)
    {
        $this->jSessionId = $jSessionId;
    }

    /**
     * @param array $curlopts
     */
    public function setCurlOpts($curlopts)
    {
        $this->curlopts = $curlopts;
    }

    /**
     * Set Curl Timeout (Optional), Default 10 Seconds.
     *
     * @param int $TimeOutInSeconds
     *
     * @return static
     */
    public function setTimeOut($TimeOutInSeconds)
    {
        $this->timeOut = $TimeOutInSeconds;

        return $this;
    }

    /**
     * Public accessor for buildUrl.
     *
     * @param string $method
     * @param string $params
     * @param bool   $append
     *
     * @return string
     */
    public function buildUrl($method = '', $params = '', $append = true)
    {
        return $this->urlBuilder->buildUrl($method, $params, $append);
    }

    // ____________________ INTERNAL CLASS METHODS ___________________

    /**
     * A private utility method used by other public methods to process XML responses.
     *
     * @param string $url
     * @param string $payload
     * @param string $contentType
     *
     * @throws \RuntimeException
     *
     * @return SimpleXMLElement
     */
    private function processXmlResponse($url, $payload = '', $contentType = 'application/xml')
    {
        if (extension_loaded('curl')) {
            $ch = curl_init();
            if (!$ch) {
                throw new \RuntimeException('Unhandled curl error: ' . curl_error($ch));
            }
            $timeout = 10;

            // Needed to store the JSESSIONID
            $cookiefile     = tmpfile();
            $cookiefilepath = stream_get_meta_data($cookiefile)['uri'];

            foreach ($this->curlopts as $opt => $value) {
                curl_setopt($ch, $opt, $value);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefilepath);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefilepath);
            if (!empty($payload)) {
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-type: ' . $contentType,
                    'Content-length: ' . mb_strlen($payload),
                ]);
            }
            $data = curl_exec($ch);
            if (false === $data) {
                throw new \RuntimeException('Unhandled curl error: ' . curl_error($ch));
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpcode < 200 || $httpcode >= 300) {
                throw new BadResponseException('Bad response, HTTP code: ' . $httpcode);
            }
            curl_close($ch);
            unset($ch);

            $cookies = file_get_contents($cookiefilepath);
            if (false !== mb_strpos($cookies, 'JSESSIONID')) {
                preg_match('/(?:JSESSIONID\s*)(?<JSESSIONID>.*)/', $cookies, $output_array);
                $this->setJSessionId($output_array['JSESSIONID']);
            }

            return new SimpleXMLElement($data);
        }

        throw new \RuntimeException('Post XML data set but curl PHP module is not installed or not enabled.');
    }
}
