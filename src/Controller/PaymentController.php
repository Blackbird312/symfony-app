<?php

namespace App\Controller;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'paymentController',
        ]);
    }

    #[Route('/payment/new', name: 'app_payment_new')]
    public function new(): Response
    {
        // Create a new payment instance
        $payment = new Payment();
        $payment->setAmount(100)
        ->setPaymentDate(new \DateTime());

        // Persist the payment to the database
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return new Response('New payment added with id ' . $payment->getId());
    }

    #[Route('/payment/all', name: 'app_payment_all', methods: ['GET'])]
    public function getAll()
    {
        $payments = $this->entityManager->getRepository(Payment::class)->findAll();
        return $this->json($payments);
    }

    //! check with teacher how to handle ereors for this one, and how is this handled.
    // #[Route('/customjni/{id}', name: 'app_payment_one', methods: ['GET'])]
    // public function getOne(payment $payment, $id)
    // {
    //     return $this->json($payment);
    // }

    #[Route('/payment/{id}', name: 'app_payment_one', methods: ['GET'])]
    public function getOne($id)
    {
        try {
            $payment = $this->entityManager->getRepository(Payment::class)->find($id);
            if (!$payment) {
                throw new \Exception('payment not found');
            }
            return $this->json($payment);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/payment/delete/{id}', name: 'app_payment_delete')]
    public function delete($id): Response
    {
        try {
            $payment = $this->entityManager->getRepository(Payment::class)->find($id);
            if (!$payment) {
                throw new \Exception('payment not found');
            }
            $this->entityManager->remove($payment);
            $this->entityManager->flush();
            return $this->json(['message' => 'payment deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/payment/edit/{id}', name: 'app_payment_edit')]
    public function edit($id): Response
    {
        try {
            $payment = $this->entityManager->getRepository(Payment::class)->find($id);
            if (!$payment) {
                throw new \Exception('payment not found');
            }

            $payment->setAmount(1200)
            ->setPaymentDate(new \DateTime());

            $this->entityManager->flush();

            return $this->json(["payment updated seccussfully" => $payment]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
