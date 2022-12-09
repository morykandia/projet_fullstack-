<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\PDOFactory;
use App\Manager\UserManager;
use App\Route\Route;
use App\Service\JwtHelper;

class SecurityController extends AbstractController
{
    #[Route('/create', name: "createuser", methods: ["POST"])]
    public function CreateUser ()
    {

        $htmlUsername = strip_tags($_POST['username']);
        $htmlPassword = strip_tags($_POST['password']);
        $htmlEmail = strip_tags($_POST['email']);
        $htmlFirstName = strip_tags($_POST['firstName']);
        $htmlLastname = strip_tags($_POST['lastName']);
        $htmlGender = strip_tags($_POST['gender']);
        
        $user = new User ();

        $user->setUsername( $htmlUsername);
        $user->setHashedPassword( $htmlPassword);
        $user->setEmail( $htmlEmail);
        $user->setFirstName( $htmlFirstName);
        $user->setLastName( $htmlLastname);
        $user->setGender( $htmlGender);

        $createUserManager = new UserManager((new PDOFactory()));

        $createUserManager->insertUser($user);

        $this->renderJSON([
            "message" => "user creted succesfully",
            "user"=>$user
        ]);

    }


    #[Route('/login', name: "login", methods: ["POST"])]
    public function login()
    {
       
        $formEmail = $_SERVER['PHP_AUTH_USER'];
        $formPwd = $_SERVER['PHP_AUTH_PW'];

       

        $userManager = new UserManager(new PDOFactory());
        $user = $userManager->getByUser($formEmail);
       

        if (!$user) {
            $this->renderJSON([
                "message" => "no user found"
            ]);
            exit;
        }

        if ($user->passwordMatch($formPwd)) {
            

            $this->renderJson( [
                "jwt" => JwtHelper::buildJWT($user)
            ]);
        }

        $this->renderJSON([
            "message" => "no MATCHING PASSWORD found"
        ]);
        exit;
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        session_destroy();
        //header('location: /');
        exit;
    }

    
    #[Route('/account', name: 'account', methods: ['GET'])]
    public function account()
    {
        $userManager = new UserManager(new PDOFactory());

        if (isset($_SESSION)) {
            $user = $userManager->getUserbyId($_SESSION['auth']);
            $this->renderJSON(compact('user'));
        }
       // header('location: /?error=notfound');
    }
}
