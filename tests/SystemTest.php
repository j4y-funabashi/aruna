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
        $this->http = new Client(
            array(
                'base_uri' => 'http://127.0.0.1',
                'timeout'  => 2.0
            )
        );
    }
}
