<?php

namespace Aruna;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PostViewModel
 * @author yourname
 */
class PostViewModel
{
    public $h;
    public $uid;
    public $url;
    public $published;
    public $human_date;

    public function __construct(
        $post_data,
        $url_generator
    ) {
        $this->h = $post_data['h'];
        $this->uid = $post_data['uid'];
        $this->url = $url_generator->generate(
            'post',
            array('post_id' => $post_data['uid']),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->published = (new \DateTimeImmutable($post_data['published']))
            ->format('c');
        $this->human_date = (new \DateTimeImmutable($post_data['published']))
            ->format("Y-m-d");

        if (isset($post_data['files']['photo'])) {
            $this->photo = $post_data['files']['photo'];
        }
        if (isset($post_data['link_preview'])) {
            $this->link_preview = $post_data['link_preview'];
        }
    }
}
