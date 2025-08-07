<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    public function testCreateTask(): void
    {
        $user = new User();
        $service = new TaskService();

        $data = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'status' => 'новая'
        ];

        $task = $service->createTask($data, $user);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Title', $task->getTitle());
        $this->assertEquals('Test Description', $task->getDescription());
        $this->assertEquals('новая', $task->getStatus());
        $this->assertSame($user, $task->getOwner());
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
    }

    public function testUpdateTask(): void
    {
        $task = new Task();
        $service = new TaskService();

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Desc',
            'status' => 'в процессе'
        ];

        $service->updateTask($task, $data);

        $this->assertEquals('Updated Title', $task->getTitle());
        $this->assertEquals('Updated Desc', $task->getDescription());
        $this->assertEquals('в процессе', $task->getStatus());
    }
}
