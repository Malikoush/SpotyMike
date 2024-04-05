<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
                'name' => $user->getFirstname(),
                'encrypt' => $user->getPassword(),
                'mail' => $user->getEmail(),
                'tel' => $user->getTel(),
                'birthday' => $user->getDateBirth()
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

        return $this->json($user->serializer());
    }

    #[Route('/user', name: 'app_user_post', methods: 'POST')]
    public function postUser(Request $request, UserPasswordHasherInterface $passwordHash): JsonResponse
    {
        parse_str($request->getContent(), $data);
        //vérification attribut nécessaire
        if (!isset($data['firstname']) || !isset($data['email']) || !isset($data['encrypte']) || !isset($data['sexe']) || !isset($data['birthday'])) {
            return new JsonResponse([
                'error' => 'Missing data',
                'data' => $data
            ], JsonResponse::HTTP_BAD_REQUEST);
        }



        $email = $data['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'error' => 'Invalid email address',
                'email' => $email
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
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $dateOfBirth = \DateTimeImmutable::createFromFormat('Y-m-d', $data['birthday']);
        $user->setDateBirth($dateOfBirth);
        $user->setSexe($data['sexe']);
        $user->setEmail($data['email']);


        $hash = $passwordHash->hashPassword($user, $data['encrypte']);

        $user->setPassword($hash);
        if (isset($data['tel'])) {
            if (preg_match('/^0[1-9]([-. ]?[0-9]{2}){4}$/', $data['tel'])) {
                $user->setTel($data['tel']);
            } else {
                return new JsonResponse([
                    'error' => 'Invalid phone number',
                    'phone' => $data['tel']
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
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

        $email = $data['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'error' => 'Invalid email address',
                'email' => $email
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $existingUser = $this->repository->findOneByEmail($data['email']);
        if ($existingUser !== null) {
            return new JsonResponse([
                'error' => 'Email already exists',
                'email' => $data['email']
            ], JsonResponse::HTTP_CONFLICT);
        }

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
            $user->setPassword($data['encrypte']);
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
