<?php

namespace Aruna;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PostViewModel
 * @author yourname
 */
class PostViewModel
{
    private $mf_array;
    private $entry;

    public function __construct(array $mf_array) {
        $this->mf_array = $mf_array;
        $this->entry = $this->findFirstEntry($mf_array);
    }

    private function findFirstEntry($mf_array)
    {
        if (!isset($mf_array['items'])) {
            throw new \Exception("mf array does not contain items");
        }
        $entries = array_values(
            array_filter(
                $mf_array['items'],
                function ($item) {
                    return (isset($item['type']) && is_array($item['type']) && in_array("h-entry", $item['type']));
                }
        )
        );
        if (isset($entries[0])) {
            return $entries[0];
        } else {
            throw new \Exception("mf array does not contain an entry");
        }
    }

    public function get($param)
    {
        if (isset($this->entry['properties'][$param])) {
            return $this->entry['properties'][$param];
        }
    }

    public function type()
    {
        if (null !== ($this->get('photo'))) {
            return "photo";
        }
        if (null !== ($this->get('like-of'))) {
            return "like";
        }
        if (null !== ($this->get('bookmark-of'))) {
            return "bookmark";
        }
        return "note";
    }
}
