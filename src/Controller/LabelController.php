<?php

namespace App\Controller;

use App\Entity\Label;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LabelController extends AbstractController
{
    private $repository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Label::class);
    }

    #[Route('/labels', name: 'app_labels_get_all', methods: 'GET')]
    public function getLabels()
    {
        $labels = $this->repository->findAll();

        if (!$labels) {
            return $this->json([
                'message' => 'No labels found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $serializedLabels = [];
        foreach ($labels as $label) {
            $serializedLabels[] = [
                'id' => $label->getId(),
                'nom' => $label->getNom()
            ];
        }

        return new JsonResponse($serializedLabels);
    }

    #[Route('/label/{id}', name: 'app_label_get', methods: 'GET')]
    public function getLabel(int $id): JsonResponse
    {
        $label = $this->repository->find($id);

        if (!$label) {
            return $this->json([
                'error' => 'Label not found',
                'labelid' => $id,
            ]);
        }

        return $this->json([
            'id' => $label->getId(),
            'nom' => $label->getNom()
        ]);
    }

    #[Route('/label', name: 'app_label_post', methods: 'POST')]
    public function postLabel(Request $request): JsonResponse
    {
        parse_str($request->getContent(), $data);

        if (!isset($data['nom']) || !isset($data['create_at'])) {
            return new JsonResponse([
                'error' => 'Missing data',
                'data' => $data
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
        $label = new Label();
        $label->setNom($data['nom']);
        $label->setCreateAt($date);
        $label->setUpdateAt($date);

        $this->entityManager->persist($label);
        $this->entityManager->flush();

        return new JsonResponse([
            'validate' => 'Label added successfully',
            'id' => $label->getId()
        ]);
    }

    #[Route('/label/{id}', name: 'app_label_put', methods: 'PUT')]
    public function putLabel(Request $request, int $id): JsonResponse
    {
        $label = $this->repository->find($id);

        if (!$label) {
            return $this->json([
                'error' => 'Label not found',
                'labelid' => $id,
            ]);
        }
        parse_str($request->getContent(), $data);

        if (isset($data['nom'])) {
            $label->setNom($data['nom']);
        }

        $this->entityManager->persist($label);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Label updated successfully']);
    }

    #[Route('/label/{id}', name: 'app_label_delete', methods: 'DELETE')]
    public function deleteLabel(int $id): JsonResponse
    {
        $label = $this->repository->find($id);

        if (!$label) {
            return $this->json([
                'error' => 'Label not found',
                'labelid' => $id,
            ]);
        }

        $this->entityManager->remove($label);
        $this->entityManager->flush();

        return $this->json(['message' => 'Label deleted successfully']);
    }
}
