<?php

declare(strict_types=1);

namespace Zone\Wildduck\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Tests\Unit\ApiRequestor\RecordingClient;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\Dto\Message\FlagCounterResponseDto;
use Zone\Wildduck\Dto\Message\LabelCounterResponseDto;
use Zone\Wildduck\Dto\Message\LabelResponseDto;
use Zone\Wildduck\Dto\Message\UserLabelsResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\HttpClient\CurlClient;
use Zone\Wildduck\Service\LabelService;
use Zone\Wildduck\WildduckClient;

/**
 * Pins the URL, method and params shape of every label request.
 */
class LabelServiceTest extends TestCase
{
    private const string USER = '64a6663e5a17b40001e60c0a';

    private RecordingClient $http;
    private LabelService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->http = new RecordingClient();
        ApiRequestor::setHttpClient($this->http);
        $client = new WildduckClient([
            'api_base' => 'https://localhost:8080',
            'access_token' => 'test-token',
        ]);
        $this->service = $client->labels();
    }

    #[\Override]
    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(CurlClient::instance());
        parent::tearDown();
    }

    public function testClientExposesLabelService(): void
    {
        $this->assertInstanceOf(LabelService::class, $this->service);
    }

    public function testListSendsGetWithCountersAndSearchable(): void
    {
        $this->http->responseBody = '{"success":true,"labels":['
            . '{"id":"a1","name":"Work","total":5,"unseen":2},'
            . '{"id":"b2","name":"Family"}]}';

        $result = $this->service->list(self::USER, counters: true, searchable: true);

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels', $this->http->lastUrl);
        $this->assertSame(['counters' => true, 'searchable' => true], $this->http->lastParams);
        $this->assertInstanceOf(UserLabelsResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertSame('Work', $result->labels[0]->name);
        $this->assertSame(5, $result->labels[0]->total);
        $this->assertSame(2, $result->labels[0]->unseen);
        $this->assertNull($result->labels[1]->total);
    }

    public function testListDefaultsToNoCountersAndSearchableScope(): void
    {
        $this->http->responseBody = '{"success":true,"labels":[]}';

        $this->service->list(self::USER);

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels', $this->http->lastUrl);
        $this->assertSame(['counters' => false, 'searchable' => true], $this->http->lastParams);
    }

    public function testGetCounterUrlEncodesLabelName(): void
    {
        $this->http->responseBody = '{"success":true,"label":"Home > Kids \"All\"","total":42,"unseen":7}';

        $result = $this->service->getCounter(self::USER, 'Home > Kids "All"', searchable: true);

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame(
            'https://localhost:8080/users/' . self::USER . '/label-counters/Home+%3E+Kids+%22All%22',
            $this->http->lastUrl
        );
        $this->assertSame(['searchable' => true], $this->http->lastParams);
        $this->assertInstanceOf(LabelCounterResponseDto::class, $result);
        $this->assertSame('Home > Kids "All"', $result->label);
        $this->assertSame(42, $result->total);
        $this->assertSame(7, $result->unseen);
    }

    public function testGetCounterUrlEncodesSlashesInLabelName(): void
    {
        $this->http->responseBody = '{"success":true,"label":"Work/Web","total":1,"unseen":0}';

        $this->service->getCounter(self::USER, 'Work/Web', searchable: false);

        $this->assertSame(
            'https://localhost:8080/users/' . self::USER . '/label-counters/Work%2FWeb',
            $this->http->lastUrl
        );
        $this->assertSame(['searchable' => false], $this->http->lastParams);
    }

    public function testGetFlagCounterSendsGetForFlagged(): void
    {
        $this->http->responseBody = '{"success":true,"flag":"flagged","total":12,"unseen":4}';

        $result = $this->service->getFlagCounter(self::USER, 'flagged');

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame(
            'https://localhost:8080/users/' . self::USER . '/flag-counters/flagged',
            $this->http->lastUrl
        );
        $this->assertSame(['searchable' => true], $this->http->lastParams);
        $this->assertInstanceOf(FlagCounterResponseDto::class, $result);
        $this->assertSame('flagged', $result->flag);
        $this->assertSame(12, $result->total);
        $this->assertSame(4, $result->unseen);
    }

    public function testGetFlagCounterSendsGetForUnread(): void
    {
        $this->http->responseBody = '{"success":true,"flag":"unread","total":3,"unseen":3}';

        $result = $this->service->getFlagCounter(self::USER, 'unread');

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame(
            'https://localhost:8080/users/' . self::USER . '/flag-counters/unread',
            $this->http->lastUrl
        );
        $this->assertSame(['searchable' => true], $this->http->lastParams);
        $this->assertSame('unread', $result->flag);
    }

    public function testCreateSendsPostWithNameBody(): void
    {
        $this->http->responseBody = '{"success":true,"id":"a1","name":"Work"}';

        $result = $this->service->create(self::USER, 'Work');

        $this->assertSame('post', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels', $this->http->lastUrl);
        $this->assertSame(['name' => 'Work'], $this->http->lastParams);
        $this->assertInstanceOf(LabelResponseDto::class, $result);
        $this->assertSame('a1', $result->id);
        $this->assertSame('Work', $result->name);
    }

    public function testGetSendsGetToLabelPath(): void
    {
        $this->http->responseBody = '{"success":true,"id":"a1","name":"Work","metaData":{"color":"red"}}';

        $result = $this->service->get(self::USER, 'a1');

        $this->assertSame('get', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels/a1', $this->http->lastUrl);
        $this->assertSame([], $this->http->lastParams);
        $this->assertInstanceOf(LabelResponseDto::class, $result);
        $this->assertSame(['color' => 'red'], $result->metaData);
    }

    public function testRenameSendsPutWithNewNameBody(): void
    {
        $this->http->responseBody = '{"success":true,"id":"a1","name":"Family"}';

        $result = $this->service->rename(self::USER, 'a1', 'Family');

        $this->assertSame('put', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels/a1', $this->http->lastUrl);
        $this->assertSame(['name' => 'Family'], $this->http->lastParams);
        $this->assertInstanceOf(LabelResponseDto::class, $result);
        $this->assertSame('Family', $result->name);
    }

    public function testDeleteSendsDeleteWithoutBody(): void
    {
        $this->http->responseBody = '{"success":true,"scheduled":"task1","existing":false}';

        $result = $this->service->delete(self::USER, 'a1');

        $this->assertSame('delete', $this->http->lastMethod);
        $this->assertSame('https://localhost:8080/users/' . self::USER . '/labels/a1', $this->http->lastUrl);
        $this->assertSame([], $this->http->lastParams);
        $this->assertInstanceOf(SuccessResponseDto::class, $result);
        $this->assertTrue($result->success);
    }
}
