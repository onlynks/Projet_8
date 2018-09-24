<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Form\TaskType;
use AppBundle\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(TaskRepository $taskRepository, EntityManager $entityManager)
    {
        $this->taskRepository = $taskRepository->repository;
        $this->em = $entityManager;
    }

    /**
     * @Route("/tasks", name="task_list",methods={"GET"})
     */
    public function list()
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $this->taskRepository->findAll(),
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create", methods={"POST", "GET"})
     */
    public function create(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');


            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit", methods={"PUT", "GET"})
     */
    public function edit(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTask(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTask(Task $task, Request $request): Response
    {
        $this->denyAccessUnlessGranted('delete', $task);

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
