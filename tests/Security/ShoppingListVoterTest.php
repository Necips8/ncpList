<?php

namespace App\Tests\Security;

use App\Entity\ShoppingList;
use App\Entity\User;
use App\Security\Voter\ShoppingListVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ShoppingListVoterTest extends TestCase
{
    public function testVoterAllowsOwner(): void
    {
        $voter = new ShoppingListVoter();
        
        $owner = new User();
        $list = new ShoppingList();
        $list->setOwner($owner);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($owner);

        $this->assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($token, $list, [ShoppingListVoter::VIEW])
        );
    }

    public function testVoterDeniesNonOwner(): void
    {
        $voter = new ShoppingListVoter();
        
        $owner = new User();
        $otherUser = new User();
        
        $list = new ShoppingList();
        $list->setOwner($owner);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($otherUser);

        $this->assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, $list, [ShoppingListVoter::VIEW])
        );
    }
}
