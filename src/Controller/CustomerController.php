<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CustomerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/customer/all', name: 'app_customer_all', methods: ['GET'])]
    public function getAll(): Response
    {
        $customers = $this->entityManager
            ->getRepository(Customer::class)
            ->findAll();

        return $this->render('customer/index.html.twig', [
            'customers' => $customers,
        ]);
    }

    #[Route('/customer/view/{id}', name: 'app_customer_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $customer = $this->entityManager
            ->getRepository(Customer::class)
            ->find($id);

        if (!$customer) {
            throw $this->createNotFoundException('Customer not found');
        }

        return $this->render('customer/view.html.twig', [
            'customer' => $customer,
        ]);
    }

      #[Route('/customer/edit/{id}', name: 'app_customer_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Customer $customer): Response
    {
        // build form, pre-filled with $customer
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // no need to persist—entity is managed
            $this->entityManager->flush();

            $this->addFlash('success', 'Customer updated successfully.');
            return $this->redirectToRoute('app_customer_all');
        }

        return $this->render('customer/edit.html.twig', [
            'form'     => $form->createView(),
            'customer' => $customer,
        ]);
    }

    #[Route('/customer/delete/{id}', name: 'app_customer_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $customer = $this->entityManager
            ->getRepository(Customer::class)
            ->find($id);

        if (!$customer) {
            throw $this->createNotFoundException('Customer not found');
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_customer_all');
    }

     #[Route('/customer/create', name: 'app_customer_create', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_customer_all');
        }

        return $this->render('customer/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
