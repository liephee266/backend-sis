<?php

namespace App\Controller;

use App\Services\Toolkit;
use App\Entity\Autorisation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/v1/main/cron')]
final class CronController extends AbstractController
{

    private $toolkit;
    private $entityManager;
    private $serializer;
    private $security;

    public function __construct(Toolkit $toolKit, EntityManagerInterface $entityManager,  SerializerInterface $serializer, Security $security)
    {
        $this->toolkit = $toolKit;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->security = $security;
    }
    
    #[Route('/clean-authorization', name: 'app_cron_clean_authorization')]
    public function cleanAuthorization(): JsonResponse
    {
        $tabmapp = [
            'dossier_medicale' => 'DossierMedicale',
        ];
        $all_authorizations = $this->entityManager->getRepository(Autorisation::class)->findBy([
            'type_demande' => 'AUTORISATION',
            'entity' => 'dossier_medicale',
            'status' => 2,
        ]);
        foreach($all_authorizations as $authorization){
            if ($this->toolkit->joursDepuisDate( $authorization->getUpdatedAt()) >= $authorization->getDateLimit()) {
                $data_access = $this->entityManager->getRepository('App\Entity\\'.$tabmapp[$authorization->getEntity()])->find($authorization->getEntityId());
                $user_access = $data_access->getAccess();
                $index = array_search($data_access->getDemanderId()->getId(), $user_access);
                if ($index !== false) {
                    unset($user_access[$index]);
                }
                $data_access->setAccess($user_access);
                $this->entityManager->persist($data_access);
                $this->entityManager->flush();
            }
        }
        return new JsonResponse([]);
    }
}
