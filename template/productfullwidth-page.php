<?php
// this is full width template 
get_header();

  // Start the loop.
  while ( have_posts() ) : the_post();

    the_content();

  endwhile;


get_footer(); ?>
