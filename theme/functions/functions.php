<?php 
function root_data ($component_name) {
  include get_template_directory() . "/theme/filters/components/{$component_name}.php";
  return apply_filters("root_component_{$component_name}", []);
}
