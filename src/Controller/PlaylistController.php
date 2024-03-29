<?php

namespace App\Controller;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class PlaylistController extends AbstractController
{

    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Playlist::class);
    }


    #[Route('/playlist/{id}', name: 'app_playlist_delete', methods: ['DELETE'])]
    public function delete_playlist_by_id(int $id): JsonResponse
    {

        $playlist = $this->repository->find($id);

        if (!$playlist) {
            return $this->json([
                'error' => 'Playlist not found',
                'playlistid' => $id,
            ]);
        }

        $this->entityManager->remove($playlist);
        $this->entityManager->flush();

        return $this->json(['message' => 'Playlist deleted successfully']);
    }

    #[Route('/playlist', name: 'post_playlist', methods: 'POST')]
    public function post_playlist(Request $request): JsonResponse
    {
        parse_str($request->getContent(), $data);

        if (!isset($data['title']) || !isset($data['public']) || !isset($data['idplaylist'])) {
            return new JsonResponse([
                'error' => 'Missing data',
                'data' => $data
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));


        $playlist = new Playlist();
        $playlist->setTitle($data['title']);
        $playlist->setIdPlaylist($data['idplaylist']);
        $playlist->setpublic($data['public']);
        $playlist->setCreateAt($date);

        $playlist->setUpdateAt($date);

        $this->entityManager->persist($playlist);
        $this->entityManager->flush();

        return new JsonResponse([
            'validate' => 'Playlist added successfully',
            'id' => $playlist->getId()

        ]);
    }

    #[Route('/playlist/{id}', name: 'app_playlist_put', methods: ['PUT'])]
    public function putPlaylist(Request $request, int $id): JsonResponse
    {
        $playlist = $this->repository->find($id);

        if (!$playlist) {
            return new JsonResponse([
                'error' => 'Playlist not found',
                'id' => $id
            ]);
        }


        parse_str($request->getContent(), $data);


        if (isset($data['title'])) {
            $playlist->setTitle($data['title']);
        }
        if (isset($data['public'])) {
            $playlist->setPublic($data['public']);
        }
        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $playlist->setUpdateAt($date);



        $this->entityManager->persist($playlist);
        $this->entityManager->flush();


        return new JsonResponse(['message' => 'Playlist updated successfully']);
    }

    #[Route('/playlist/{id}', name: 'app_playlist', methods: ['GET'])]
    public function get_playlist_by_id(int $id): JsonResponse
    {

        $playlist = $this->repository->find($id);

        if (!$playlist) {
            return $this->json([
                'error' => 'Playlist not found',
                'playlistid' => $id,
            ]);
        }


        return $this->json([
            'id' => $playlist->getId(),

            'title' => $playlist->getTitle(),
            'public' => $playlist->isPublic(),
            'createat' => $playlist->getCreateAt(),
            'updateat' => $playlist->getUpdateAt(),

        ]);
    }
    #[Route('/playlist', name: 'app_playlists_get', methods: ['GET'])]
    public function get_all_playlists(): JsonResponse
    {

        $playlists = $this->repository->findAll();

        if (!$playlists) {
            return $this->json([
                'message' => 'No playlists found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $serializedPlaylists = [];
        foreach ($playlists as $playlist) {
            $serializedPlaylists[] = [
                'id' => $playlist->getId(),

                'title' => $playlist->getTitle(),
                'public' => $playlist->isPublic(),
                'createat' => $playlist->getCreateAt()->format('Y-m-d H:i:s'),
                'updateat' => $playlist->getUpdateAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($serializedPlaylists);
    }
}
