<?php

namespace App\Controller;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends AbstractController
{

    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Album::class);
    }


    #[Route('/album/{id}', name: 'app_album_delete', methods: ['DELETE'])]
    public function delete_album_by_id(int $id): JsonResponse
    {
        
        $album = $this->repository->find($id);

        if (!$album) {
            return $this->json([
                'error' => 'Album not found',
                'albumid' => $id,
            ]);
        }

        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->json(['message' => 'Album deleted successfully']);

        
    }

    #[Route('/album', name: 'post_album', methods: 'POST')]
    public function post_album(Request $request): JsonResponse
    {
        parse_str($request->getContent(), $data);
        
        if (!isset($data['nom']) || !isset($data['categ']) || !isset($data['cover']) || !isset($data['year']) || !isset($data['idalbum'])) {
            return new JsonResponse(['error' => 'Missing data'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $album = new Album();
        $album->setNom($data['nom']);
        $album->setCateg($data['categ']);
        $album->setCover($data['cover']);
        $album->setYear($data['year']);
        $album->setIdAlbum($data['idalbum']);

        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return new JsonResponse([
            'validate' => 'Album added successfully',
            'id' => $album->getId()
        
        ]);
    }

    #[Route('/album/{id}', name: 'app_album_put', methods: ['PUT'])]
    public function putAlbum(Request $request, int $id): JsonResponse
    {
        $album = $this->repository->find($id);

        if (!$album) {
            return new JsonResponse([
                'error' => 'Album not found',
                'id' => $id
        ]);
        }

        
        parse_str($request->getContent(), $data);

       
        if (isset($data['nom'])) {
            $album->setNom($data['nom']);
        }
        if (isset($data['categ'])) {
            $album->setCateg($data['categ']);
        }
        if (isset($data['cover'])) {
            $album->setCover($data['cover']);
        }
        if (isset($data['year'])) {
            $album->setYear($data['year']);
        }
        if (isset($data['idalbum'])) {
            $album->setIdAlbum($data['year']);
        }

        
        $this->entityManager->persist($album);
        $this->entityManager->flush();

    
        return new JsonResponse(['message' => 'Album updated successfully']);
    }

    #[Route('/album/{id}', name: 'app_album', methods: ['GET'])]
    public function get_album_by_id(int $id): JsonResponse
    {
        
        $album = $this->repository->find($id);

        if (!$album) {
            return $this->json([
                'error' => 'Album not found',
                'albumid' => $id,
            ]);
        }

        

        return $this->json([
            'nom' => $album->getNom(),
            'categ' => $album->getCateg(),
            'cover' => $album->getCover(),
            'year' => $album->getYear(),
            'idalbum' => $album->getIdAlbum(),
        ]);

        
    }
    #[Route('/album', name: 'app_albums_get', methods: ['GET'])]
    public function get_all_albums(): JsonResponse
    {
        
        $albums = $this->repository->findAll();

        if (!$albums) {
            return $this->json([
                'message' => 'No albums found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $serializedAlbums = [];
        foreach ($albums as $album) {
            $serializedAlbums[] = [
                'nom' => $album->getNom(),
                'categ' => $album->getCateg(),
                'cover' => $album->getCover(),
                'year' => $album->getYear(),
                'idalbum' => $album->getIdAlbum(),
            ];
        }

        return new JsonResponse($serializedAlbums);

        
    }
}
