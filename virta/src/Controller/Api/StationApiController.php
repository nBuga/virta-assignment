<?php

namespace App\Controller\Api;

use App\Entity\Station;
use App\Exception\CompanyNotFoundException;
use App\Model\StationDTO;
use App\Repository\StationRepository;
use App\Service\CustomSerializer;
use App\Service\StationAPIService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/v1", "api_")]
class StationApiController extends AbstractController
{
    public function __construct(
        private readonly CustomSerializer $serializer,
        private readonly StationRepository $stationRepository,
        private readonly StationAPIService $stationAPIService,
    ) {
    }

    /**
     * List the stations.
     *
     * This call lists all the stations.
     */
    #[Route('/stations', name: 'stations', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns the stations.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function stations(): JsonResponse
    {
        $stations = $this->stationRepository->findAll();
        $response = [
            'total' => count($stations),
            'data' => $stations,
        ];

        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, json: true);
    }

    /**
     * Show a station.
     *
     * This call show a specific station.
     */
    #[Route('/stations/{id}', name: 'station_show', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns a specific station.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function show(string $id): JsonResponse
    {
        $station = $this->stationRepository->find(trim($id));
        if (!$station instanceof Station) {
            return $this->getResponseStationNotFound($id);
        }

        $response = ['data' => $station];
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: Response::HTTP_OK, json: true);
    }

    /**
     * Update a station.
     *
     * This call updates a station.
     */
    #[Route('/stations/{id}', name: 'station_update', methods: ["PUT", "PATCH"], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Updates a station.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function update(
        #[MapRequestPayload(acceptFormat: 'json',)] StationDTO $stationDTto,
        string $id
    ): JsonResponse {
        $station = $this->stationRepository->find(trim($id));
        if (!$station instanceof Station) {
            return $this->getResponseStationNotFound($id);
        }

        try {
            $station = $this->stationAPIService->upsertStation($stationDTto, $station);
            $response = ['data' => $station];
            $responseStatusCode = Response::HTTP_OK;
        } catch (CompanyNotFoundException $exception) {
            $response = ['error' => ['message' => $exception->getMessage()]];
            $responseStatusCode = $exception->getStatusCode();
        }
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: $responseStatusCode, json: true);
    }

    /**
     * Create a station.
     *
     * This call creates a station.
     */
    #[Route('/stations', name: 'station_create', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: 201,
        description: 'Create a station.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function create(#[MapRequestPayload(acceptFormat: 'json',)] StationDTO $stationDTto): JsonResponse
    {
        try {
            $newStation = new Station();
            $station = $this->stationAPIService->upsertStation($stationDTto, $newStation);
            $response = ['data' => $station];
            $responseStatusCode = Response::HTTP_CREATED;
        } catch (CompanyNotFoundException $exception) {
            $response = ['error' => ['message' => $exception->getMessage()]];
            $responseStatusCode = $exception->getStatusCode();
        }
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: $responseStatusCode, json: true);
    }

    /**
     * Delete a station.
     *
     * This call deletes a station.
     */
    #[Route('/stations/{id}', name: 'station_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Delete a station.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function delete(string $id): JsonResponse
    {
        $station = $this->stationRepository->find(trim($id));
        if (!$station instanceof Station) {
            return $this->getResponseStationNotFound($id);
        }

        $this->stationAPIService->deleteStation($station);

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    private function getResponseStationNotFound(string $id): JsonResponse
    {
        $response = ['error' => ['message' => sprintf("Station with id %s not found!", $id)]];

        return new JsonResponse(data: $response, status: Response::HTTP_NOT_FOUND);
    }

    /**
     * Lists all charging stations within the radius of n kilometers from a point (latitude, longitude)
     *
     * This call lists all charging stations within the radius of n kilometers from a point (latitude, longitude)
     */
    #[Route('/stations/{latitude}/{longitude}/{radius}', name: 'charging_stations', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Lists all charging stations within the radius of n kilometers from a point (latitude, longitude)',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function chargingStations(
        float $latitude,
        float $longitude,
        float $radius,
        StationRepository $stationRepository
    ): JsonResponse {
        $stations = $stationRepository->findWithinRadius($latitude, $longitude, $radius);
        $data = [
            'total' => count($stations),
            'data' => $stations
        ];
        $data = $this->serializer->serialize($data);

        return new JsonResponse(data: $data, status: Response::HTTP_OK, json: true);
    }
}
