<?php  
// https://github.com/GeoNomad/chrome-read-later 
// Open Source Copyright benlo.com MIT License
// https://readlater.000webhostapp.com/add.php

header("Access-Control-Allow-Origin: *");

extract(array_map(stripslashes, $_POST));
touch(__FILE__);
$dt = gmdate('Ymd His');
file_put_contents('DEBUG', "$dt ID=$id U=$url [$remove]\n", FILE_APPEND);

if ( $test )
  {
  echo "del";
  exit;
  }

if ( $remove) // from storage.mjs 24
  {
  if ( $remove == 'null') exit;
  
  $list = unserialize(file_get_contents('LIST'));
  $history = unserialize(file_get_contents('HISTORY'));
  $history[$remove]['id']    = $list[$remove]['id'];
  $history[$remove]['title'] = $list[$remove]['title']; 
  unset($list[$remove]);
  file_put_contents('LIST',serialize($list));
  file_put_contents('HISTORY',serialize($history));
  }

if ( !$url ) exit;  // $url post from storage.mjs 14
if ( $url == 'undefined' ) exit;

$list = unserialize(file_get_contents('LIST'));
if ($id) $list[$url]['id'] = $id;
else $list[$url]['id'] = 1000*time();
if ($title ) $list[$url]['title'] = $title;
else $list[$url]['title'] = getTitle($url);
file_put_contents('LIST',serialize($list));

echo "ok";


function getTitle($url) 
  {
  $page = file_get_contents($url);
  $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
  if ( !$title ) $title = substr($url,strpos($url,'//')+2,30);
  return $title;
  }
?>
