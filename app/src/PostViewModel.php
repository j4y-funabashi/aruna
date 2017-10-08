<?php

namespace Aruna;

/**
 * Class PostViewModel
 * @author yourname
 */
class PostViewModel implements \JsonSerializable
{
    private $mf_array;
    private $entry;
    private $date_deleted;

    public function __construct(
        array $mf_array,
        $date_deleted = null
    ) {
        $this->mf_array = $mf_array;
        $this->entry = $mf_array;
        $this->date_deleted = $date_deleted;
    }

    public function jsonSerialize()
    {
        $this->mf_array['properties']['url'] = [$this->url()];
        return $this->mf_array;
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

    public function url()
    {
        return "https://j4y.co/p/".$this->get("uid");
    }

    public function published()
    {
        return $this->get("published");
    }

    public function publishedHuman()
    {
        $published = new \DateTimeImmutable($this->published());
        return $published->format("j F, Y");
    }

    public function content()
    {
        if (null === $this->get("content")) {
            return null;
        }
        $content = $this->get("content");
        if (isset($content["html"])) {
            $content = $content["html"];
        }
        return $content;
    }

    public function name()
    {
        $date = (new \DateTimeImmutable($this->get("published")))->format("Y-m-d");
        $title = (null !== $this->content())
            ? strip_tags($this->content())
            : implode(" ", $this->category());
        return sprintf(
            " %s %s",
            $date,
            $title
        );
    }

    public function category()
    {
        if (isset($this->entry['properties']['category'])) {
            return $this->entry['properties']['category'];
        }
        return array();
    }

    public function photo()
    {
        if (isset($this->entry['properties']['photo'])) {
            return $this->entry['properties']['photo'];
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

    public function authorName()
    {
        return $this->author()["name"];
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
}
