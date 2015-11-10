<?php
$settings = array(
'oauth_access_token' => "",
'oauth_access_token_secret' => "",
'consumer_key' => "",
'consumer_secret' => ""
);

$username = '';
$limit = 100;

$template = <<<EOT
    ---
    title: {{ tweet.title|replace({"\n" : ""})|raw }}
date: {{ tweet.date }}
taxonomy:
category: blog
---
{{ tweet.html|raw }}
EOT;

$dir = "";

