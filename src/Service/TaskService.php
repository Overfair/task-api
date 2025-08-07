<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;

class TaskService
{
    public function createTask(array $data, User $user): Task
    {
        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDescription($data['description'] ?? null);
        $task->setStatus($data['status'] ?? 'новая');
        $task->setOwner($user);
        $task->setCreatedAt(new \DateTimeImmutable());

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $task->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $task->setStatus($data['status']);
        }

        return $task;
    }
}
