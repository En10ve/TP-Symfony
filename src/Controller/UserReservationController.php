<?php

// src/Controller/UserReservationController.php
namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserReservationController extends AbstractController
{
    /**
     * @Route("/profile/reservations", name="user_reservation_index")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(): Response
    {
        // Récupérer toutes les réservations de l'utilisateur connecté
        $reservations = $this->getUser()->getReservations();

        return $this->render('user/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/profile/reservations/new", name="user_reservation_new")
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setUser($this->getUser()); // Associer la réservation à l'utilisateur connecté
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('user_reservation_index');
        }

        return $this->render('user/reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/reservations/{id}", name="user_reservation_show")
     * @Security("is_granted('ROLE_USER')")
     */
    public function show(Reservation $reservation): Response
    {
        // Afficher une réservation spécifique
        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à accéder à cette réservation.');
        }

        return $this->render('user/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}

