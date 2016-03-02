<?php

// ROUTES
$app->get("/", 'posts.controller:feed')
    ->bind('post_feed');
$app->get("/p/{post_id}", 'posts.controller:getById')
    ->bind('post');

$app->get("/login", 'auth.controller:login')
    ->bind('login');
$app->get("/auth", 'auth.controller:login')
    ->bind('auth');
$app->post('/micropub', 'micropub.controller:createPost');
