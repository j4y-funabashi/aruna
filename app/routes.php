<?php

// ROUTES
$app->get("/", 'posts.controller:feed')
    ->bind('root');
$app->get("/p/{post_id}", 'posts.controller:getById')
    ->bind('post');

$app->get("/login", 'auth.controller:login')
    ->bind('login');
$app->get("/auth", 'auth.controller:auth')
    ->bind('auth');
$app->post('/micropub', 'micropub.controller:createPost');
$app->get('/micropub', 'micropub.controller:form');
$app->post('/webmention', 'webmention.controller:createMention');
