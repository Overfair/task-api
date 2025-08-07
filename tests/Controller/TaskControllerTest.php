<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TaskControllerTest extends WebTestCase
{
    public function testUnauthorizedAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/tasks');

        $this->assertResponseStatusCodeSame(401);
    }
}
