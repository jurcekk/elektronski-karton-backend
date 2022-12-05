<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\VerifyAccount;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\VerifyAccountRepository;
use App\Service\RegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nebkam\SymfonyTraits\FormTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailRepository;

class UserController extends AbstractController
{
    use FormTrait;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    #[Route('/users', methods: 'POST')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher,MailerInterface $mailer): Response
    {
        $user = new User();
        $plainTextPassword = json_decode($request->getContent(), false);

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainTextPassword->password
        );
        //password hasher configured to hash in BCRYPT algorithm
        $this->handleJSONForm($request, $user, UserType::class);

        $user->setPassword($hashedPassword);
        $user->setAllowed(false);
        $user->setTypeOfUser(3);

        $email = new EmailRepository();
        $registrationRepo = new RegistrationRepository();

        $token = new VerifyAccount($registrationRepo->handleToken());

        $this->em->persist($user);
        $this->em->flush();
        $this->em->persist($token);
        $this->em->flush();

        $email->sendWelcomeEmail($user,$mailer,$token);

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user_created']);
    }

    #[Route('/verify_account',methods: 'GET')]
    public function verifyAccount(Request $request,VerifyAccountRepository $verifyRepo,UserRepository $userRepo):Response
    {
        $queryParams = (object)$request->query->all();
        $user = $userRepo->find($queryParams->id);

        $savedToken = $verifyRepo->findTokenByTokenValue($queryParams->token);
        if($savedToken && ($savedToken->expires < strtotime(date('Y-m-d h:i:s'))) ) {
            $user->setAllowed(true);

            $this->em->persist($user);
            $this->em->flush();
        }
        return $this->json("",Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}', methods: 'PUT')]
    public function updateUser(Request $request, int $id, UserRepository $repo): Response
    {
        $user = $repo->find($id);

        $this->handleJSONForm($request, $user, UserType::class);

        $this->em->persist($user);
        $this->em->flush();

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user_created']);
    }

    #[Route('/users/{id}', methods: 'DELETE')]
    public function deleteUser(Request $request, int $id, UserRepository $repo): Response
    {
        $user = $repo->find($id);

        $this->em->remove($user);
        $this->em->flush();

        return $this->json("", Response::HTTP_NO_CONTENT);
    }

    #[Route('/users', methods: 'GET')]
    public function showAllUsers(Request $request, UserRepository $repo): Response
    {
        $allUsers = $repo->findAll();

        return $this->json($allUsers, Response::HTTP_OK, [], ['groups' => 'user_showAll']);
    }

    #[Route('/users/{id}', methods: 'GET')]
    public function showOneUser(Request $request, int $id, UserRepository $repo): Response
    {
        $user = $repo->find($id);

        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user_showAll']);
    }

    //this method will have its place here for some time until i made a better one
    //for now it will be here just to make sure password verify the symfony hashed password
    #[Route('/password_verify/{id}', methods: 'POST')]
    public function passwordVerify(int $id, UserRepository $repo, Request $request): Response
    {
        //this method work for some reason.
        //nice!
        $user = $repo->find($id);

        $data = json_decode($request->getContent(), false);

        $okay = password_verify($data->password, $user->getPassword());
        return $this->json($okay, Response::HTTP_OK, [], ['groups' => 'user_ok']);
    }


    //image upload method here

}
