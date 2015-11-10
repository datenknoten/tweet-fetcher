<?php
/* ----------------------------------------------------------------------------
 * "THE VODKA-WARE LICENSE" (Revision 42):
 * <tim@datenkonten.me> wrote this file.  As long as you retain this notice you
 * can do whatever you want with this stuff. If we meet some day, and you think
 * this stuff is worth it, you can buy me a vodka in return.     Tim Schumacher
 * ----------------------------------------------------------------------------
 */

require_once "vendor/autoload.php";

require_once "config.php";

$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name=' . $username . '&count='.$limit;
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$result = $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

$result = json_decode($result);

$twig = new Twig_Environment(new \Twig_Loader_String());


if (is_array($result) && (count($result) > 0)) {
    $url = "https://api.twitter.com/1.1/statuses/oembed.json";
    foreach($result as $tweet) {
        $tweet_path = $dir . '/' . $tweet->id_str;
        if (file_exists($tweet_path)) {
            continue;
        }

        $getfield = '?align=center&id='.$tweet->id_str;
        $result_code = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
        $result_code = json_decode($result_code);
        $html = $result_code->html;
        $html = str_replace('<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>','',$html);
        $html = preg_replace_callback('|https?://t.co/([a-zA-Z0-9]+)|',function($matches){
            $url = $matches[0];
            $client = new GuzzleHttp\Client();
            //$res = $client->request('HEAD', $url,['debug' => true]);
            $res = $client->head($url,['allow_redirects' => false]);
            $url = $res->getHeader('location');
            return $url[0];
        },$html);
        $data = [];
        $data['html'] = $html;
        if (property_exists($tweet,"retweeted_status")) {
            $data['title'] = $tweet->retweeted_status->text;
        } else {
            $data['title'] = $tweet->text;
        }
        $data['title'] = preg_replace_callback('|https?://t.co/([a-zA-Z0-9]+)|',function($matches){
            $url = $matches[0];
            $client = new GuzzleHttp\Client();
            //$res = $client->request('HEAD', $url,['debug' => true]);
            $res = $client->head($url,['allow_redirects' => false]);
            $url = $res->getHeader('location');
            return $url[0];
        },$data['title']);
        $data['date'] = $tweet->created_at;
        $md = $twig->render($template,['tweet' => $data]);

        mkdir($tweet_path);
        file_put_contents($tweet_path . '/tweet.md',$md);
        var_dump("Wrote file for tweet ".$tweet->id_str);
    }
}
