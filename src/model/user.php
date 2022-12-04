<?php

namespace App\Model\User;

require_once('src/lib/DatabaseConnection.php');

use App\Lib\Database\DatabaseConnection;
use PDO;

class User
{
    public int $id;
    public string $name;
    public string $mail;
    public string $avatar;
    public string $password;
    public string $creation_date;
    public ?string $description;
}

class UserRepository
{
    public PDO $databaseConnection;

    public function __construct() {
        $this->databaseConnection = (new DatabaseConnection())->getConnection();
    }

    public function createUser(string $name, string $mail, string $password): void {
        // check if username is valid
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            require_once('client/templates/login.php');
        } else {
            $avatar = "avatar image";

            $statement = $this->databaseConnection->prepare('INSERT INTO users (name, mail, avatar, password) VALUES (:name, :mail, :avatar, :password)');
            $statement->execute(compact('name', 'mail', 'password', 'avatar'));

            require_once('client/templates/homepage.php');
        }

    }

    public function checkUserAvailability(string $name, string $mail): bool {
        $result = $this->databaseConnection->prepare("SELECT * FROM users WHERE (name = :name OR mail = :mail)");
        $result->execute(compact('name', 'mail'));
        $count = $result->rowCount();
        if ($count == 0) {
            return true;
        }
        return false;
    }

    public function loginUser(string $ids, string $password): void {
        $result = $this->databaseConnection->prepare("SELECT * FROM users WHERE (name = :ids OR mail = :ids) AND password = :password");
        $result->execute(compact('ids', 'password'));
        $count = $result->rowCount();
        if ($count == 0) {
            throw new \RuntimeException('Account doesnt exist');
        } else {
            throw new \RuntimeException('logged in user');
        }
    }
}