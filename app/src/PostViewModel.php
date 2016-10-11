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

    public function __construct(
        array $mf_array
    ) {
        $this->mf_array = $mf_array;
        $this->entry = $this->findFirstEntry($mf_array);
    }

    public function toJson()
    {
        return json_encode($this->mf_array);
    }

    public function toString()
    {
        return json_encode($this->mf_array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getEntry()
    {
        return $this->entry;
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

    public function setCategory($category)
    {
        $this->entry['properties']['category'] = $category;
        $this->mf_array["items"] = [$this->entry];
    }

    public function setLike($like)
    {
        $like = array(
            "type" => "h-cite",
            "properties" => [
                "published" => [$like->published()],
                    "url" => [$like->get("url")],
                        "author" => array(
                            [
                                "type" => ["h-card"],
                                "properties" => $like->author()
                            ]
                        )
                    ]
                );
        $this->entry['properties']['like'][] = $like;
        $this->mf_array["items"] = [$this->entry];
    }

    public function setComment($comment)
    {
        $comment = array(
            "type" => "h-cite",
            "properties" => [
                "published" => [$comment->published()],
                    "url" => [$comment->get("url")],
                    "content" => array(
                            [
                                "value" => $comment->get("content")["value"],
                                "html" => $comment->get("content")["html"]
                            ]
                        ),
                        "author" => array(
                            [
                                "type" => ["h-card"],
                                "properties" => $comment->author()
                            ]
                        )
                    ]
                );
        $this->entry['properties']['comment'][] = $comment;
        $this->mf_array["items"] = [$this->entry];
    }

    public function comments()
    {
        if (isset($this->entry['properties']['comment'])) {
            return $this->entry['properties']['comment'];
        }
        return [];
    }

    public function likes()
    {
        if (isset($this->entry['properties']['like'])) {
            return $this->entry['properties']['like'];
        }
        return [];
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
}
