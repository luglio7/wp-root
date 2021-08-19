<?php
$config = [
  "disable_admin_bar_css" => false,
  "disable_blocks_css" => true,
  "disable_comments" => true,
  "disable_emoji" => true,
  "disable_head_tags" => true,
  "disable_posts" => false,
  "disable_feeds" => true
];

// theme setup
add_action("after_setup_theme", function() {
  add_theme_support("post-thumbnails");
  add_theme_support("title-tag");
  // add_theme_support("align-wide");
  // add_theme_support("custom-logo");

  load_theme_textdomain("root", get_stylesheet_directory() . "/languages");

  add_image_size("wide", 1920, 1920, false);

  register_nav_menu("nav-menu", __("Nav menu", "root"));
});

// styles
add_action("wp_enqueue_scripts", function() {
  $css_url = get_stylesheet_directory_uri() . "/assets/css/style.css";
  wp_enqueue_style("root_style", $css_url, [], ROOT_IS_DEVELOPMENT);
});

// scripts
add_action("wp_enqueue_scripts", function() {
  $js_url = get_stylesheet_directory_uri() . "/assets/js/index.js";
  if (ROOT_IS_DEVELOPMENT) {
    $js_url = "http://127.0.0.1:8000/index.js";
  }
  wp_enqueue_script("root_main", $js_url, [], ROOT_IS_DEVELOPMENT, true);
});

// disable block styles
if ($config["disable_blocks_css"]) {
  add_action("wp_print_styles", function() {
    wp_dequeue_style("wp-block-library");
  }, 100);
}

// disable admin bar css
if ($config["disable_admin_bar_css"]) {
  add_action("get_header", function() {
    remove_action("wp_head", "_admin_bar_bump_cb");
  });
}

// disable head tags
if ($config["disable_head_tags"]) {
  remove_action("wp_head", "wp_generator");
  remove_action("wp_head", "rsd_link");
  remove_action("wp_head", "wlwmanifest_link");
  remove_action("wp_head", "wp_shortlink_wp_head");
  remove_action("wp_head", "rest_output_link_wp_head", 10);
  remove_action("wp_head", "wp_oembed_add_discovery_links", 10);
  remove_action("template_redirect", "rest_output_link_header", 11, 0);
}

// disable emoji
if ($config["disable_emoji"]) {
  remove_action("admin_print_styles", "print_emoji_styles");
  remove_action("wp_head", "print_emoji_detection_script", 7);
  remove_action("admin_print_scripts", "print_emoji_detection_script");
  remove_action("wp_print_styles", "print_emoji_styles");
  remove_filter("wp_mail", "wp_staticize_emoji_for_email");
  remove_filter("the_content_feed", "wp_staticize_emoji");
  remove_filter("comment_text_rss", "wp_staticize_emoji");

  add_filter("emoji_svg_url", "__return_false");
  add_filter("tiny_mce_plugins", function ($plugins) {
      if (is_array ($plugins)) {
          return array_diff ($plugins, array("wpemoji"));
      } else {
          return array();
      }
  });
}

// disable posts
if ($config["disable_posts"]) {
  add_action("admin_menu", function () {
    remove_menu_page("edit.php");
  });

  add_action("admin_bar_menu", function($wp_admin_bar) {
      $wp_admin_bar->remove_node("new-post");
  }, 999);

  add_action("wp_dashboard_setup", function(){
      remove_meta_box("dashboard_quick_press", "dashboard", "side");
  }, 999);
}

// disable comments
if ($config["disable_comments"]) {
  // remove admin menu
  add_action("admin_menu", function () {
    remove_menu_page("edit-comments.php");
  }, 99);

  // remove admin bar link
  add_action("wp_before_admin_bar_render", function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu("comments");
  }, 99);

  // remove comments support for registered post types
  add_action("init", function() {
    foreach (get_post_types() as $_post_type) {
      if (post_type_supports($_post_type, "comments")) {
        remove_post_type_support($_post_type, "comments");
        remove_post_type_support($_post_type, "trackbacks");
      }
    }
  }, 99);

  // remove meta box
  add_action("admin_init", function() {
    foreach (get_post_types() as $_post_type) {
      remove_meta_box("commentsdiv", $_post_type, "normal");
      remove_meta_box("commentstatusdiv", $_post_type, "normal");
    }
  }, 99);

  // remove discussion option page
  add_action("admin_menu", function() {
    remove_submenu_page("options-general.php", "options-discussion.php");
  });

  // close comments and pings
  add_filter("comments_open", "__return_false", 99);
  add_filter("pings_open", "__return_false", 99);

  // remove comments columns from posts and pages
  add_filter("manage_post_posts_columns", function ($columns) {
    unset($columns["comments"]);
    return $columns;
  });
  
  add_filter("manage_page_posts_columns", function ($columns) {
    unset($columns["comments"]);
    return $columns;
  });
}

// disable feeds
if ($config["disable_feeds"]) {
  remove_action("wp_head", "feed_links", 2);
  remove_action("wp_head", "feed_links_extra", 3);
  add_action("do_feed", function () { die(); }, 1);
  add_action("do_feed_rdf", function () { die(); }, 1);
  add_action("do_feed_rss", function () { die(); }, 1);
  add_action("do_feed_rss2", function () { die(); }, 1);
  add_action("do_feed_atom", function () { die(); }, 1);
  add_action("do_feed_rss2_comments", function () { die(); }, 1);
  add_action("do_feed_atom_comments", function () { die(); }, 1);
}
