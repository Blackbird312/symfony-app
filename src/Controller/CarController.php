<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CarController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    #[Route('/car/all', name: 'app_car_all', methods: ['GET'])]
    public function getAll(): Response
    {
        $cars = $this->entityManager
            ->getRepository(Car::class)
            ->findAll();

        return $this->render('car/index.html.twig', [
            'cars' => $cars,
        ]);
    }

    #[Route('/car/view/{id}', name: 'app_car_view', methods: ['GET'])]
    public function view(int $id): Response
    {
        $car = $this->entityManager
            ->getRepository(Car::class)
            ->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        return $this->render('car/view.html.twig', [
            'car' => $car,
        ]);
    }

    #[Route('/car/create', name: 'app_car_create', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $car  = new Car();
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($car);
            $this->entityManager->flush();

            $this->addFlash('success', 'Car created successfully.');
            return $this->redirectToRoute('app_car_all');
        }

        return $this->render('car/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/car/edit/{id}', name: 'app_car_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Car $car): Response
    {
        $form = $this->createForm(CarType::class, $car);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // entity is already managed
            $this->entityManager->flush();

            $this->addFlash('success', 'Car updated successfully.');
            return $this->redirectToRoute('app_car_all');
        }

        return $this->render('car/edit.html.twig', [
            'form' => $form->createView(),
            'car'  => $car,
        ]);
    }

    #[Route('/car/delete/{id}', name: 'app_car_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $car = $this->entityManager
            ->getRepository(Car::class)
            ->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        $this->entityManager->remove($car);
        $this->entityManager->flush();

        $this->addFlash('success', 'Car deleted successfully.');
        return $this->redirectToRoute('app_car_all');
    }
}
