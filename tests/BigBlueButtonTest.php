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
namespace Alireza5014;

use Alireza5014\Core\ApiMethod;
use Alireza5014\Parameters\DeleteRecordingsParameters;
use Alireza5014\Parameters\EndMeetingParameters;
use Alireza5014\Parameters\GetMeetingInfoParameters;
use Alireza5014\Parameters\GetRecordingsParameters;
use Alireza5014\Parameters\IsMeetingRunningParameters;
use Alireza5014\Parameters\PublishRecordingsParameters;

/**
 * Class BigBlueButtonTest
 * @package BigBlueButton
 */
class BigBlueButtonTest extends TestCase
{
    /**
     * @var BigBlueButton
     */
    private $bbb;

    /**
     * Setup test class
     */
    public function setUp()
    {
        parent::setUp();

        foreach (['BBB_SECRET', 'BBB_SERVER_BASE_URL'] as $k) {
            if (!getenv($k)) {
                $this->fail('$_SERVER[\'' . $k . '\'] not set in '
                    . 'phpunit.xml');
            }
        }

        $this->bbb = new BigBlueButton();
    }

    /* API Version */

    /**
     * Test API version call
     */
    public function testApiVersion()
    {
        $apiVersion = $this->bbb->getApiVersion();
        $this->assertEquals('SUCCESS', $apiVersion->getReturnCode());
        $this->assertEquals('2.0', $apiVersion->getVersion());
    }

    /* Create Meeting */

