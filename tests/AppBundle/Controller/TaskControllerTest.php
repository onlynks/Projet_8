<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Repository\TaskRepository;
use Tests\AppBundle\Boot;

class TaskControllerTest extends Boot
{
    protected  $taskRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->taskRepository = new TaskRepository($this->em);
    }

    public function testList()
    {
        $client = static::logAsAdmin();

        for($i = 1; $i<5; $i++) {
            $this->createRandomTask($this->taskRepository);
        }

        $crawler = $client->request('GET','/tasks');

        $selections = $crawler->filter('div > h4 > a')->links();
        $data = [];

        foreach ($selections as $selection){
            $data[] = $selection->getNode()->textContent;
        }

        $tasks = $this->taskRepository->repository->findAll();

        $taskName = [];

        foreach($tasks as $task){
            $taskName[] = $task->getTitle();
        }

        $this->assertSame($data, $taskName);
    }

    public function testCreate()
    {
        $client = static::logAsAdmin();

        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
        'task[title]' => 'Things to do',
        'task[content]' => 'Clean my room'
        ]);
        $client->submit($form);

        $client->followRedirect();

        $content  =  $client->getResponse()->getContent();

        $this->assertContains('La tâche a bien été ajoutée.', $content);
    }

    public function testDeleteAsAdmin()
    {
        $client = static::logAsAdmin();

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');

        $this->em->persist($task);
        $this->em->flush();

        $lastTask = $this->taskRepository->getLast();
        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$lastTask->getId().'/delete"] > button')->form();
        $client->submit($form);

        $client->followRedirect();

        $content = $client->getResponse()->getContent();

        $this->assertContains('La tâche a bien été supprimée.', $content);
    }

    public function testUpdate() {

        $task = $this->createRandomTask($this->taskRepository);

        $client = static::logAsAdmin();

        $crawler = $client->request('GET', '/tasks/'.$task->getId().'/edit');

        $tasTitle = $task->getTitle().'!';
        $taskContent = $task->getContent().'!';

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => $tasTitle,
            'task[content]' => $taskContent
        ]);
        $client->submit($form);

        $client->followRedirect();

        $updatedTask = $this->taskRepository->repository->find($task);

        $taskData = [$updatedTask->getTitle(), $updatedTask->getContent()];

        $this->assertSame([$tasTitle,$taskContent], $taskData);

    }

    public function testToggle() {

        $client = static::logAsAdmin();

        $task = new Task();
        $task->setTitle('test toggle');
        $task->setContent('test toggle');

        $this->em->persist($task);
        $this->em->flush();

        $taskToTest = $this->taskRepository->getLast();

        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$taskToTest->getId().'/toggle"] > button')->form();

        $client->submit($form);

        $client->followRedirect();

        $this->assertContains('La tâche '.$task->getTitle().' a bien été marquée comme faite.', $client->getResponse()->getContent());
    }

    public function tearDown()
    {
        $tasksCreated = $this->taskRepository->repository->findAll();

        foreach($tasksCreated as $task) {
            $this->em->remove($task);
        }
        $this->em->flush();

        parent::tearDown();
    }

}
