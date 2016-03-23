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

$app->post('/micropub', 'action.create_post:__invoke');

$app->get('/micropub', 'micropub.controller:form');

$app->post('/webmention', 'webmention.controller:createMention');
$app->get('/webmention/{mention_id}', 'webmention.controller:view')
    ->bind("webmention");
