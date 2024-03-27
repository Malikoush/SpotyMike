<?php

namespace App\Controller;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class ArtistController extends AbstractController
{

    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Artist::class);
    }


    #[Route('/artist/{id}', name: 'app_artist_delete', methods: ['DELETE'])]
    public function delete_artist_by_id(int $id): JsonResponse
    {
        
        $artist = $this->repository->find($id);

        if (!$artist) {
            return $this->json([
                'error' => 'Artist not found',
                'artistid' => $id,
            ]);
        }

        $this->entityManager->remove($artist);
        $this->entityManager->flush();

        return $this->json(['message' => 'Artist deleted successfully']);

        
    }

    #[Route('/artist', name: 'post_artist', methods: 'POST')]
    public function post_artist(Request $request): JsonResponse
    {
        parse_str($request->getContent(), $data);
        
        if (!isset($data['fullname']) || !isset($data['label']) || !isset($data['user_id_user_id'])  ){
            return new JsonResponse([
                'error' => 'Missing data',
                'data' => $data
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($data['user_id_user_id']);
    if (!$user) {
        return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_BAD_REQUEST);
    }

        $artist = new Artist();
        $artist->setFullname($data['fullname']);
        $artist->setLabel($data['label']);
        $artist->setUserIdUser($user);
        if (isset($data['description'])) {
            $artist->setdescription($data['description']);
        }

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return new JsonResponse([
            'validate' => 'Artist added successfully',
            'id' => $artist->getId()
        
        ]);
    }

    #[Route('/artist/{id}', name: 'app_artist_put', methods: ['PUT'])]
    public function putArtist(Request $request, int $id): JsonResponse
    {
        $artist = $this->repository->find($id);

        if (!$artist) {
            return new JsonResponse([
                'error' => 'Artist not found',
                'id' => $id
        ]);
        }

        
        parse_str($request->getContent(), $data);

       
        if (isset($data['fullname'])) {
            $artist->setFullname($data['fullname']);
        }
        if (isset($data['label'])) {
            $artist->setLabel($data['label']);
        }
        if (isset($data['description'])) {
            $artist->setDescription($data['description']);
        }
      

        
        $this->entityManager->persist($artist);
        $this->entityManager->flush();

    
        return new JsonResponse(['message' => 'Artist updated successfully']);
    }

    #[Route('/artist/{id}', name: 'app_artist', methods: ['GET'])]
    public function get_artist_by_id(int $id): JsonResponse
    {
        
        $artist = $this->repository->find($id);

        if (!$artist) {
            return $this->json([
                'error' => 'Artist not found',
                'artistid' => $id,
            ]);
        }

      
        return $this->json([
            'id' => $artist->getId(),
            'user' => [
                'id' => $artist->getUserIdUser()->getId(),
                'name' => $artist->getUserIdUser()->getName(),
                'mail' => $artist->getUserIdUser()->getEmail(),
                'tel' => $artist->getUserIdUser()->getTel(),
                
            ],
            'fullname' => $artist->getFullname(),
            'label' => $artist->getLabel(),
            'description' => $artist->getDescription(),
         
        ]);

        
    }
    #[Route('/artist', name: 'app_artists_get', methods: ['GET'])]
    public function get_all_artists(): JsonResponse
    {
        
        $artists = $this->repository->findAll();

        if (!$artists) {
            return $this->json([
                'message' => 'No artists found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $serializedArtists = [];
        foreach ($artists as $artist) {
            $serializedArtists[] = [
                'id' => $artist->getId(),
                'user' => [
                    'id' => $artist->getUserIdUser()->getId(),
                    'name' => $artist->getUserIdUser()->getName(),
                    'mail' => $artist->getUserIdUser()->getEmail(),
                    'tel' => $artist->getUserIdUser()->getTel(),
                    
                ],
                'fullname' => $artist->getFullname(),
                'label' => $artist->getLabel(),
                'description' => $artist->getDescription(),
            ];
        }

        return new JsonResponse($serializedArtists);

        
    }
}
