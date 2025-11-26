<?php

namespace App\Services;

use App\Entities\ApiToken;
use App\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\Str;
use DateTime;

class TokenService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function createToken(User $user, string $name = 'auth_token', ?DateTime $expiresAt = null): string
    {
        $plainToken = Str::random(80);

        $apiToken = new ApiToken();
        $apiToken->setUser($user);
        $apiToken->setToken(hash('sha256', $plainToken));
        $apiToken->setName($name);
        $apiToken->setExpiresAt($expiresAt);

        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();

        return $plainToken;
    }

    public function findUserByToken(string $plainToken): ?User
    {
        $hashedToken = hash('sha256', $plainToken);

        $tokenRepository = $this->entityManager->getRepository(ApiToken::class);
        $apiToken = $tokenRepository->findOneBy(['token' => $hashedToken]);

        if (!$apiToken || $apiToken->isExpired()) {
            return null;
        }

        $apiToken->setLastUsedAt(new DateTime());
        $this->entityManager->flush();

        return $apiToken->getUser();
    }

    public function revokeToken(string $plainToken): bool
    {
        $hashedToken = hash('sha256', $plainToken);

        $tokenRepository = $this->entityManager->getRepository(ApiToken::class);
        $apiToken = $tokenRepository->findOneBy(['token' => $hashedToken]);

        if (!$apiToken) {
            return false;
        }

        $this->entityManager->remove($apiToken);
        $this->entityManager->flush();

        return true;
    }

    public function revokeAllTokens(User $user): void
    {
        $tokenRepository = $this->entityManager->getRepository(ApiToken::class);
        $tokens = $tokenRepository->findBy(['user' => $user]);

        foreach ($tokens as $token) {
            $this->entityManager->remove($token);
        }

        $this->entityManager->flush();
    }
}
