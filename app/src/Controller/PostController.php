<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Factory\PDOFactory;
use App\Manager\PostManager;
use App\Manager\UserManager;
use App\Route\Route;
use App\Service\JwtHelper;

class PostController extends AbstractController
{

    #[Route('/', name: "homepage", methods: ["GET"])]
    public function home()
    {
        $jwt = str_replace("Bearer ", "", getallheaders()['authorization'] ?? "");

        $decodedJwt = JwtHelper::verifyJwt($jwt);

        if (!$decodedJwt) {
            $this->renderJson([
                "message" => "wrong credentials"
            ]);
            die;
        }

        $manger = new PostManager(new PDOFactory());
        $posts = $manger->getAllPosts();

        $this->renderJson(["posts" => $posts]);
    }

    #[Route('/post/{id}', name: "post-id", methods: ["GET"])]
    public function postById($id)
    {
        $postManager = new PostManager(new PDOFactory());
        $userManager = new UserManager(new PDOFactory());

        $post = $postManager->getPostById($id);
        if (isset($_SESSION['auth'])) {
            $isAdmin = $userManager->getUserbyId($_SESSION['auth'])->getRoles()['ROLE'] == 'ADMIN' ? true : false;
        } else {
            $isAdmin = false;
        }
        $user = $userManager->getUserbyId($post->getAuthor());

        $author = $user == null ? 'ANON' : $user->getUsername();

        if (!$post) {
            //header('location: /?error=notfound');
            exit;
        }
        $this->renderJson(compact('post', 'author', 'isAdmin'));
        if (isset($_GET['admin'])) {
            //header('location:/posts');
        }
    }


    /**
     * @param $id
     * @return void
     */
    #[Route('/post/{id}/update', name: "update-post", methods: ["GET", "POST"])]
    public function updatePost(int $id)
    {
        $postManager = new PostManager(new PDOFactory());
        $post = $postManager->getPostById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderJson( compact('post'));
        }

        $postManager->updatePost($id, $_POST);
        //header('location: /post/' . $id . '?admin');
        exit;
    }


    /**
     *     * @return void
     */
    #[Route('/new', name: "new-post", methods: ["GET", "POST"])]
    public function insertPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->renderJson(["truyc" => 'posts/new']);
        }
        $postManager = new PostManager(new PDOFactory());

        $post = new Post();
        extract($_POST);
        $author_id = $_SESSION['auth'];
        $date = new \DateTime();
        $created_at = $date->format('d-m-Y');



        $post->hydrate(compact('title', 'content', 'author_id', 'created_at', 'img'));

        $postManager->insertPost($post);

        //header("location: /");
        exit;
    }
    /**
     *     * @return void
     */


    #[Route('/post/{id}/delete', name: "delete-post", methods: ["GET"])]
    public function deletePost($id)
    {
        $userManager =  new UserManager(new PDOFactory());
        $userRole = $userManager->getUserbyId($_SESSION['auth'])->getRoles();
        if ($userRole['ROLE'] == 'ADMIN') {
            $postManager = new PostManager(new PDOFactory());
            $postManager->deletePost($id);
        }
        if (isset($_GET['admin'])) {
            header('location: /posts');
            exit;
        }
       // header("location: /");
        exit;
    }

    
    #[Route('/posts', name: "all-posts", methods: ["GET"])]
    public function posts()
    {
        $userManager =  new UserManager(new PDOFactory());
        $userRole = $userManager->getUserbyId($_SESSION['auth'])->getRoles();

        if ($userRole['ROLE'] == 'ADMIN') {
            $postManager = new PostManager(new PDOFactory());
            $posts = $postManager->getAllPosts();
            $this->renderJson(compact('posts', 'userRole'));
        }

        //header("location: /");
        exit;
    }

}
