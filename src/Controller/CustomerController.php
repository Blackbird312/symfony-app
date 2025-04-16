<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CustomerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    // #[Route('/customer', name: 'app_customer')]
    // public function index(): Response
    // {
    //     return $this->render('customer/index.html.twig', [
    //         'controller_name' => 'CustomerController',
    //     ]);
    // }

    #[Route('/customer/new', name: 'app_customer_new')]
    public function new(): Response
    {
        // Create a new Customer instance
        $customer = new Customer();
        $customer->setFullName('John Doe')
            ->setDriverLicense('D123456')
            ->setCin('CIN789');

        // Persist the customer to the database
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new Response('New customer added with id ' . $customer->getId());
    }

    #[Route('/customer/all', name: 'app_customer_all', methods: ['GET'])]
    public function getAll()
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        // return $this->json($customers);
        return $this->render('customer/index.html.twig', [
                    'customers' => $customers
                ]);
    }

    //! check with teacher how to handle ereors for this one, and how is this handled.
    // #[Route('/customjni/{id}', name: 'app_customer_one', methods: ['GET'])]
    // public function getOne(Customer $customer, $id)
    // {
    //     return $this->json($customer);
    // }

    #[Route('/customer/{id}', name: 'app_customer_one', methods: ['GET'])]
    public function getOne($id)
    {
        try {
            $customer = $this->entityManager->getRepository(Customer::class)->find($id);
            if (!$customer) {
                throw new \Exception('Customer not found');
            }
            return $this->json($customer);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/customer/delete/{id}', name: 'app_customer_delete')]
    public function delete($id): Response
    {
        try {
            $customer = $this->entityManager->getRepository(Customer::class)->find($id);
            if (!$customer) {
                throw new \Exception('Customer not found');
            }
            $this->entityManager->remove($customer);
            $this->entityManager->flush();
            return $this->json(['message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/customer/edit/{id}', name: 'app_customer_edit')]
    public function edit($id): Response
    {
        try {
            $customer = $this->entityManager->getRepository(Customer::class)->find($id);
            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            $customer->setFullName("Mohammed EL MIMOUNI");
            $customer->setDriverLicense("a87wd6a9d");
            $customer->setCin("wd0aw90d8");

            $this->entityManager->flush();

            return $this->json(["Customer updated seccussfully" => $customer]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
