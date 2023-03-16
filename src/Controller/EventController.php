<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/evenement', name: 'event.index')]
    public function index(EventRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $eventsCome = count($repository->findEventCome());
        $events = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'eventsCome' => $eventsCome
        ]);
    }

    #[Route('/evenement/nouveau', name: 'event.create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($event);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre Event à été créer avec succès !'
            );
            return $this->redirectToRoute('event.index');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/evenement/{id}', name: 'event.show')]
    public function show(Event $event): Response
    {


        return $this->render('event/show.html.twig', [
            'event' => $event
        ]);
    }


    #[Route('/evenement/join/{id}', name: 'event.join')]
    public function join(Event $event): Response
    {

        /* @todo add mailer */
        $this->addFlash(
            'success',
            'Un email à été envoyer a l\'administrateur de l\'evenement'
        );

        return $this->redirectToRoute('event.index');
    }
}
