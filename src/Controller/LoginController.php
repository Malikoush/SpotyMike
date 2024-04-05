<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Cache\CacheItemPoolInterface;



class LoginController extends  AbstractController
{
    private $repository;
    private $cache;
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager, CacheItemPoolInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->cache = $cache;
        $this->repository = $entityManager->getRepository(User::class);
    }

    #[Route('/login', name: 'app_login', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Welcome to MikeLand',
            'path' => 'src/Controller/LoginController.php',
        ]);
    }

    // use Symfony\Component\HttpFoundation\Request;
    #[Route('/login', name: 'app_login_post', methods: ['POST', 'PUT'])]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager, UserPasswordHasherInterface $passwordHash): JsonResponse
    {
        // Définir les paramètres de limite de fréquence
        $maxAttempts = 5;
        $interval = 300;

        //recup l'ip
        $ip = $request->getClientIp();
        // Récupérer le nombre de tentatives de connexion pour cette adresse IP dans le cache
        $attempts = $this->cache->getItem('login_attempts_' . $ip)->get() ?: 0;
        $timezone = new \DateTimeZone('Europe/Paris');
        $time = new DateTime('now', $timezone);
        // Vérifier si le nombre de tentatives a dépassé la limite
        if ($attempts >= $maxAttempts) {
            $expiration = $this->cache->getItem('expiration_' . $ip)->get();
            $temprestant = $expiration->modify('+5 minutes')->diff($time)->format('%i');

            return new JsonResponse([
                'error' => 'true.',
                'message' => 'Trop de tentatives de connexion(5 max).Veuiller réessayer ultérieurment ' . $temprestant . " minutes restante"
            ], 429);
        }
        $attempts++;
        $item = $this->cache->getItem('login_attempts_' . $ip);
        $expiration = $this->cache->getItem('expiration_' . $ip);

        $expiration->set($time);
        $this->cache->save($expiration);
        $item->set($attempts);
        $item->expiresAfter($interval);

        $this->cache->save($item);


        parse_str($request->getContent(), $data);
        //vérification attribut nécessaire
        if (!isset($data['username']) || !isset($data['mdp'])) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Email/Password manquants',
            ], 400);
        }
        $email = $data['username'];
        // vérif format mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'error' => true,
                'message' => "Le format de l'email est invalide."
            ], 400);
        }
        $password = $data['mdp'];
        // vérif format mdp
        if (
            strlen($password) >= 8 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[!@#$%^&*()-_+=]/', $password)
        ) {
            return new JsonResponse([
                'error' => true,
                'message' => "Le mot de passe doit contenir au moins une majuscule,une minuscule,un chiffre,un caractère spécial et avoir 8 caractères minimum."
            ], 400);
        }
        $user = $this->repository->findOneByEmail($email);
        // vérif Compte existant
        if (!$user) {

            return new JsonResponse([
                'error' => true,
                'message' => 'Aucun utilisateur trouvé. Mot de passe ou Identifiant incorecte',
            ], 404);
        }
        /*
        // vérif Compte actif
        if ($user->isActive == false) {

            return new JsonResponse([
                'error' => true,
                'message' => "Le compte n'est plus actif ou est suspendu.",
            ], 403);
        }*/
        if ($passwordHash->isPasswordValid($user, $password)) {
            $token = $JWTManager->create($user);
            return new JsonResponse([
                'error' => false,
                'message' => "L'utilisateur a était authentifié avec succès",
                'user' => $user->serializer(),
                'token' => $token,
            ]);
        }
        return new JsonResponse([
            'error' => true,
            'message' => 'Aucun utilisateur trouvé. Mot de passe ou Identifiant incorecte',
        ], 404);
    }
}
