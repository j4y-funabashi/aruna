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
    public function awesome_function()
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
            "html": "&#xD;\n        <p>Go Ape, Newcastle<\/p>&#xD;\n&#xD;\n    ",
            "value": "Go Ape, Newcastle"
          }
        ],
        "name": [
          "jpg photo\r\n        Go Ape, Newcastle \r\n\r\n     \r\n    \r\n    \r\n\r\n        \r\n                mates\r\n                @AlexChan Funabashi\r\n             \r\n    \r\n    \r\n        2016-04-09T14:51:28+01:00\r\n\r\n    \r\n    Jay Robinson"
        ]
      }
    }
  ],
  "rels": {
    
  },
  "debug": {
    "package": "https:\/\/packagist.org\/indieweb\/php-mf2",
    "version": "v0.3.0",
    "note": [
      "This output was generated from the php-mf2 library available at https:\/\/github.com\/indieweb\/php-mf2",
      "Please file any issues with the parser at https:\/\/github.com\/indieweb\/php-mf2\/issues"
    ]
  }
}';

        $expected = '<article class="h-entry">

    <img class="u-photo post-photo" alt="jpg photo" src="2016/20160525153645_5745b87d52719.jpg" />

        <div class="e-content post-content">
        &#xD;
        <p>Go Ape, Newcastle</p>&#xD;
&#xD;
    
    </div>
    
    <div class="post-meta">

        <div class="post-tags">
                <a class="p-category" href="/tag/mates">mates</a>
                <a class="p-category" href="/tag/@AlexChan Funabashi">@AlexChan Funabashi</a>
            </div>
    
    <a class="u-url post-url" href="http://j4y.co/p/20160525153645_5745b87d52719">
        <time class="dt-published" datetime="2016-04-09T14:51:28+01:00">2016-04-09T14:51:28+01:00</time>
    </a>

    <div class="p-author h-card post-author">
    <img class="author-photo u-photo" src="/profile_pic.jpeg">
    <a class="author-name" href="http://j4y.co" class="p-name">Jay Robinson</a>
</div>
</div>


</article>
';

        $mf_array = json_decode($json, true);
        $post = new PostViewModel($mf_array);
        $result = $SUT->__invoke($post);
        $this->assertEquals($expected, $result);
    }
}
