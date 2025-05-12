<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PusherController extends AbstractController
{
    #[Route('/pusher-test', name: 'pusher_test')]
public function testPusher(): Response
{
    return $this->render('test.html.twig', [
        'pusher_key' => $_ENV['PUSHER_APP_KEY'],
        'pusher_cluster' => $_ENV['PUSHER_APP_CLUSTER'],
    ]);
}

}

