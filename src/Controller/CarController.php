<?php

namespace App\Controller;

use App\Entity\Car;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    #[Route('/car', name: 'app_car')]
    public function index(): Response
    {
        return $this->render('car/index.html.twig', [
            'controller_name' => 'carController',
        ]);
    }

    #[Route('/car/new', name: 'app_car_new')]
    public function new(): Response
    {
        // Create a new car instance
        $car = new Car();
        $car->setBrand('Toyota')
        ->setModel('Camry')
        ->setYear(2022)
        ->setCarLicense('ABC123')
        ->setAvailable(true);

        // Persist the car to the database
        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return new Response('New car added with id ' . $car->getId());
    }

    #[Route('/car/all', name: 'app_car_all', methods: ['GET'])]
    public function getAll()
    {
        $cars = $this->entityManager->getRepository(Car::class)->findAll();
        return $this->json($cars);
    }

    //! check with teacher how to handle ereors for this one, and how is this handled.
    // #[Route('/customjni/{id}', name: 'app_car_one', methods: ['GET'])]
    // public function getOne(car $car, $id)
    // {
    //     return $this->json($car);
    // }

    #[Route('/car/{id}', name: 'app_car_one', methods: ['GET'])]
    public function getOne($id)
    {
        try {
            $car = $this->entityManager->getRepository(Car::class)->find($id);
            if (!$car) {
                throw new \Exception('car not found');
            }
            return $this->json($car);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/car/delete/{id}', name: 'app_car_delete')]
    public function delete($id): Response
    {
        try {
            $car = $this->entityManager->getRepository(Car::class)->find($id);
            if (!$car) {
                throw new \Exception('car not found');
            }
            $this->entityManager->remove($car);
            $this->entityManager->flush();
            return $this->json(['message' => 'car deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/car/edit/{id}', name: 'app_car_edit')]
    public function edit($id): Response
    {
        try {
            $car = $this->entityManager->getRepository(Car::class)->find($id);
            if (!$car) {
                throw new \Exception('car not found');
            }

            $car->setBrand('Mitsubishi')
            ->setModel('Lancer')
            ->setYear(2025)
            ->setCarLicense('ABC123')
            ->setAvailable(true);

            $this->entityManager->flush();

            return $this->json(["car updated seccussfully" => $car]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
