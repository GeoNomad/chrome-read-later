<?php
// https://github.com/GeoNomad/chrome-read-later 
// Open Source Copyright benlo.com MIT License
// https://readlater.000webhostapp.com/index.php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING | E_PARSE);

touch(__FILE__);
$delete  = $_GET['delete'];
$history = $_GET['history'];
$read    = $_GET['read'];

if ( $read )
  {
  header("Location: $read");
  $list = unserialize(file_get_contents('LIST'));
  $history = unserialize(file_get_contents('HISTORY'));
  $history[$read]['id']    = $list[$read]['id'];
  $history[$read]['title'] = $list[$read]['title']; 
  unset($list[$read]);
  file_put_contents('LIST',serialize($list));
  file_put_contents('HISTORY',serialize($history));
  exit;
  }

if ( $delete )
  {
  $list = unserialize(file_get_contents('LIST'));
  $history = unserialize(file_get_contents('HISTORY'));
  $history[$delete]['id']    = $list[$delete]['id'];
  $history[$delete]['title'] = $list[$delete]['title'];
  if ( !$found ) $found = $list[$delete];
  unset($list[$delete]);
  file_put_contents('LIST',serialize($list));
  file_put_contents('HISTORY',serialize($history));
  $history = 0;
  
  if ( !$found )
    {
    $filename = 'HISTORY';
    $list = unserialize(file_get_contents($filename));
    unset($list[$delete]);
    file_put_contents($filename, serialize($list));
    $history = 1;
    }
  }

$filename = 'LIST';
if ($history) $filename = 'HISTORY';
$list = unserialize(file_get_contents($filename));

foreach($list as $url => $info)
  {
  $id    = $info['id'];
  if ( $id == 'undefined' ) $id = 0;
//  if ( !is_numeric($id)) $id = time()*1000;
  if ( $id ) $idtime = date('Y-m-d H:i',$id/1000);
  else $idtime = '2022-03-24';
  $title = $info['title'];
  if (!$title) 
    {
    $title = getTitle($url);
    $list[$url]['title'] = $title;
    $changed = true;
    }
  $urlx = urlencode($url);
  $li[]  = "<p id='$id' class='slug'>
   <img src='https://www.google.com/s2/favicons?sz=16&domain_url=$url' title='$idtime' width=16>
   <a href='?read=$url'>$title</a>
   <a class='right' href='?delete=$urlx'><img src='deleteB.png' height=16></a>
   </p>";
  }
if ( $changed ) file_put_contents($filename, serialize($list));


rsort($li);
if ( $history ) $Hn = count($li);
else $Ln = count($li);
echo "<html><head><title>Read Later</title>
<meta name='viewport' content='width=device-width, initial-scale=0.86, maximum-scale=5.0, minimum-scale=0.86'>
<link rel='stylesheet' href='main.css'>
<link rel='icon' type='image/x-icon' href='favicon.ico'>
</head><body>\n";
echo "<p class='text-center'><a href='?history=1' class='button'>HISTORY $Hn</a>
<a href='index.php' class='button'>UNREAD $Ln</a><a href='https://github.com/GeoNomad/chrome-read-later'><img src='helpblue256.png' height='30' class='right'></a>
</p>\n";

foreach($li as $line)
  {
  $id = substr($line,strpos($line,'id=')+4,13);
  $iddate = date('l Y-m-d',$id/1000);
  if ( $id < 1000000 ) $iddate = '2022-03-24';
  if ( $iddate != $donedate ) 
    {
    if ( ($history == 1) && ($id < (time()-86400*2)*1000) ) 
      {
      echo "<p class='slug text-center'><a href='?history=2'><b>Show Older History</a></b></p>\n";
      break;
      }
    echo "<p class='slug'>$iddate</p>\n";
    }
  $donedate = $iddate;
  echo "$line\n";
  $N++;
  }
if ( $N > 10 ) echo "<p class='text-center'><a href='?history=1' class='button'>HISTORY</a>
<a href='index.php' class='button'>UNREAD</a>
</p>\n";
echo "</body></html>\n";

function getTitle($url) 
  {
  $page = file_get_contents($url);
  $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : null;
  if ( !$title ) $title = substr($url,strpos($url,'//')+2,30);
  return $title;
  }

function getIcon($url)
  {
  $icon = file_get_contents("https://www.google.com/s2/favicons?sz=64&domain_url=$url");
  echo "{$icon}";
  }
?>
