<?php

namespace Aruna\Pipeline;

/**
 * Class ConvertMarkdown
 * @author yourname
 */
class ConvertMarkdown
{

    public function __construct(
        $log,
        $parser
    ) {
        $this->log = $log;
        $this->parser = $parser;
    }

    public function __invoke($event)
    {

        if (isset($event['content'])) {
            $event['content'] = $this->parser->parse($event['content']);
        }

        return $event;
    }
}