    /**
     * Test create meeting URL
     */
    public function testCreateMeetingUrl()
    {
        $params = $this->generateCreateParams();
        $url    = $this->bbb->getCreateMeetingUrl($this->getCreateMock($params));
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $this->assertContains('=' . urlencode($value), $url);
        }
    }

    /**
     * Test create meeting
     */
    public function testCreateMeeting()
    {
        $params = $this->generateCreateParams();
        $result = $this->bbb->createMeeting($this->getCreateMock($params));
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    /**
     * Test create meeting with a document URL
     */
    public function testCreateMeetingWithDocumentUrl()
    {
        $params = $this->getCreateMock($this->generateCreateParams());
        $params->addPresentation('https://placeholdit.imgix.net/~text?txtsize=96&bg=30406B&txtclr=ffffff&txt=BigBlueButton&w=800&h=600');

        $result = $this->bbb->createMeeting($params);

        $this->assertCount(1, $params->getPresentations());
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    /**
     * Test create meeting with a document URL and filename
     */
    public function testCreateMeetingWithDocumentUrlAndFileName()
    {
        $params = $this->getCreateMock($this->generateCreateParams());
        $params->addPresentation('https://placeholdit.imgix.net/~text?txtsize=100&bg=AB5080&txtclr=ffffff&txt=BigBlueButton&w=1920&h=1080', null, 'placeholder.png');

        $result = $this->bbb->createMeeting($params);

        $this->assertCount(1, $params->getPresentations());
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    /**
     * Test create meeting with a document URL
     */
    public function testCreateMeetingWithDocumentEmbedded()
    {
        $params = $this->getCreateMock($this->generateCreateParams());
        $params->addPresentation('bbb_logo.png', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'bbb_logo.png'));

        $result = $this->bbb->createMeeting($params);

        $this->assertCount(1, $params->getPresentations());
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    /**
     * Test create meeting with a multiple documents
     */
    public function testCreateMeetingWithMultiDocument()
    {
        $params = $this->getCreateMock($this->generateCreateParams());
        $params->addPresentation('https://placeholdit.imgix.net/~text?txtsize=96&bg=DE3040&txtclr=ffffff&txt=BigBlueButton&w=1600&h=1200', null, 'presentation.png');
        $params->addPresentation('logo.png', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'bbb_logo.png'));

        $result = $this->bbb->createMeeting($params);

        $this->assertCount(2, $params->getPresentations());
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    /* Join Meeting */

    /**
     * Test create join meeting URL
     */
    public function testCreateJoinMeetingUrl()
    {
        $joinMeetingParams = $this->generateJoinMeetingParams();
        $joinMeetingMock   = $this->getJoinMeetingMock($joinMeetingParams);

        $url = $this->bbb->getJoinMeetingURL($joinMeetingMock);

        foreach ($joinMeetingParams as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $this->assertContains('=' . urlencode($value), $url);
        }
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testJoinMeeting()
    {
        $joinMeetingParams = $this->generateJoinMeetingParams();
        $joinMeetingMock   = $this->getJoinMeetingMock($joinMeetingParams);
        $joinMeetingMock->setRedirect(false);

        $joinMeeting = $this->bbb->joinMeeting($joinMeetingMock);
        $this->assertEquals('SUCCESS', $joinMeeting->getReturnCode());
        $this->assertNotEmpty($joinMeeting->getAuthToken());
        $this->assertNotEmpty($joinMeeting->getUserId());
        $this->assertNotEmpty($joinMeeting->getSessionToken());
    }

    /* Get Default Config XML */

    public function testGetDefaultConfigXMLUrl()
    {
        $url = $this->bbb->getDefaultConfigXMLUrl();
        $this->assertContains(ApiMethod::GET_DEFAULT_CONFIG_XML, $url);
    }

    public function testGetDefaultConfigXML()
    {
        $result = $this->bbb->getDefaultConfigXML();
        $this->assertNotEmpty($result->getRawXml());
    }

    /* Set Config XML */

    public function testSetConfigXMLUrl()
    {
        $url = $this->bbb->setConfigXMLUrl();
        $this->assertContains(ApiMethod::SET_CONFIG_XML, $url);
    }

    public function testSetConfigXML()
    {
        // Fetch the Default Config XML file
        $defaultConfigXMLResponse = $this->bbb->getDefaultConfigXML();

        // Modify the XML file if required

        // Create a meeting
        $params                = $this->generateCreateParams();
        $createMeetingResponse = $this->bbb->createMeeting($this->getCreateMock($params));
        $this->assertEquals('SUCCESS', $createMeetingResponse->getReturnCode());

        // Execute setConfigXML request
        $params             = ['meetingId' => $createMeetingResponse->getMeetingId()];
        $setConfigXMLParams = $this->getSetConfigXMLMock($params);
        $setConfigXMLParams = $setConfigXMLParams->setRawXml($defaultConfigXMLResponse->getRawXml());
        $this->assertEquals($setConfigXMLParams->getRawXml(), $defaultConfigXMLResponse->getRawXml());

        $result = $this->bbb->setConfigXML($setConfigXMLParams);
        $this->assertEquals('SUCCESS', $result->getReturnCode());
        $this->assertNotEmpty($result->getToken());
    }

    /* End Meeting */

    /**
     * Test generate end meeting URL
     */
    public function testCreateEndMeetingUrl()
    {
        $params = $this->generateEndMeetingParams();
        $url    = $this->bbb->getEndMeetingURL($this->getEndMeetingMock($params));
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $this->assertContains('=' . urlencode($value), $url);
        }
    }

    public function testEndMeeting()
    {
        $meeting = $this->createRealMeeting($this->bbb);

        $endMeeting = new EndMeetingParameters($meeting->getMeetingId(), $meeting->getModeratorPassword());
        $result     = $this->bbb->endMeeting($endMeeting);
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    public function testEndNonExistingMeeting()
    {
        $params = $this->generateEndMeetingParams();
        $result = $this->bbb->endMeeting($this->getEndMeetingMock($params));
        $this->assertEquals('FAILED', $result->getReturnCode());
    }

    /* Is Meeting Running */

    public function testIsMeetingRunning()
    {
        $result = $this->bbb->isMeetingRunning(new IsMeetingRunningParameters($this->faker->uuid));
        $this->assertEquals('SUCCESS', $result->getReturnCode());
        $this->assertEquals(false, $result->isRunning());
    }

    /* Get Meetings */

    public function testGetMeetingsUrl()
    {
        $url = $this->bbb->getMeetingsUrl();
        $this->assertContains(ApiMethod::GET_MEETINGS, $url);
    }

    public function testGetMeetings()
    {
        $result = $this->bbb->getMeetings();
        $this->assertNotEmpty($result->getMeetings());
    }

    /* Get meeting info */

    public function testGetMeetingInfoUrl()
    {
        $meeting = $this->createRealMeeting($this->bbb);

        $url = $this->bbb->getMeetingInfoUrl(new GetMeetingInfoParameters($meeting->getMeetingId(), $meeting->getModeratorPassword()));
        $this->assertContains('=' . urlencode($meeting->getMeetingId()), $url);
        $this->assertContains('=' . urlencode($meeting->getModeratorPassword()), $url);
    }

    public function testGetMeetingInfo()
    {
        $meeting = $this->createRealMeeting($this->bbb);

        $result = $this->bbb->getMeetingInfo(new GetMeetingInfoParameters($meeting->getMeetingId(), $meeting->getModeratorPassword()));
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    public function testGetRecordingsUrl()
    {
        $url = $this->bbb->getRecordingsUrl(new GetRecordingsParameters());
        $this->assertContains(ApiMethod::GET_RECORDINGS, $url);
    }

    public function testGetRecordings()
    {
        $result = $this->bbb->getRecordings(new GetRecordingsParameters());
        $this->assertEquals('SUCCESS', $result->getReturnCode());
    }

    public function testPublishRecordingsUrl()
    {
        $url = $this->bbb->getPublishRecordingsUrl(new PublishRecordingsParameters($this->faker->sha1, true));
        $this->assertContains(ApiMethod::PUBLISH_RECORDINGS, $url);
    }

    public function testPublishRecordings()
    {
        $result = $this->bbb->publishRecordings(new PublishRecordingsParameters('non-existing-id-' . $this->faker->sha1, true));
        $this->assertEquals('FAILED', $result->getReturnCode());
    }

    public function testDeleteRecordingsUrl()
    {
        $url = $this->bbb->getDeleteRecordingsUrl(new DeleteRecordingsParameters($this->faker->sha1));
        $this->assertContains(ApiMethod::DELETE_RECORDINGS, $url);
    }

    public function testDeleteRecordings()
    {
        $result = $this->bbb->deleteRecordings(new DeleteRecordingsParameters('non-existing-id-' . $this->faker->sha1));
        $this->assertEquals('FAILED', $result->getReturnCode());
    }

    public function testUpdateRecordingsUrl()
    {
        $params = $this->generateUpdateRecordingsParams();
        $url    = $this->bbb->getUpdateRecordingsUrl($this->getUpdateRecordingsParamsMock($params));
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $this->assertContains('=' . urlencode($value), $url);
        }
    }

    public function testUpdateRecordings()
    {
        $params = $this->generateUpdateRecordingsParams();
        $result = $this->bbb->updateRecordings($this->getUpdateRecordingsParamsMock($params));
        $this->assertEquals('FAILED', $result->getReturnCode());
    }
}
