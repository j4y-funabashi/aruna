<?php

namespace Test;

use GuzzleHttp\Client;

/**
 * Class SystemTest
 * @author John Doe
 */
class SystemTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->app = require __DIR__ . "/../app/app.php";
    }
}
