<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PaymentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/payment/all', name: 'app_payment_all', methods: ['GET'])]
    public function getAll(): Response
    {
        $payments = $this->entityManager
            ->getRepository(Payment::class)
            ->findAll();

        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/payment/view/{id}', name: 'app_payment_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $payment = $this->entityManager
            ->getRepository(Payment::class)
            ->find($id);

        if (!$payment) {
            throw $this->createNotFoundException('Payment not found');
        }

        return $this->render('payment/view.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/payment/create', name: 'app_payment_create', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($payment);
            $this->entityManager->flush();

            $this->addFlash('success', 'Payment created successfully.');
            return $this->redirectToRoute('app_payment_all');
        }

        return $this->render('payment/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/payment/edit/{id}', name: 'app_payment_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Payment $payment): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // managed entity, just flush
            $this->entityManager->flush();

            $this->addFlash('success', 'Payment updated successfully.');
            return $this->redirectToRoute('app_payment_all');
        }

        return $this->render('payment/edit.html.twig', [
            'form'    => $form->createView(),
            'payment' => $payment,
        ]);
    }

    #[Route('/payment/delete/{id}', name: 'app_payment_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $payment = $this->entityManager
            ->getRepository(Payment::class)
            ->find($id);

        if (!$payment) {
            throw $this->createNotFoundException('Payment not found');
        }

        $this->entityManager->remove($payment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Payment deleted successfully.');
        return $this->redirectToRoute('app_payment_all');
    }
}
