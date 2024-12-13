<?php

// src/Controller/AdminReservationController.php
namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminReservationController extends AbstractController
{
    /**
     * @Route("/admin/reservations", name="admin_reservation_index")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les réservations
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        // Affichage des réservations dans une vue Twig
        return $this->render('admin/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/admin/reservations/new", name="admin_reservation_new")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Redirection après la création de la réservation
            return $this->redirectToRoute('admin_reservation_index');
        }

        return $this->render('admin/reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/reservations/{id}/edit", name="admin_reservation_edit")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Reservation $reservation, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirection après modification de la réservation
            return $this->redirectToRoute('admin_reservation_index');
        }

        return $this->render('admin/reservation/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/reservations/{id}/delete", name="admin_reservation_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // Supprimer la réservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        // Redirection après suppression
        return $this->redirectToRoute('admin_reservation_index');
    }
}
