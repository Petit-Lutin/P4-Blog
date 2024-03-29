<?php

// Chargement des classes
require_once("model/DbConnect.php");
require_once('model/PostManager.php');
require_once('model/CommentManager.php');
require_once('model/UserManager.php');


class FrontOfficeController
{
    public function __construct()
    { //si on voulait ajouter des choses propres à la classe
    }

    public function listPosts($page)
    {
        $postManager = new PostManager(); // Création d'un objet
        $posts = $postManager->getPosts($page);
        // Appel d'une fonction de cet objet avec page en paramètre

        require('view/frontend/listPostsView.php');
        $posts->closeCursor();
    }

    public function post()
    {
        $postManager = new PostManager();
        $commentManager = new CommentManager();

        $post = $postManager->getPost($_GET['id'], $_GET['slug']);
        $slug = $post['slug'];
        if ($_GET['slug'] <> $slug) {
            require('view/frontend/404.php');
        } else {
            $title = htmlspecialchars($post['title']);
            $comments = $commentManager->getComments($_GET['id'], $_SERVER['REMOTE_ADDR']);
            require('view/frontend/postView.php');
        }


    }

    public function addComment($postId, $author, $comment)
    {
        $commentManager = new CommentManager();
        $affectedLines = $commentManager->postComment($postId, $author, $comment);

        $postManager = new PostManager();
        $post = $postManager->getPost($_GET['id']);
        $slug = $post['slug'];

        if ($affectedLines === false) {
            throw new Exception('Impossible d\'ajouter le commentaire !');
        } else {
            header('Location: ' . $postId . '/' . $slug);
        }
    }

    public function reportComment($commentId, $REMOTE_ADDR)
    {
        $commentManager = new CommentManager();
        $postId = $commentManager->reportComment($commentId, $REMOTE_ADDR);

        $postManager = new PostManager();
        $post = $postManager->getPost($postId);
        $slug = $post['slug'];
        if ($postId === false) {
            throw new Exception('Impossible de signaler le commentaire !');
        }

        header('Location: ' . $postId . '/' . $slug);
    }
}
