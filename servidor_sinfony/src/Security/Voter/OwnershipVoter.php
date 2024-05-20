<?php
// src/Security/Voter/OwnershipVoter.php

namespace App\src\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Ejercicio;
use App\Entity\Partida;

class OwnershipVoter extends Voter
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function supports(string $attribute, $subject): bool
    {
        $supports = in_array($attribute, ['EDIT', 'DELETE']) && ($subject instanceof Ejercicio || $subject instanceof Partida);
        $this->logger->info("Voter supports: " . json_encode($supports));
        return $supports;
    }
    
    
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // Asegurarse de que el usuario es una instancia de UserInterface
        if (!$user instanceof UserInterface) {
            return false;
        }

        $result = $subject->getAutor() === $user->getUserIdentifier();
        $this->logger->info(sprintf("Voting on attribute: User %s, Author %s, Result: %s",
            $user->getUserIdentifier(), $subject->getAutor(), json_encode($result)));

        return $result;
    }
}


