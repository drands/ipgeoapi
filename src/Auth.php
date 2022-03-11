<?php

namespace App;

class Auth {
    public static function getUsers() {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        return [$_ENV['ADMIN_USER'] => $_ENV['ADMIN_PASS']];
    }
}