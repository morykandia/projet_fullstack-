<?php

namespace App\Controller;

use App\Factory\PDOFactory;
use App\Manager\UserManager;
use App\Route\Route;

class UserController extends AbstractController
{
    #[Route('/user/{username}', name: "username", methods: ["GET"])]
    public function userByUsername($username)
    {
        $manager = new UserManager(new PDOFactory());
        $user = $manager->getByUsername($username);
    }


    #[Route('/users', name: 'all-users', methods: ['GET'])]
    public function users()
    {
        $userManager = new UserManager(new PDOFactory());
        $userRole = $userManager->getUserbyId($_SESSION['auth'])->getRoles();

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $userRole['ROLE'] == 'ADMIN') {

            $userManager = new UserManager(new PDOFactory());
            $users = $userManager->getAllUsers();
            $title = 'list-users';

            $this->renderJson( compact('users', 'title'));
        }

        //header('location: /?error=not-authorized');
        exit;
    }


    #[Route('/users/{id}/delete', name: 'delete-users', methods: ['GET'])]
    public function deleteUser($id)
    {

        $userManager =  new UserManager(new PDOFactory());
        $userRole = $userManager->getUserbyId($_SESSION['auth'])->getRoles();
        if ($userRole['ROLE'] == 'ADMIN') {
            $userManager->deleteUser($id);
        }
        //header('location:' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    
    #[Route('/users/{id}/update', name: 'update-user', methods: ['GET', 'POST'])]
    public function updateUser($id)
    {

        $userManager =  new UserManager(new PDOFactory());
        $user = $userManager->getUserbyId($id);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderJSON( compact('user'));
        }
        $email = ['email' => $user->getEmail()];
        $data = array_merge($_POST, $email);
        $userManager->updateUser($id, $data);
        if ($userManager->getUserbyId($_SESSION['auth'])->getRoles()['ROLE'] == 'ADMIN') {
            if (preg_match('/account/', $_SERVER['HTTP_REFERER'])) {
                //header('location: /account');
                exit;
            }
            header('location: /users');
            exit;
        }
        //header('location: /account');
        exit;
    }
}