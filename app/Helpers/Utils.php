<?php

namespace App\Helpers;

class Utils
{
    public static function gerarCodigoLiberacao(): string
    {
        do {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= random_int(0, 9);
            }
        } while (preg_match('/(\d)\1{2,}/', $codigo)); // evita repetições

        return $codigo;
    }

    public static function getTokenData(array $requestAuth): array
    {
        return [
            'email' => $requestAuth['email'] ?? null,
            'expo_push_token' => $requestAuth['expo_push_token'] ?? null,
        ];
    }
}
