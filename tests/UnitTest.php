<?php

namespace Test;

/**
 * Class UnitTest
 * @author John Doe
 */
class UnitTest extends \PHPUnit_Framework_TestCase
{

    protected function loadJsonFixture($file)
    {
        return file_get_contents(__DIR__."/fixtures/".$file.".json");
    }
}
