<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(private TaskService $taskService) {

    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request, TaskRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $status = $request->query->get('status');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = min(100, (int) $request->query->get('limit', 10));
        $offset = ($page - 1) * $limit;

        $criteria = ['owner' => $user];
        if ($status) {
            $criteria['status'] = $status;
        }

        $tasks = $repo->findBy($criteria, ['createdAt' => 'DESC'], $limit, $offset);

        return $this->json($tasks, 200, [], ['groups' => 'task:read']);
    }


    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['error' => 'Пользователь не найден или не авторизован'], 401);
        }

        $task = $this->taskService->createTask($data, $user);
        $em->persist($task);
        $em->flush();

        return $this->json($task, 201, [], ['groups' => 'task:read']);
    }


    #[Route('/{id}', methods: ['GET'])]
    public function show(Task $task): JsonResponse
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();

        if (!$user || $task->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Доступ запрещён'], 403);
        }

        return $this->json($task, 200, [], ['groups' => 'task:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Task $task, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!$user || $task->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Доступ запрещён'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $this->taskService->updateTask($task, $data);
        $em->flush();

        return $this->json($task, 200, [], ['groups' => 'task:read']);
    }


    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if (!$user || $task->getOwner()->getId() !== $user->getId()) {
            return $this->json(['error' => 'Доступ запрещён'], 403);
        }

        $em->remove($task);
        $em->flush();

        return $this->json(['message' => 'Задача удалена']);
    }

}
