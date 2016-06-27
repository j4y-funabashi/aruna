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
        require_once __DIR__ . "/../common.php";
        exec("sh ".__DIR__ . "/../resources/reset_db.sh");
        $this->http = new Client(
            array(
                "http_errors" => false
            )
        );
    }

    protected function insertValidPost()
    {
        $db_file = getenv("ROOT_DIR")."/aruna_db.sq3";
        $db = new \PDO("sqlite:".$db_file);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        $post = array(
            "items" => array(
                array(
                    "type" => ["h-entry"]
                )
            )
        );
        $post = json_encode($post);

        $q = "REPLACE INTO posts (id, published, type, post)
            VALUES
            ('1234', '2016-01-01T01:01:01', 'note', '".$post."')
            ";
        $r = $db->query($q);
    }
}
