<?php

namespace App\Tests\Entity;

use App\Entity\ShoppingList;
use App\Entity\User;
use App\Entity\ListItem;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class ShoppingListTest extends TestCase
{
    public function testShoppingListCreation(): void
    {
        $user = new User();
        $user->setName('testuser');

        $list = new ShoppingList();
        $list->setName('Test List');
        $list->setOwner($user);

        $this->assertSame('Test List', $list->getName());
        $this->assertSame($user, $list->getOwner());
        $this->assertInstanceOf(\DateTimeImmutable::class, $list->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $list->getUpdatedAt());
        $this->assertCount(0, $list->getItems());
    }

    public function testAddItemToList(): void
    {
        $list = new ShoppingList();
        $item = new ListItem();
        $item->setName('Brot');
        $item->setAmount(2.0);

        $list->addItem($item);

        $this->assertCount(1, $list->getItems());
        $this->assertSame($list, $item->getList());
        $this->assertSame('Brot', $list->getItems()[0]->getName());
    }

    public function testRemoveItemFromList(): void
    {
        $list = new ShoppingList();
        $item = new ListItem();
        $item->setName('Brot');

        $list->addItem($item);
        $this->assertCount(1, $list->getItems());

        $list->removeItem($item);
        $this->assertCount(0, $list->getItems());
        $this->assertNull($item->getList());
    }
}
