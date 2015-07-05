<?php

namespace BitWasp\Bitcoin\Tests\Networking\Structure;

use BitWasp\Bitcoin\Tests\Networking\AbstractTestCase;
use BitWasp\Bitcoin\Networking\Serializer\Structure\InventoryVectorSerializer;
use BitWasp\Buffertools\Buffer;
use BitWasp\Bitcoin\Networking\Structure\InventoryVector;

class InventoryVectorTest extends AbstractTestCase
{
    public function testInventoryVector()
    {
        $buffer = Buffer::hex('4141414141414141414141414141414141414141414141414141414141414141');
        $inv = new InventoryVector(InventoryVector::ERROR, $buffer);
        $this->assertEquals(0, $inv->getType());

        $inv = new InventoryVector(InventoryVector::MSG_TX, $buffer);
        $this->assertEquals(1, $inv->getType());
        $this->assertTrue($inv->isTx());
        $this->assertFalse($inv->isBlock());
        $this->assertFalse($inv->isFilteredBlock());
        $this->assertFalse($inv->isError());

        $inv = new InventoryVector(InventoryVector::MSG_BLOCK, $buffer);
        $this->assertEquals(2, $inv->getType());
        $this->assertTrue($inv->isBlock());
        $this->assertFalse($inv->isTx());
        $this->assertFalse($inv->isError());
        $this->assertFalse($inv->isFilteredBlock());

        $inv = new InventoryVector(InventoryVector::MSG_FILTERED_BLOCK, $buffer);
        $this->assertEquals(3, $inv->getType());
        $this->assertTrue($inv->isFilteredBlock());
        $this->assertFalse($inv->isBlock());
        $this->assertFalse($inv->isTx());
        $this->assertFalse($inv->isError());

        $inv = new InventoryVector(InventoryVector::ERROR, $buffer);
        $this->assertTrue($inv->isError());

        $this->assertEquals($buffer, $inv->getHash());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidType()
    {
        new InventoryVector(9, new Buffer('4141414141414141414141414141414141414141414141414141414141414141'));
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidLength()
    {
        new InventoryVector(InventoryVector::MSG_TX, new Buffer('41414141414141414141414141414141414141414141414141414141414141'));
    }

    public function testSerializer()
    {
        $buffer = Buffer::hex('4141414141414141414141414141414141414141414141414141414141414141');
        $inv = new InventoryVector(InventoryVector::ERROR, $buffer);

        $serializer = new InventoryVectorSerializer();
        $serialized = $inv->getBuffer();

        $parsed = $serializer->parse($serialized);
        $this->assertEquals($inv, $parsed);

    }
}
