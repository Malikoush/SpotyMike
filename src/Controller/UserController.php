<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    #[Route('/user', name: 'app_users_get_all', methods: 'GET')]
    public function getUsers(): JsonResponse
    {
        $users = $this->repository->findAll();

        if (!$users) {
            return $this->json([
                'message' => 'No users found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $serializedUsers = [];
        foreach ($users as $user) {
            $serializedUsers[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'mail' => $user->getEmail(),
                'tel' => $user->getTel()
            ];
        }

        return new JsonResponse($serializedUsers);
    }

    #[Route('/user/{id}', name: 'app_user_get', methods: 'GET')]
    public function getUserById(int $id)
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'User not found',
                'userid' => $id,
            ]);
        }

        return $this->json([
            'id' => $user->getId(),
            'name' => $user->getName(),
            'mail' => $user->getEmail(),
            'tel' => $user->getTel()
        ]);
    }

    #[Route('/user', name: 'app_user_post', methods: 'POST')]
    public function postUser(Request $request): JsonResponse
    {
        parse_str($request->getContent(), $data);

        if (!isset($data['name']) || !isset($data['email']) || !isset($data['encrypte'])) {
            return new JsonResponse([
                'error' => 'Missing data',
                'data' => $data
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $this->repository->findOneByEmail($data['email']);
        if ($existingUser !== null) {
            return new JsonResponse([
                'error' => 'Email already exists',
                'email' => $data['email']
            ], JsonResponse::HTTP_CONFLICT);
        }


        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $user = new User();
        $user->setIdUser($data['id_user']);
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setEncrypte($data['encrypte']);
        if (isset($data['tel'])) {
            $user->setTel($data['tel']);
        }
        $user->setCreateAt($date);
        $user->setUpdateAt($date);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'validate' => 'User added successfully',
            'id' => $user->getId()
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_put', methods: 'PUT')]
    public function putUser(Request $request, int $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'User not found',
                'userid' => $id,
            ]);
        }
        parse_str($request->getContent(), $data);

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['tel'])) {
            $user->setTel($data['tel']);
        }
        if (isset($data['encrypte'])) {
            $user->setEncrypte($data['encrypte']);
        }
        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $user->setUpdateAt($date);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User updated successfully']);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: 'DELETE')]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->repository->find($id);

        if (!$user) {
            return $this->json([
                'error' => 'User not found',
                'userid' => $id,
            ]);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'User deleted successfully']);
    }
}
