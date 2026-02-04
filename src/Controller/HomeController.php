<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // INFO: $user = new User();
        // INFO: $user->setEmail('john@doe.fr')
        // INFO:     ->setUsername('johndoe')
        // INFO:     ->setPassword($hasher->hashPassword($user, 'password123'))
        // INFO: ->setRoles([]);

        // INFO: $em->persist($user);
        // INFO: $em->flush();
        return $this->render('home/index.html.twig');
    }
}
