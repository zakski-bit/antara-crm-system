<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $query = 'SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1';
        $statement = Database::connection()->prepare($query);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $record = $statement->fetch(PDO::FETCH_ASSOC);

        return $record ?: null;
    }
}
