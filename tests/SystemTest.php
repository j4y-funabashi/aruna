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
        exec("sh ".__DIR__ . "/../resources/reset_db.sh");
        $this->http = new Client(
            array(
                "http_errors" => false
            )
        );
    }
}
