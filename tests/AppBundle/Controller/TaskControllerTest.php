<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Tests\AppBundle\Boot;

class TaskControllerTest extends Boot
{
    public function testList()
    {
        $em = $this->em;

        $client = static::logAsAdmin();

        $crawler = $client->request('GET','/tasks');

        $selections = $crawler->filter('div > h4 > a')->links();
        $data = [];

        foreach ($selections as $selection){
            $data[] = $selection->getNode()->textContent;
        }

        $tasks = $em->getRepository(Task::class)->findAll();

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

    public function testDelete()
    {
        $client = static::logAsAdmin();

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');

        $this->em->persist($task);
        $this->em->flush();

        $lastTask = $this->em->getRepository(Task::class)->getLast();
        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$lastTask->getId().'/delete"] > button')->form();
        $client->submit($form);

        $client->followRedirect();

        $content = $client->getResponse()->getContent();

        $this->assertContains('La tâche a bien été supprimée.', $content);
    }

    public function testDeleteNoAuthor() {

        $client = static::logAsUser();

        $task = $this->createRandomTask();

        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$task->getId().'/delete"] > button')->form();
        $client->submit($form);

        $client->followRedirect();

        $content = $client->getResponse()->getContent();

        $this->assertContains('Vous n\'avez pas les droits nécessaires pour supprimer cette tâche.', $content);

    }

    public function testUpdate() {

        $task = $this->createRandomTask();

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

        $updatedTask = $this->em->getRepository(Task::class)->find($task);

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

        $taskToTest = $this->em->getRepository(Task::class)->getLast();

        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->filter('form[action="/tasks/'.$taskToTest->getId().'/toggle"] > button')->form();

        $client->submit($form);

        $client->followRedirect();

        $this->assertContains('La tâche '.$task->getTitle().' a bien été marquée comme faite.', $client->getResponse()->getContent());
    }

}
