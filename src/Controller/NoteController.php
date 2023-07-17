<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/note')]
class NoteController extends AbstractController
{

    public function __construct(private NoteRepository $repo, private EntityManagerInterface $em) {}


    #[Route(methods: 'GET')]
    public function all(): JsonResponse
    {
        return $this->json($this->repo->findAll());
    }
    #[Route('/{id}', methods: 'POST')]
    public function add(Request $request, SerializerInterface $serializer, Student $student): JsonResponse {
        try {
            $note = $serializer->deserialize($request->getContent(), Note::class, 'json');
        } catch (\Exception $e) {
            return $this->json('Invalid Body', 400);
        }

        $note->setStudent($student);
        $note->setCreatedAt(new \DateTime());
        
        $this->em->persist($note);
        $this->em->flush();
        return $this->json($note, 201);

    }
}
