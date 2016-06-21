<?php

namespace Test;

use Aruna\RenderPost;
use Aruna\PostViewModel;

/**
 * Class RenderPostTest
 * @author yourname
 */
class RenderPostTest extends UnitTest
{

    /**
     * @test
     */
    public function it_renders_a_photo()
    {
        $app = new \Silex\Application();
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../views',
        ));
        $view = $app['twig'];
        $SUT = new RenderPost($view);

        $json = '{
  "items": [
    {
      "type": [
        "h-entry"
      ],
      "properties": {
        "author": [
          {
            "type": [
              "h-card"
            ],
            "properties": {
              "photo": [
                "\/profile_pic.jpeg"
              ],
              "name": [
                "Jay Robinson"
              ],
              "url": [
                "http:\/\/j4y.co"
              ]
            },
            "value": "Jay Robinson"
          }
        ],
        "category": [
          "mates",
          "@AlexChan Funabashi"
        ],
        "photo": [
          "2016\/20160525153645_5745b87d52719.jpg"
        ],
        "url": [
          "http:\/\/j4y.co\/p\/20160525153645_5745b87d52719"
        ],
        "published": [
          "2016-04-09T14:51:28+01:00"
        ],
        "content": [
          {
            "html": "Go Ape, Newcastle",
            "value": "Go Ape, Newcastle"
          }
        ]
      }
    }
  ]
}';

        $expected = '<article class="h-entry">

    <img class="u-photo post-photo" alt="jpg photo" src="2016/20160525153645_5745b87d52719.jpg" />

        <div class="e-content post-content">
        Go Ape, Newcastle
    </div>
    
    <div class="post-meta">

    <div class="post-tags"><a class="p-category" href="/tag/mates">mates</a> <a class="p-category" href="/tag/@AlexChan Funabashi">@AlexChan Funabashi</a></div>

    <a class="u-url post-url" href="http://j4y.co/p/20160525153645_5745b87d52719">
        <time class="dt-published" datetime="2016-04-09T14:51:28+01:00">2016-04-09T14:51:28+01:00</time>
    </a>

    <div class="p-author h-card post-author">
    <img class="author-photo u-photo" src="/profile_pic.jpeg">
    <a class="author-name" href="http://j4y.co" class="p-name">Jay Robinson</a>
</div>

    <a href="https://brid.gy/publish/facebook"></a>

</div>

</article>
';

        $mf_array = json_decode($json, true);
        $post = new PostViewModel($mf_array);
        $result = $SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }


    /**
     * @test
     */
    public function it_renders_a_photo_with_person_tags()
    {
        $app = new \Silex\Application();
        $app->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../../views',
        ));
        $view = $app['twig'];
        $SUT = new RenderPost($view);

        $json = '{
  "items": [
    {
      "type": [
        "h-entry"
      ],
      "properties": {
        "category": [
          {
            "type": [
              "h-card"
            ],
            "properties": {
              "name": [
                "AlexChan Funabashi"
              ],
              "url": [
                "https:\/\/www.facebook.com\/alexandra.pinnock.35"
              ]
            },
            "value": "@https:\/\/www.facebook.com\/alexandra.pinnock.35"
          },
          "mates"
        ],
        "author": [
          {
            "type": [
              "h-card"
            ],
            "properties": {
              "photo": [
                "\/profile_pic.jpeg"
              ],
              "name": [
                "Jay Robinson"
              ],
              "url": [
                "http:\/\/j4y.co"
              ]
            },
            "value": "Jay Robinson"
          }
        ],
        "photo": [
          "2016\/20160525153645_5745b87d52719.jpg"
        ],
        "url": [
          "http:\/\/j4y.co\/p\/20160525153645_5745b87d52719"
        ],
        "published": [
          "2016-04-09T14:51:28+01:00"
        ],
        "content": [
          {
            "html": "Go Ape, Newcastle",
            "value": "Go Ape, Newcastle"
          }
        ],
        "name": [
          "jpg photo\r\n        Go Ape, Newcastle \r\n\r\n     \r\n    \r\n    \r\n\r\n        \r\n                mates\r\n                @https:\/\/www.facebook.com\/alexandra.pinnock.35\r\n             \r\n    \r\n    \r\n        2016-04-09T14:51:28+01:00\r\n\r\n    \r\n    Jay Robinson"
        ]
      }
    }
  ]
}';

        $expected = '<article class="h-entry">

    <img class="u-photo post-photo" alt="jpg photo" src="2016/20160525153645_5745b87d52719.jpg" />

        <div class="e-content post-content">
        Go Ape, Newcastle
    </div>
    
    <div class="post-meta">

    <div class="post-tags"><a class="p-category h-card" href="https://www.facebook.com/alexandra.pinnock.35">AlexChan Funabashi</a> <a class="p-category" href="/tag/mates">mates</a></div>

    <a class="u-url post-url" href="http://j4y.co/p/20160525153645_5745b87d52719">
        <time class="dt-published" datetime="2016-04-09T14:51:28+01:00">2016-04-09T14:51:28+01:00</time>
    </a>

    <div class="p-author h-card post-author">
    <img class="author-photo u-photo" src="/profile_pic.jpeg">
    <a class="author-name" href="http://j4y.co" class="p-name">Jay Robinson</a>
</div>

    <a href="https://brid.gy/publish/facebook"></a>

</div>

</article>
';

        $mf_array = json_decode($json, true);
        $post = new PostViewModel($mf_array);
        $result = $SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }
}
