<?php

// ROUTES
$app->get("/", 'posts.controller:feed')
    ->bind('post_feed');
$app->get("/p/{post_id}", 'posts.controller:getById')
    ->bind('post');
$app->post('/micropub', 'micropub.controller:createPost');
