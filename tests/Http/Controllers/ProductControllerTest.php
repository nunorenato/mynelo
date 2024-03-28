<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\ProductController;
use PHPUnit\Framework\TestCase;

class ProductControllerTest extends TestCase
{

    public function testGetWithSync()
    {
        $pc = new ProductController();
        $p = $pc->getWithSync(42404);
        self::assertNotNull($p);
    }
}
