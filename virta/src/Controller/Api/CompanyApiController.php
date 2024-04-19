<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Company;
use App\Entity\Station;
use App\Exception\CompanyNotFoundException;
use App\Model\CompanyDTO;
use App\Repository\CompanyRepository;
use App\Service\CompanyAPIService;
use App\Service\CustomSerializer;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/v1", "api_")]
class CompanyApiController extends AbstractController
{
    public function __construct(
        private readonly CustomSerializer $serializer,
        private readonly CompanyRepository $companyRepository,
        private readonly CompanyAPIService $companyAPIService,
    ) {
    }

    /**
     * List the companies.
     *
     * This call lists all the companies.
     */
    #[Route('/companies', name: 'companies', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns the companies.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function companies(): JsonResponse
    {
        $companies = $this->companyRepository->findAll();
        $response = [
            'total' => count($companies),
            'data' => $companies,
        ];

        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, json: true);
    }

    /**
     * Show a company.
     *
     * This call show a specific company.
     */
    #[Route('/companies/{id}', name: 'company_show', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns a specific company.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function show(string $id): JsonResponse
    {
        $company = $this->companyRepository->find(trim($id));
        if (!$company instanceof Company) {
            return $this->getResponseCompanyNotFound($id);
        }

        $response = ['data' => $company];
        $responseStatusCode = Response::HTTP_OK;
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: $responseStatusCode, json: true);
    }

    /**
     * Update a company.
     *
     * This call updates a company.
     */
    #[Route('/companies/{id}', name: 'company_update', methods: ["PUT", "PATCH"], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Updates a company.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function update(#[MapRequestPayload(acceptFormat: 'json',)] CompanyDTO $companyDTto, string $id): JsonResponse
    {
        $company = $this->companyRepository->find(trim($id));
        if (!$company instanceof Company) {
            return $this->getResponseCompanyNotFound($id);
        }

        try {
            $company = $this->companyAPIService->upsertCompany($companyDTto, $company);
            $response = ['data' => $company];
            $responseStatusCode = Response::HTTP_OK;
        } catch (CompanyNotFoundException $exception) {
            $response = ['error' => ['message' => $exception->getMessage()]];
            $responseStatusCode = $exception->getStatusCode();
        }
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: $responseStatusCode, json: true);
    }

    /**
     * Create a company.
     *
     * This call creates a company.
     */
    #[Route('/companies', name: 'company_create', methods: ['POST'], format: 'json')]
    #[OA\Response(
        response: 201,
        description: 'Create a company.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function create(#[MapRequestPayload(acceptFormat: 'json',)] CompanyDTO $companyDTto): JsonResponse
    {
        try {
            $newCompany = new Company();
            $company = $this->companyAPIService->upsertCompany($companyDTto, $newCompany);
            $response = ['data' => $company];
            $responseStatusCode = Response::HTTP_CREATED;
        } catch (CompanyNotFoundException $exception) {
            $response = ['error' => ['message' => $exception->getMessage()]];
            $responseStatusCode = $exception->getStatusCode();
        }
        $data = $this->serializer->serialize($response);

        return new JsonResponse(data: $data, status: $responseStatusCode, json: true);
    }

    /**
     * Delete a company.
     *
     * This call deletes a company.
     */
    #[Route('/companies/{id}', name: 'company_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Delete a company.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Station::class))
        )
    )]
    public function delete(string $id): JsonResponse
    {
        $company = $this->companyRepository->find(trim($id));
        if (!$company instanceof Company) {
            return $this->getResponseCompanyNotFound($id);
        }

        $this->companyAPIService->deleteCompany($company);

        return new JsonResponse(data: null, status: Response::HTTP_NO_CONTENT);
    }

    private function getResponseCompanyNotFound(string $id): JsonResponse
    {
        $response = ['error' => ['message' => sprintf("Company with id %s not found!", $id)]];

        return new JsonResponse(data: $response, status: Response::HTTP_NOT_FOUND);
    }


}
