<?php
/*
Plugin Name: Product Cat
Plugin URI: http://www.weborigin.org
Description:  This is a Ccompanion Plugin for Weborigin Theme
Author: weborigin
Version: 1.0
Author URI: http://weborigin.org/
*/
/**
 *
 */
class ProductCat
{

  public function __construct(){
    $this->include();
    $this->hooking();
    $this->make_shortcode();

  }
  public function include(){
    require_once('vendor/autoload.php');
    require_once('functions/helping.php');
  }
  public function hooking(){
    add_filter( 'theme_page_templates', [$this,'fullWidthPage'] );
    add_filter( 'template_include', [$this,'templeateLocation']  );
    add_action( 'wp_enqueue_scripts', [$this,'stylesheet'] );
    add_filter( 'woocommerce_locate_template', [$this,'procat_woocommerce_locate_template'] , 10, 3 );

  }
  public function fullWidthPage($page_templates){
        $post_templates['productfullwidth-page.php'] = __('ProductCat Fullwidth');
        return $post_templates;
  }
  function templeateLocation( $template ) {
      if(  get_page_template_slug() === 'productfullwidth-page.php' ) {
        $template = Config::directory('template/productfullwidth-page.php');
      }
      return $template;
  }

  public function stylesheet() {
    wp_enqueue_style( 'productcat-stylesheet', Config::url('assets/mainstylesheet.css'));
  }

  function make_shortcode() {
    add_shortcode('all_categories', array($this,'product_categories'));
  }

  function procat_plugin_path() {
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
  }

  function procat_woocommerce_locate_template( $template, $template_name, $template_path ) {
    global $woocommerce;
    $_template = $template;
    if ( ! $template_path ){
      $template_path = $woocommerce->template_url;
    }
    $plugin_path  = $this->procat_plugin_path() . '/woocommerce/';
    // Look within passed path within the theme - this is priority
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
      )
    );
    // Modification: Get the template from this plugin, if it exists
    if ( ! $template && file_exists( $plugin_path . $template_name ) ){
        $template = $plugin_path . $template_name;
    }
    // Use default template
    if ( ! $template ){
      $template = $_template;
    }
    // Return what we found
    return $template;
  }


	public function product_categories( $atts ) {

  		$atts = extract(shortcode_atts( array(
  			'number'     => null,
  			'orderby'    => 'name',
  			'order'      => 'ASC',
  			'columns'    => '4',
  			'hide_empty' => 1,
  			'parent'     => '',
  			'ids'        => ''
  		), $atts ));
  		// get terms and workaround WP bug with parents/pad counts
  		$args = array(
  			'orderby'    => $orderby,
  			'order'      => $order,
  			'hide_empty' => $hide_empty,
  			'include'    => $ids,
  			'pad_counts' => true,
  			'child_of'   => $parent
  		);
  		$product_categories = get_terms( 'product_cat', $args );
  		ob_start();
  		if ( $product_categories ) {
    			foreach ( $product_categories as $category ) {
                // if ($category->count > 0) { ?>
                <div class="items-wrap">
                    <div class="cat-item">
                        <a target="_blank" class="items-href" href="<?php echo get_term_link( $category->term_id ); ?>">
                          <?php
                            $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
                            // get the image URL
                            $image = wp_get_attachment_url( $thumbnail_id );
                            // print the IMG HTML
                            echo "<img class='catimage' src='{$image}' alt='' />";
                          ?>
                      		<h3 class="cat-title"> <?php echo $category->name; ?> </h3>
                      	</a>
                    </div>
                </div>
          <?php     // }
        }
  		}
  		return '<div class="category-container" > <div class="woocom-category columns-' . $columns. '">' . ob_get_clean() . '</div></div>';
  }


}

new ProductCat();


/**
 * Add "Custom" template to page attirbute template section.
 */
// function wpse_288589_add_template_to_select( $page_templates ) {
//
//     // Add custom template named template-custom.php to select dropdown
//     $post_templates['template-custom.php'] = __('Custom');
//
//     return $post_templates;
// }
//
// add_filter( 'theme_page_templates', 'wpse_288589_add_template_to_select', 10, 4 );
