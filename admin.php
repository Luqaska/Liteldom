<?php require_once("_inc.php");








// Admin panel username and password
$username	=	$_ENV["user"];
$password	=	$_ENV["pass"];








function jsdel($strng){
  return preg_replace("/\t/","",preg_replace("/\n/","\\n",preg_replace("/\"/","&quot;",$strng)));
}
$header = $h0."Admin".$h1."<span style='float:right'>";
$login_form = 'Login to access</span><hr></div><div class=\'notlogo\'><style>#login input{width:40%;text-align:center;padding:0.5% 0;}</style><form method="POST" action="admin.php" id="login" style="text-align:center"><h1>Login to access</h1><p>to access to the administration panel</p><input type="text" name="user" placeholder="Username"><br><input type="password" name="pass" placeholder="Password"><br><input type="submit" name="send" value="Login"></form>';

// Check if you are logged in
if(isset($_SESSION["user"])){
  $user = $_SESSION["user"];
  // Dashboard
  if(isset($_GET["dash"])){
    echo $header."Welcome ".$_SESSION["user"]."!</span><hr></div><div class='notlogo'><div id='route'>Dashboard</div><br><ul><li><a href='?general'>General</a></li><li><a href='?nav'>Nav</a></li><li><a href='?posts'>Posts</a></li><li><a href='?styles'>Styles</a></li><!--<li><a href='?analytics'>Analytics</a></li>--><li><a href='?logout'>Log out</a></li></ul>".$footer;
  // General settings
  }elseif(isset($_GET["general"])){
    // Update settings
    if(isset($_POST["send"])){
      if(!isset($_POST["title"]) || !$_POST["title"]){
        die("The title field should have text");
      }else{
        file_put_contents($dir."config.json",
          "{\n\t\"title\":\"".jsdel($_POST["title"])."\",\n\t\"description\":\"".jsdel($_POST["description"])."\",\n\t\"utterances\":\"".jsdel($_POST["utterances"])."\",\n\t\"lang\":\"".jsdel($_POST["lang"])."\",\n\t\"timezone\":\"".jsdel($_POST["timezone"])."\",\n\t\"footer\":\"".jsdel($_POST["footer"])."\"\n}"
        );
        header("location: ?general");
      }
    }
    // Update general settings update form
    echo $header."General</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > General</div><br><style>input[type=text]{width:100%;}input[type=submit]{background:transparent;border:none;padding:0;font-family:'Anonymous Pro', monospace;font-size:medium;text-decoration:underline;color:#0066cc;cursor:pointer;}input[type=submit]:active{color:#ea1919;}textarea{width:100%;height:200px;}</style><form method='POST'><b>Title:</b><span style='color:red' title='Required'>*</span><br><input type='text' name='title' value=\"".$title."\" required><br><br><b>Description:</b><br><input type='text' name='description' value=\"".$description."\"><br><br><b>Utterances:</b><br><input type='text' name='utterances' value=\"".$utterances."\"><br><br><b>lang (HTML):</b><br><input type='text' name='lang' value=\"".$config->{"lang"}."\"><br><br><b>Timezone:</b><br><input type='text' name='timezone' value=\"".$config->{"timezone"}."\"><br><br><b>Footer:</b><br><input type='text' name='footer' value=\"".$copy."\"><br><ul><li><input type='submit' name='send' value='Update'></li></ul></form>".$footer;
  }elseif(isset($_GET["nav"])){
    // Update navbar file
    if(isset($_POST["send"])){
      file_put_contents($dir."nav.md", $_POST["content"]);
      header("location: ?nav");
    }
    // Show navbar update form
    echo $header."Nav</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > Nav</div><br><style>input[type=text]{width:100%;}input[type=submit]{background:transparent;border:none;padding:0;font-family:'Anonymous Pro', monospace;font-size:medium;text-decoration:underline;color:#0066cc;cursor:pointer;}input[type=submit]:active{color:#ea1919;}textarea{width:100%;height:200px;}</style><form method='POST'>Keep in mind that 1 line = 1 item!<br><textarea type='text' placeholder='(Note, you can use HTML code and Markdown here)' name='content'>".htmlentities(file_get_contents($dir."nav.md"))."</textarea><br><ul><li><input type='submit' name='send' value='Save'></li></ul></form>".$footer;
  }elseif(isset($_GET["posts"])){
    if(!$_GET["posts"]){
      // Check if searching
      if(isset($_POST["s"]) && $_POST["s"]==true){
        $s=true; $sr=" value='".preg_replace("/\"/","&quot;",$_POST["s"])."'";
      }else{$s=false; $sr="";}
      echo $header."Posts</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > Posts</div><br><style>input[type=text]{width:100%;}</style><form method='POST'><input name='s' type='text' placeholder='Filter'".$sr."></form><p><a href='?posts=new'>New post</a></p><ul>";
      if($s){
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
            strpos($nd[0], $_POST["s"]) !== false ||
            strpos(strtolower($nd[1]), strtolower($_POST["s"])) !== false ||
            strpos(strtolower(file_get_contents($dir."posts/".$route.".md")), strtolower($_POST["s"])) !== false
          ){
            echo "<li><a href='?posts={$nd[1]}'><b>$nd[0]</b> - <i>$nd[1]</i></a><span style='float:right'><a href='?posts={$nd[1]}&del'>Delete</a></span></li>"; $i=1;
          }
        }
        if($i==0){echo "<p>No posts found</p>";}
      }else{
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
            echo "<li><a href='?posts=$nd[1]'><b>$nd[0]</b> - <i>$nd[1]</i></a><span style='float:right'><a href='?posts={$nd[1]}&del'>Delete</a></span></li>";
          }
        }else{
          echo "<p>There are no posts on this blog :/</p>";
        }
      }
      echo "</ul>".$footer;

    }elseif(strtolower($_GET["posts"])=="new"){
      if(isset($_POST["send"])){
        $i=0; foreach(glob($dir."posts/*.md") as $route){
          $post = str_replace(".md","",str_replace($dir."posts/","",$route));
          $nd = explode("_", $post);
          if(strtolower($nd[1])==strtolower($_POST["id"])){
            $i=1;
          }
        }
        if($i==0){
          $nPostC = fopen($dir."posts/".date("Y-m-d")."_".$_POST["id"].".md", "w");
          fwrite($nPostC, $_POST["content"]);
          fclose($nPostC);
          $nPostC = fopen($dir."votes/".$_POST["id"].".txt", "w");
          fwrite($nPostC, "0");
          fclose($nPostC);
          header("location: ?posts=".$_POST["id"]."&posted");
        }else{
          echo "Error. Permalink/ID already exists";
        }
      }else{
        echo $header."Create post</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > <a href='?posts'>Posts</a> > Create post</div><br><style>input[type=text]{width:100%;}input[type=submit]{background:transparent;border:none;padding:0;font-family:'Anonymous Pro', monospace;font-size:medium;text-decoration:underline;color:#0066cc;cursor:pointer;}input[type=submit]:active{color:#ea1919;}textarea{width:100%;height:200px;}</style><form method='POST'><b>Permalink/ID:</b><br><input type='text' name='id' required><br>Your permalink must be unique, this cannot be changed, and all of them are the ways the system identifies your posts.<br><br><b>Content:</b><br><textarea type='text' name='content' placeholder='(Note, you can use HTML code and Markdown here)' required></textarea><br><ul><li><input type='submit' name='send' value='Create'></li></ul></form>".$footer;
      }
    }else{
      $i=0; foreach(glob($dir."posts/*.md") as $route){
        $post = str_replace(".md","",str_replace($dir."posts/","",$route));
        $nd = explode("_", $post);
        if(strtolower($nd[1])==strtolower($_GET["posts"])){
          if(isset($_GET["del"])){
            if(isset($_POST["confirm"])){
              unlink($route);
              unlink($dir."votes/".$_GET["posts"].".txt");
              header("location: ?posts");
            }else{
              echo $header."Confirmation</span><hr></div><div class='notlogo'><style>input[type=submit],button{width:100%;padding:10px 0;font-size:medium;margin:10px 0;}</style><div id='route'><a href='?dash'>Dashboard</a> > <a href='?posts'>Posts</a> > Delete post</div><br><form method='POST'><h1>Confirm</h1><p>Are you sure do you want to delete this post?<br><b>This action cannot be undone!!!</b></p><input type='submit' name='confirm' value='Yes'><a href='?posts'><button>No</button></a>".$footer;
            }
          }elseif(isset($_POST["send"])){
            file_put_contents($route, $_POST["content"]);
          }else{
            echo $header."Edit post</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > <a href='?posts'>Posts</a> > Edit post</div><br><style>input[type=text]{width:100%;}input[type=submit]{background:transparent;border:none;padding:0;font-family:'Anonymous Pro', monospace;font-size:medium;text-decoration:underline;color:#0066cc;cursor:pointer;}input[type=submit]:active{color:#ea1919;}textarea{width:100%;height:200px;}</style><form method='POST'><b>Content:</b><br><textarea type='text' name='content' placeholder='(Note, you can use HTML code and Markdown here)' required>".htmlentities(file_get_contents($route))."</textarea><br><ul><li><input type='submit' name='send' value='Save'></li></ul></form>".$footer;
          }
        }
      }
      if($i==0){
        echo "Not found";
      }
    }
  }elseif(isset($_GET["styles"])){
    // Update settings
    if(isset($_POST["send"])){
      file_put_contents("custom.css", $_POST["content"]);
      header("location: ?styles");
    }
    // Update form
    echo $header."Styles</span><hr></div><div class='notlogo'><div id='route'><a href='?dash'>Dashboard</a> > Styles</div><br><style>input[type=text]{width:100%;}input[type=submit]{background:transparent;border:none;padding:0;font-family:'Anonymous Pro', monospace;font-size:medium;text-decoration:underline;color:#0066cc;cursor:pointer;}input[type=submit]:active{color:#ea1919;}textarea{width:100%;height:200px;}</style><form method='POST'><textarea type='text' name='content'>".htmlentities(file_get_contents("custom.css"))."</textarea><br><ul><li><input type='submit' name='send' value='Save'></li></ul></form>".$footer;
  }elseif(isset($_GET["analytics"])){
    echo $header."Analytics (7 days)</span><hr></div><div class='notlogo'>";
    echo $footer;
  }elseif(isset($_GET["logout"])){
    session_destroy();
    header("location: ?logout");
  }else{
    header("location: ?dash");
  }
}elseif(isset($_POST["send"])){
  $user = $_POST["user"];
  $pass = $_POST["pass"];
  if(password_verify($user, $username) && password_verify($pass, $password)){
    $_SESSION["user"] = $user;
    header("location: ?dash");
  }else{
    echo $header.$login_form.'<p style="text-align:center"><span style="background:#d62222;color:white">Incorrect username and/or password<br>Please try again later</span></p>'.$footer;
  }
}else{
  if(isset($_GET["logout"]) && !isset($_SESSION["user"])){
    $logout='<p style="text-align:center"><span style="background:green;color:white">You\'ve been logged out successfully</span></p>';
  }else{$logout="";}
  echo $header.$login_form.$logout.$footer;
} ?>