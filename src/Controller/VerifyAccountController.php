<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\VerifyAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyAccountController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    #[Route('/verify_account', methods: 'GET')]
    public function verifyAccount(Request $request, VerifyAccountRepository $verifyRepo, UserRepository $userRepo): Response
    {
        $queryParams = (object)$request->query->all();

        $savedToken = $verifyRepo->findTokenByTokenValue($queryParams->token);
        if ($savedToken[0]['token'] && ($savedToken[0]['expires'] > strtotime(date('Y-m-d h:i:s')))) {
            $user = $userRepo->find($queryParams->user_id);

            $user->setAllowed(true);
            $this->em->persist($user);
            $this->em->flush();

            $token = $verifyRepo->find($queryParams->token_id);
            $this->em->remove($token);
            $this->em->flush();
        }
        return $this->json("", Response::HTTP_NO_CONTENT);
    }

//    i think i used this route just once
//    after that i added remove token in upper method
//    #[Route('/remove_token/{id}', methods: 'DELETE')]
//    public function removeToken(Request $request, VerifyAccountRepository $verifyRepo,int $id): Response
//    {
//        $token = $verifyRepo->find($id);
//        $this->em->remove($token);
//        $this->em->flush();
//
//        return $this->json("", Response::HTTP_NO_CONTENT);
//    }

}
