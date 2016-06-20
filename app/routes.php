<?php

// ROUTES
$app->get("/", 'action.show.photos:__invoke')
    ->bind('root');

$app->get("/p/{post_id}", 'action.show_post:__invoke')
    ->bind('post');

$app->get("/photos", "action.show.photos:__invoke")
    ->bind("photos");

$app->get("/login", 'auth.controller:login')
    ->bind('login');

$app->get("/auth", 'auth.controller:auth')
    ->bind('auth');

$app->post('/micropub', 'action.create_post:__invoke');
$app->get('/micropub', 'action.show_micropub_form:__invoke');

$app->post('/webmention', 'webmention.controller:createMention');
$app->get('/webmention/{mention_id}', 'webmention.controller:view')
    ->bind("webmention");

$app->get("/{year}/{month}/{day}", 'action.show_date_feed:__invoke')
    ->value('month', '*')
    ->value('day', '*')
    ->bind('date_feed');
