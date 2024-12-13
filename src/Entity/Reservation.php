<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Date de l'événement
    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date de l'événement ne peut pas être vide.")]
    #[Assert\GreaterThan("today + 1 hour", message: "La réservation doit être effectuée au moins 24 heures à l'avance.")]
    private ?\DateTimeInterface $date = null;

    // Plage horaire de la réservation
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La plage horaire ne peut pas être vide.")]
    private ?string $timeSlot = null;

    // Nom de l'événement
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'événement ne peut pas être vide.")]
    private ?string $eventName = null;

    // Relation ManyToOne avec User
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Vérifie la disponibilité de la plage horaire avant de persister
     * @ORM\PrePersist
     */
    public function validateUniqueTimeSlot(ExecutionContextInterface $context): void
    {
        $existingReservation = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findOneBy([
                'date' => $this->date,
                'timeSlot' => $this->timeSlot
            ]);

        if ($existingReservation) {
            $context->buildViolation('Cette plage horaire est déjà réservée.')
                ->atPath('timeSlot')
                ->addViolation();
        }
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getTimeSlot(): ?string
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(string $timeSlot): self
    {
        $this->timeSlot = $timeSlot;
        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Cette méthode permet d'obtenir l'EntityManager à l'intérieur de l'entité
     */
    private function getDoctrine()
    {
        return $this->getManager();
    }

    /**
     * Fonction simulée pour récupérer l'EntityManager, cela dépend de votre contexte exact.
     */
    private function getManager()
    {
        // Utilisez ici le service Doctrine de votre contrôleur si nécessaire pour manipuler la base de données
        // Cette méthode est juste un exemple pour illustrer.
        return $this->manager;
    }
}
