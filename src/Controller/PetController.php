<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Form\PetType;
use App\Repository\PetRepository;
use App\Service\UploadImage;
use Doctrine\ORM\EntityManagerInterface;
use Nebkam\SymfonyTraits\FormTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PetController extends AbstractController
{
    use FormTrait;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    #[Route('/pets', methods: 'POST')]
    public function createPet(Request $request): Response
    {
        $pet = new Pet();
        $this->handleJSONForm($request,$pet,PetType::class);
        $this->em->persist($pet);
        $this->em->flush();

        return $this->json($pet,Response::HTTP_CREATED,[],['groups'=>'pet_created']);
    }

    #[Route('/pet_upload_image/{id}',methods:'POST')]
    public function uploadProfileImage(Request $request,PetRepository $repo,int $id):Response
    {
        $pet = $repo->find($id);
        $uploadImage = new UploadImage($request,$pet,$this->em);

        $uploadImage->upload();
        return $this->json($pet,Response::HTTP_CREATED,[],['groups'=>'pet_created']);
    }

    #[Route('/pets/{id}',methods:'PUT')]
    public function updatePet(Request $request,PetRepository $repo,int $id):Response
    {
        $pet = $repo->find($id);

        $this->handleJSONForm($request,$pet,PetType::class);

        $this->em->persist($pet);
        $this->em->flush();

        return $this->json($pet,Response::HTTP_CREATED,[],['groups'=>'pet_created']);
    }

    #[Route('/pets/{id}',methods:'DELETE')]
    public function deletePet(Request $request,PetRepository $repo,int $id):Response
    {
        $pet = $repo->find($id);

        $this->em->remove($pet);
        $this->em->flush();

        return $this->json("",Response::HTTP_NO_CONTENT);
    }

    #[Route('/pets/{id}',methods:'GET')]
    public function showOnePet(Request $request,PetRepository $repo,int $id):Response
    {
        $pet = $repo->find($id);

        return $this->json($pet,Response::HTTP_OK,[],['groups'=>'pet_showAll']);
    }

    #[Route('/pets',methods:'GET')]
    public function showAllPets(Request $request,PetRepository $repo):Response
    {
        $pets = $repo->findAll();

        return $this->json($pets,Response::HTTP_OK,[],['groups'=>'pet_showAll']);
    }
}
