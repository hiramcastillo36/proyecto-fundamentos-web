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

}


?>
