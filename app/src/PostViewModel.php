<?php

namespace Aruna;

/**
 * Class PostViewModel
 * @author yourname
 */
class PostViewModel
{
    private $mf_array;
    private $entry;
    private $date_deleted;

    public function __construct(
        array $mf_array,
        $date_deleted = null
    ) {
        $this->mf_array = $mf_array;
        $this->entry = $this->findFirstEntry($mf_array);
        $this->date_deleted = $date_deleted;
    }

    public function toJson()
    {
        return json_encode($this->mf_array);
    }

    public function isDeleted()
    {
        return $this->date_deleted;
    }

    public function toString()
    {
        return json_encode($this->mf_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function get($param)
    {
        if (isset($this->entry['properties'][$param][0])) {
            return $this->entry['properties'][$param][0];
        }
        return null;
    }

    public function published()
    {
        if (null !== $this->get("published")) {
            return $this->get("published");
        }
        return date("c");
    }

    public function category()
    {
        if (isset($this->entry['properties']['category'])) {
            return $this->entry['properties']['category'];
        }
        return array();
    }

    public function author()
    {
        if (isset($this->entry['properties']['author'][0]['properties'])) {
            $out = array(
                "name" => $this->entry['properties']['author'][0]['properties']['name'][0],
                "photo" => $this->entry['properties']['author'][0]['properties']['photo'][0],
                "url" => $this->entry['properties']['author'][0]['properties']['url'][0]
            );
            return $out;
        }
        return array();
    }

    public function type()
    {
        if (null !== ($this->date_deleted)) {
            return "tombstone";
        }
        if ("delete" == ($this->get('action'))) {
            return "delete";
        }
        if (null !== ($this->get('photo'))) {
            return "photo";
        }
        if (null !== ($this->get('like-of'))) {
            return "like";
        }
        if (null !== ($this->get('bookmark-of'))) {
            return "bookmark";
        }
        if (null !== ($this->get('in-reply-to'))) {
            return "reply";
        }
        return "note";
    }

    public function deleted()
    {
        return $this->date_deleted;
    }

    private function findFirstEntry($mf_array)
    {
        return $mf_array;
    }
}
