<?php

require_once 'conexion.php';

class CAD {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    public function insertNewsletter($email) {
        try {
            $sql = "INSERT INTO newsletter (email) VALUES (:email)";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->execute();
            $result = true;
            return $result;
        } catch (Exception $e) {
            $result = false;
            return $result;
        }
    }

    public function signIn ($email, $password) {
        try {
            $stmt = $this->conexion->conectar()->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $result = true;
            } else {
                $result = false;
            }
            return $result;
        } catch (Exception $e) {
            $result = false;
            return $result;
        }
    }

    public function signUp ($email, $password) {
        try {
            $stmt = $this->conexion->conectar()->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $result = false;
                throw new Exception('User already exists');
            }

            $password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, password, role) VALUES (:email, :password, 'user')";
            $stmt = $this->conexion->conectar()->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $result = true;
            return $result;

        } catch (Exception $e) {
            $result = false;
            return $result;
        }
    }

    public function getNewsletterSubscribers($offset = 0, $limit = 10) {
        $query = "SELECT * FROM newsletter ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $subscribers;
    }

    public function getTotalSubscribers() {
        $query = "SELECT COUNT(*) as total FROM newsletter";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->execute();
        $total = $stmt->fetch(PDO::FETCH_ASSOC);
        return $total['total'];
    }

    public function createBlogPost($title, $body, $author_id, $read_time, $description, $image){
        # create image record in images table
        $query = "INSERT INTO images (filename) VALUES (:url)";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':url', $image);
        $stmt->execute();

        # get image id
        $query = "SELECT id FROM images WHERE filename = :url";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':url', $image);
        $stmt->execute();
        $image_id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

        # create post record in posts table
        $query = "INSERT INTO posts (title, body, author_id, read_time, description, image_id) VALUES (:title, :body, :author_id, :read_time, :description, :image_id)";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':read_time', $read_time);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->execute();
        $result = true;
        return $result;
    }

    public function getUser($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getAllPosts($offset = 0, $limit = 6) {
        $query = "SELECT p.*, i.filename as image FROM posts p JOIN images i ON p.image_id = i.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->bindParam(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $posts;
    }

    public function getPost($id) {
        $query = "SELECT posts.*, images.filename as image_url, users.email as author_name
                 FROM posts
                 LEFT JOIN images ON posts.image_id = images.id
                 LEFT JOIN users ON posts.author_id = users.id
                 WHERE posts.id = :id";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        return $post;
    }

    public function getUserById($id){
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function createComment($post_id, $user_id, $text){
        $query = "INSERT INTO comments (post_id, user_id, text) VALUES (:post_id, :user_id, :text)";
        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':text', $text);
        $stmt->execute();
        $result = true;
        return $result;
    }

    public function getCommentsByPostId($post_id){
        $query = "SELECT c.*, u.email as author_name
                 FROM comments c
                 LEFT JOIN users u ON c.user_id = u.id
                 WHERE c.post_id = :post_id
                 ORDER BY c.created_at DESC";

        $stmt = $this->conexion->conectar()->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

    public function deletePost($post_id) {
        try {
            $stmt = $this->conexion->conectar()->prepare("DELETE FROM posts WHERE id = :id");
            $stmt->bindParam(':id', $post_id);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {

            echo $e;
            return false;
        }
    }

    public function getPosts($offset = 0, $limit = 10) {
        try {
            $stmt = $this->conexion->conectar()->prepare("
                    SELECT posts.*, images.filename as image_url, users.email as author_name
                    FROM posts
                    LEFT JOIN images ON posts.image_id = images.id
                    LEFT JOIN users ON posts.author_id = users.id
                    ORDER BY posts.created_at DESC LIMIT ? OFFSET ?
                 ");
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting posts: " . $e->getMessage());
            return [];
        }
    }

    public function getTotalPosts() {
        try {
            $stmt = $this->conexion->conectar()->query("SELECT COUNT(*) FROM posts");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total posts count: " . $e->getMessage());
            return 0;
        }
    }

    public function addLike($postId, $userId) {
        try {
            $stmt = $this->conexion->conectar()->prepare("INSERT INTO votes (post_id, user_id) VALUES (:post_id, :user_id)");
            $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Si es un error de duplicado (código 23000)
            if ($e->getCode() == '23000') {
                return false;
            }
            throw $e;
        }
    }

    public function removeLike($postId, $userId) {
        $stmt = $this->conexion->conectar()->prepare("DELETE FROM votes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getLikesCount($postId) {
        $stmt = $this->conexion->conectar()->prepare("SELECT COUNT(*) as count FROM votes WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function hasUserLiked($postId, $userId) {
        $stmt = $this->conexion->conectar()->prepare("SELECT 1 FROM votes WHERE post_id = :post_id AND user_id = :user_id");
        $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getUserWithNewsletter($id) {
        $stmt = $this->conexion->conectar()->prepare("SELECT 1 FROM newsletter WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

}


?>
