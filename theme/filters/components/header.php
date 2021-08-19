<?php 
add_filter("root_component_header", function () {
  return [
    "test" => "ok!"
  ];
});
