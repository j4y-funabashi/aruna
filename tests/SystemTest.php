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
        $this->app = \Aruna\App::build();
        $db = $this->app["db_cache"];
        $db->init();
        $this->base_url = "http://aruna_webserver";
    }
}
