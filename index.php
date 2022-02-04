<?php require_once("_inc.php");

/* Post */
if(isset($_GET["p"])){
  header("Content-type: text/html");
  echo $h0.htmlentities($_GET["p"]).$h1;
  $i=0; foreach(glob($dir."posts/*.md") as $route){
    $post = str_replace(".md","",str_replace($dir."posts/","",$route));
    $nd = explode("_", $post);
    if(strtolower($nd[1])==strtolower($_GET["p"])){
      $ptl = explode("\n",file_get_contents($route));
      $ptl = $ptl[0];
      $ptl = preg_replace('/(#+)\s(.*)/', '', $ptl[0]);
      $t = $nd[1];
      if(isset($_POST["vote"])){
        if(!isset($_SESSION["vote_".$t]) || $_SESSION["vote_".$t]==false){
          file_put_contents($dir."votes/".$t.".txt", file_get_contents($dir."votes/".$t.".txt")+1);
          $_SESSION["vote_".$t]=true;
        }elseif(isset($_SESSION["vote_".$t]) && $_SESSION["vote_".$t]==true){
          file_put_contents($dir."votes/".$t.".txt", file_get_contents($dir."votes/".$t.".txt")-1);
          $_SESSION["vote_".$t]=false;
        }
      }
      if(!isset($_SESSION["vote_".$t]) || $_SESSION["vote_".$t]==false){
        $vote="╩ Vote  ".file_get_contents($dir."votes/".$t.".txt");
      }elseif(isset($_SESSION["vote_".$t]) && $_SESSION["vote_".$t]==true){
        $vote="╦ Unvote  ".file_get_contents($dir."votes/".$t.".txt");
      }
      echo "<span id='date' style='float:right'>".date("F d, Y",strtotime($nd[0]))."</span><hr></div><div class='notlogo'><div id='article'>".Kekdown::render(file_get_contents($route))."</div><div id='vote'><form method='POST'><input type='submit' name='vote' value='".$vote."'></form></div>";
      $i=1;
    }
  }
  if($i==0){echo "<hr></div><div class='notlogo' style='text-align:center'><h1>Error!</h1><p>Post not found</p>";}
  if($utterances){echo '<br><meta property="og:title" content="$nd[1]"><script src="https://utteranc.es/client.js" repo="$utterances" issue-term="og:title" theme="github-light" crossorigin="anonymous" async></script>';}
  echo $footer;



// RSS (coming soon)
//}elseif(isset($_GET["rss"])){
//die("Coming soon");



// Search page
}elseif(isset($_GET["s"]) && $_GET["s"]==true){
  header("Content-type: text/html");
  echo $h0."Results for '".htmlentities($_GET["s"])."' | $title".$h1."<form method='GET' id='incsrch' style='float:right;text-align:right'><!--Results for --><input name='s' type='text' value=\"".preg_replace("/\"/","&quot;",$_GET["s"])."\"></form><hr></div><div class='notlogo'><ul>";
  $count=0; $list="";
  foreach(glob($dir."posts/*.md") as $route){
    $count++;
    $list = str_replace(".md","",str_replace($dir."posts/","",$route)) . "\n" . $list;
  }
  $list = explode("\n",$list);
  unset($list[count($list)-1]);
  $i=0; foreach($list as $route){
    $nd = explode("_", $route);
    if(
      strpos($nd[0], $_GET["s"]) !== false ||
      strpos(strtolower($nd[1]), strtolower($_GET["s"])) !== false ||
      strpos(strtolower(file_get_contents($dir."posts/".$route.".md")), strtolower($_GET["s"])) !== false
    ){
      echo "<li><a href='?p={$nd[1]}'><b>$nd[0]</b> - <i>$nd[1]</i></a></li>"; $i=1;
    }
  }
  if($i==0){echo "<p>No posts found</p>";}
  echo "</ul>".$footer;



// About Liteldom page
}elseif(isset($_GET["liteldom"])){
  header("Content-type: text/html");
  echo $h0."About Liteldom".$h1."<span style='float:right'>About Liteldom</span><hr></div><div class='notlogo' style='text-align:center'><h1>Liteldom</h1><p>v1.0.0-beta1</p><p>&copy;2021-2022 <a href='https://lucas.koyu.space'>Lucas Tjor</a></p><p><b>Using the Kekdown Markdown parser</b><br>Forked from <a href='https://gist.github.com/jbroadway/2836900'>Slimdown</a><br>&copy;2012-2014 <a href='https://github.com/jbroadway'>Jhonny Broadway</a><br>&copy;2022 <a href='https://lucas.koyu.space'>Lucas Tjor</a></p><p><a href='https://github.com/luqaska/liteldom'>Source code</a></p><p><b></b></p>";
  echo $footer;



/* Home */
}else{
  header("Content-type: text/html");
  if(file_exists("admin.php")){
    $admin = "<a href='admin.php'>Admin</a>";
  }else{
    $admin = "";
  }
  if(filesize($dir."nav.md") != 0){
    $nav = file_get_contents($dir."nav.md");
    $nav = preg_replace("/\n/", "", $nav);
    $nav = implode(" - ", explode("\r", Kekdown::render($nav)));
    $nav = "<div>".$nav."</div>";
  }else{
    $nav = "";
  }
  if($description!=False){$description="<p>$description</p>";}
  echo $h0."$title</title><style>$style</style>$custom_css</head><body id='home'><div style='text-align:left'><a id='logo' href='?'>$title</a><span style='float:right;text-align:right'>$admin</span></div>$nav$description<!--<a href='?rss'>RSS Feed</a><br>--><div class='notlogo'><div id='search' class='form'><form><input type='text' name='s' placeholder=\"You don't find it? Search it here!\"><input type='submit' value='Search'></form></div><ul>";
  $count=0; $list="";
  foreach(glob($dir."posts/*.md") as $route){
    $count++;
    $list = str_replace(".md","",str_replace($dir."posts/","",$route)) . "\n" . $list;
  }
  if($count != 0){
    $list = explode("\n",$list);
    unset($list[count($list)-1]);
    foreach($list as $route){
      $nd = explode("_",$route);
      echo "<li><a href='?p=$nd[1]'><b>$nd[0]</b> - <i>$nd[1]</i></a></li>";
    }
  }else{
    echo "<p>There are no posts on this blog :/</p>";
  }
  echo "</ul>".$footer;
} ?>