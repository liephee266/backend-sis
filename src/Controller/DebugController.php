<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Json;

final class DebugController extends AbstractController
{
    #[Route('/', name: 'app_debug')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Hello, this is a debug endpoint!',
            'status' => 'success'
        ], Response::HTTP_OK);
    }
}
