<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarImage;
use App\Form\CarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;               // ← add
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            // 1) Handle uploaded images
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('images')->getData();

            foreach ($imageFiles as $file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('car_images_directory'),
                    $newFilename
                );

                $carImage = new CarImage();
                $carImage->setFileName($newFilename);
                $car->addCarImage($carImage);
            }
            // 2) Persist Car (and its new images)
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
            // Handle any newly uploaded images
            /** @var UploadedFile[] $imageFiles */
            $imageFiles = $form->get('images')->getData();

            foreach ($imageFiles as $file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('car_images_directory'),
                    $newFilename
                );

                $carImage = new CarImage();
                $carImage->setFileName($newFilename);
                $car->addCarImage($carImage);
            }

            // Save changes
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

    #[Route('/car/image/delete/{id}', name: 'app_car_image_delete', methods: ['POST'])]
    public function deleteImage(Request $request, CarImage $image): RedirectResponse
    {
        $car = $image->getCar();

        // CSRF check
        $submittedToken = $request->request->get('_token');
        if (! $this->isCsrfTokenValid('delete-image'.$image->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        // remove the file from disk (optional but recommended)
        $filename = $image->getFileName();
        $uploadDir = $this->getParameter('car_images_directory');
        @unlink($uploadDir.'/'.$filename);

        // remove from database
        $this->entityManager->remove($image);
        $this->entityManager->flush();

        $this->addFlash('success', 'Image deleted.');

        // redirect back to the same car view
        return $this->redirectToRoute('app_car_view', ['id' => $car->getId()]);
    }
}
