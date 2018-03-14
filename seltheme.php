<?php
  /*
    Plugin Name: SelTheme
    Plugin URI: http://yahe.sh/
    Description: Allows the user to select a theme through the "?theme=[name]" parameter.
    Version: 0.2c1
    Author: Yahe
    Author URI: http://yahe.sh/
  */

  function seltheme_gettheme() {
    $result = null; // nothing done

    // try to read parameter
    if (isset($_GET["theme"])) {
      $result = $_GET["theme"];
    } else {
      if (isset($_COOKIE["theme"])) {
        $result = $_COOKIE["theme"];
      }
    }

    if ($result != null) {
      $result = trim(basename($result));

      $realresult = trim(realpath(get_theme_root()."/".$result));
      $realroot   = trim(realpath(get_theme_root()));
	  
      if (!((stripos($realresult, $realroot) !== false) && (strlen($realresult) > strlen($realroot)))) {
        $result = null; // stop hack
      }

      if (!(file_exists(get_theme_root()."/".$result) && is_dir(get_theme_root()."/".$result))) {
        $result = null; // stop hack
      }
    }

    return $result;
  }

  // needed to set the cookie
  function seltheme_init() {
    // set cookie information
    $url = parse_url(get_bloginfo("home"));
    $domain = $url["host"];
    if (!empty($url["path"])) {
      $path = $url["path"];
    } else {
      $path = "/";
    }

    // get theme name and set cookie
    $theme = seltheme_gettheme();
    if ($theme == null) {
      setcookie("theme", "", time(), $path, $domain);
    } else {
      setcookie("theme", $theme, time()+300000, $path, $domain);
    }
  }

  // needed to set the used theme
  function seltheme_select($theme = "") {
    // get selected theme name
    $newtheme = seltheme_gettheme();
    if ($newtheme == "") {
      $newtheme = $theme;
    }

    // set theme name
    return $newtheme;
  }

  add_action("init", "seltheme_init");

  add_filter("template",          "seltheme_select");
  add_filter("stylesheet",        "seltheme_select");
  add_filter("option_template",   "seltheme_select");
  add_filter("option_stylesheet", "seltheme_select");
?>
