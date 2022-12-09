<?php

namespace App\Manager;


use App\Entity\Post;



class PostManager extends BaseManager
{
    /**
     * @return Post[]
     */
    public function getAllPosts(): array
    {
        $query = $this->pdo->query("select * from Post");

        $users = [];

        while ($data = $query->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = new Post($data);
        }

        return $users;
    }

    public function getPostById(int $id): ?Post
    {

        $query = $this->pdo->prepare("SELECT * FROM Post WHERE Post.id = :id");
        $query->bindValue('id', $id, \PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetch(\PDO::FETCH_ASSOC);

        $post = new Post($data);


        if ($post) {
            return $post;
        }
        return null;
    }

    public function insertPost(Post $post)
    {
        $query = $this->pdo->prepare('INSERT INTO Post (title,content,author_id,created_at,img) VALUES (:title,:content,:author_id,:created_at,:img)');
        $query->bindValue('content', $post->getContent(), \PDO::PARAM_STR);
        $query->bindValue('author_id', $post->getAuthor(), \PDO::PARAM_INT);
        $query->execute();
    }


    public function deletePost(int $id)
    {
        $query = $this->pdo->prepare('DELETE FROM Post WHERE id = :id');
        $query->bindValue('id', $id, \PDO::PARAM_INT);
        $query->execute();
    }


    public function updatePost(int $id, array $data)
    {
        extract($data);
        $query = $this->pdo->prepare('UPDATE Post SET content = :content,img = :img,title=:title WHERE id = :id');
        $query->bindValue('id', $id, \PDO::PARAM_INT);
        $query->bindValue('content', $content, \PDO::PARAM_STR);
        $query->bindValue('title', $title, \PDO::PARAM_STR);
        $query->bindValue('img', $img, \PDO::PARAM_STR);
        $query->execute();
    }
}
