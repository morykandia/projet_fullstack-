<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static string $jwtKey = "mlgkfhjsk,v46456reqk,ùopekzqjnvorenq15o^hnreq";

    public static function buildJWT(User $user): string
    {
        $payload = [
            "email" => $user->getEmail(),
            "exp" => (new DateTime("+20 minutes"))->getTimestamp()
        ];

        return JWT::encode($payload, self::$jwtKey, "HS256");
    }

    public static function verifyJwt(string $jwt): ?object
    {
        try {
            return JWT::decode($jwt, new Key(self::$jwtKey, "HS256"));
        } catch (Exception $e) {
            return null;
        }
    }
}