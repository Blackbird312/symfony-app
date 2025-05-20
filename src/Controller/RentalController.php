<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Rental;
use App\Form\RentalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RentalController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/rental/all', name: 'app_rental_all', methods: ['GET'])]
    #[Route('/',            name: 'app_rental_home', methods: ['GET'])]
    public function list(): Response
    {
        $rentals = $this->entityManager
            ->getRepository(Rental::class)
            ->findAll();

        return $this->render('rental/index.html.twig', [
            'rentals' => $rentals,
        ]);
    }

    #[Route('/rental/view/{id}', name: 'app_rental_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $r = $this->entityManager->getRepository(Rental::class)->find($id);
        if (!$r) {
            throw $this->createNotFoundException('Rental not found');
        }

        return $this->render('rental/view.html.twig', [
            'rental' => $r,
        ]);
    }

    // src/Controller/RentalController.php

#[Route('/rental/create', name: 'app_rental_create', methods: ['GET','POST'])]
public function create(Request $request): Response
{
    $rental = new Rental();
    $form   = $this->createForm(RentalType::class, $rental);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // 1) build a new Payment from the two unmapped fields
        $payment = new Payment();
        $payment
            ->setAmount($form->get('paymentAmount')->getData())
            ->setPaymentDate($form->get('paymentDate')->getData());

        // 2) persist the payment first
        $this->entityManager->persist($payment);

        // 3) link it to the rental
        $rental->setPayment($payment);

        // 4) now persist the rental
        $this->entityManager->persist($rental);
        $this->entityManager->flush();

        $this->addFlash('success', 'Rental created successfully with payment.');
        return $this->redirectToRoute('app_rental_all');
    }

    return $this->render('rental/create.html.twig', [
        'form' => $form->createView(),
    ]);
}


     #[Route('/rental/edit/{id}', name: 'app_rental_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Rental $rental): Response
    {
        // 1) create the form and pre-populate the unmapped payment fields if there is one
        $form = $this->createForm(RentalType::class, $rental);
        $existingPayment = $rental->getPayment();
        if ($existingPayment) {
            $form->get('paymentAmount')->setData($existingPayment->getAmount());
            $form->get('paymentDate')->setData($existingPayment->getPaymentDate());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 2) get the submitted payment data
            $amount = $form->get('paymentAmount')->getData();
            $date   = $form->get('paymentDate')->getData();

            // 3) if no payment existed, create one
            if (!$existingPayment) {
                $existingPayment = new Payment();
                $this->entityManager->persist($existingPayment);
                $rental->setPayment($existingPayment);
            }

            // 4) update the payment entity
            $existingPayment
                ->setAmount($amount)
                ->setPaymentDate($date);

            // 5) flush both rental & payment changes
            $this->entityManager->flush();

            $this->addFlash('success', 'Rental updated successfully with payment.');
            return $this->redirectToRoute('app_rental_all');
        }

        return $this->render('rental/edit.html.twig', [
            'form'   => $form->createView(),
            'rental' => $rental,
        ]);
    }

    #[Route('/rental/delete/{id}', name: 'app_rental_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $r = $this->entityManager->getRepository(Rental::class)->find($id);
        if (!$r) {
            throw $this->createNotFoundException('Rental not found');
        }

        $this->entityManager->remove($r);
        $this->entityManager->flush();

        $this->addFlash('success', 'Rental deleted successfully.');
        return $this->redirectToRoute('app_rental_all');
    }
}
