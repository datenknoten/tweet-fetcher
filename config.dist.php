<?php
// get a app from https://apps.twitter.com/ and auth with it
$settings = array(
    'oauth_access_token' => "",
    'oauth_access_token_secret' => "",
    'consumer_key' => "",
    'consumer_secret' => ""
);

// the username to fetch the tweets from
$username = '';

// the number of tweets to fetch
$limit = 100;

// the twig template
$template = <<<EOT
---
title: {{ tweet.title|replace({"\n" : ""})|raw }}
date: {{ tweet.date }}
taxonomy:
    category: blog
---
{{ tweet.html|raw }}
EOT;

// the directory to save the tweets to
$dir = "";

