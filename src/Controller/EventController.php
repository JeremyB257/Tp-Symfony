<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/evenement', name: 'event.index')]
    public function index(EventRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $search = $request->get('search');

        $events = $paginator->paginate(
            $repository->findEventByName($search),
            $request->query->getInt('page', 1),
            10
        );
        $eventsCome = count($repository->findEventCome());


        return $this->render('event/index.html.twig', [
            'events' => $events,
            'eventsCome' => $eventsCome
        ]);
    }

    #[Route('/evenement/nouveau', name: 'event.create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $event = new Event();
        $event->setStartDate(new \DateTime())->setEndDate(new \DateTime());
        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload
            /** @var UploadedFile */
            $imgFile = $form->get('imgFile')->getData();
            if ($imgFile) {
                $fileName = uniqid() . '.' . $imgFile->guessExtension();
                $imgFile->move($this->getParameter('event_uploads'), $fileName);
                //stocker le nom du fichier dans la BDD
                $event->setImg($fileName);
            }
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
    public function join(Event $event, MailerInterface $mailer): Response
    {

        $email = (new Email())
            ->from('admin@eventime.com')
            ->to('admin@eventime.com', 'utilisateur@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Une personne souhaite rejoindre un evenement')
            ->text('Evenement' . $event->getTitle())
            ->html('<p>Evenement' . $event->getTitle() . '</p>');

        $mailer->send($email);


        $this->addFlash(
            'success',
            'Un email à été envoyer a l\'administrateur de l\'evenement'
        );

        return $this->redirectToRoute('event.index');
    }
}
