<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StationController extends AbstractController
{
    #[Route('/charging-stations', name: 'app_charging_stations')]
    public function index(): Response
    {
        return $this->render('station/charging-stations.html.twig', [
            'title' => 'Charging stations',
        ]);
    }
}
