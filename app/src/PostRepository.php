<?php

namespace Aruna;

interface PostRepository
{
    public function listByType($post_type, $limit, $offset = 0);
    public function listByDate($year, $month, $day);
}
