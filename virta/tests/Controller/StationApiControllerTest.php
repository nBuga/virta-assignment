<?php

namespace App\Test\Controller;

use App\Controller\Api\StationApiController;
use App\Repository\StationRepository;
use App\Service\CustomSerializer;
use App\Service\StationAPIService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StationApiControllerTest extends WebTestCase
{
    protected StationApiController $controller;

    protected function setUp(): void
    {
        $stationRepository = $this->createMock(StationRepository::class);
        $stationRepository->method('findAll')->willReturn([
            ['id' => 1, 'name' => 'Station 1'],
            ['id' => 2, 'name' => 'Station 2'],
        ]);

        $serializer = $this->createMock(CustomSerializer::class);
        $serializer->method('serialize')->willReturn(json_encode([
            'total' => 2,
            'data' => [
                ['id' => 1, 'name' => 'Station 1'],
                ['id' => 2, 'name' => 'Station 2'],
            ],
        ]));

        $stationAPIService = $this->createMock(StationAPIService::class);

        $this->controller = new StationApiController($serializer, $stationRepository, $stationAPIService);
    }

    public function testStationsMethodReturnsJsonResponse()
    {
        $response = $this->controller->stations();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testStationsMethodReturnsExpectedData()
    {
        $response = $this->controller->stations()->getContent();
        $expected = json_encode([
            'total' => 2,
            'data' => [
                ['id' => 1, 'name' => 'Station 1'],
                ['id' => 2, 'name' => 'Station 2'],
            ],
        ]);

        $this->assertEquals($expected, $response);
    }

    public function testShowStationNotFound(): void
    {
        $id = 'non_existing_id';
        $stationRepository = $this->createMock(StationRepository::class);
        $stationRepository->expects($this->once())
            ->method('find')
            ->with($this->equalTo($id))
            ->willReturn(null);

        $stationAPIService = $this->createMock(StationAPIService::class);
        $serializer = $this->createMock(CustomSerializer::class);
        $controller = new StationApiController($serializer, $stationRepository, $stationAPIService);

        $expectedResponse = new JsonResponse(['error' => ['message' => sprintf("Station with id %s not found!", $id)]], Response::HTTP_NOT_FOUND);
        $actualResponse = $controller->show($id);

        $this->assertEquals($expectedResponse, $actualResponse);
    }
}
