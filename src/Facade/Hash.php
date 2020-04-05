<?php

namespace Kentron\Facade;

final class Hash
{
    /**
     * Generate the hash on an optional password
     * @param  integer $length    The length of the output
     * @param  string  $algorithm The algorithm to use
     * @return string             The hash
     * @throws InvalidArgumentException On invalid algorithm
     */
    public static function generateRandom (int $length = 64, string $algorithm = "sha256"): string
    {
        if (!in_array($algorithm, hash_algos())) {
            throw new \InvalidArgumentException("'$algorithm' is not a valid algorithm");
        }

        return hash_pbkdf2(
            $algorithm,
            random_bytes(16),
            random_bytes(16),
            10000,
            $length
        );
    }
}
