<?php

namespace Aruna\Pipeline;

/**
 * Class ParseCategories
 * @author yourname
 */
class ParseCategories
{

    public function __invoke($event)
    {

        if (false === isset($event['category'])) {
            return $event;
        }

        $out = (is_array($event['category']))
            ? $event['category']
            : explode(",", $event['category']);

        $out = array_filter($out, 'strlen');
        if (empty($out)) {
            return $event;
        }
        $out = array_map('strtolower', $out);
        $out = array_map('trim', $out);
        $out = array_unique(
            array_filter($out)
        );

        $event['category'] = $out;
        return $event;
    }
}
