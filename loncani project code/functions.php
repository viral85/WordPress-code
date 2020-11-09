<?php 
add_action( 'wp_enqueue_scripts', 'listeo_enqueue_styles' );

function listeo_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css',array('bootstrap','listeo-icons','listeo-woocommerce') );

    // wp_enqueue_style( 'responsive-style', get_stylesheet_directory_uri() . '/responsive.css' );
}

function remove_parent_theme_features() {}

add_action( 'after_setup_theme', 'remove_parent_theme_features', 10 );

add_action('pre_get_posts','shop_filter_cat');

function shop_filter_cat($query) {
  if (!is_admin() && is_post_type_archive( 'product' ) && $query->is_main_query()) {
    $query->set('tax_query', array(
      array ('taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms' => array( 'listeo-booking' ),
        'operator' => 'NOT IN'
      )
    )
  );   
  }
}

/*---------------php function file in paste (svg img not upload )-----------*/
function add_svg_to_upload_mimes( $upload_mimes ) {
 $upload_mimes['svg'] = 'image/svg+xml';
 $upload_mimes['svgz'] = 'image/svg+xml';
 return $upload_mimes;
}
add_filter( 'upload_mimes', 'add_svg_to_upload_mimes', 10, 1 );



add_filter('wp_nav_menu_items','sk_wcmenucart', 10, 2);
function sk_wcmenucart($menu, $args) {

  // Check if WooCommerce is active and add a new item to a menu assigned to Primary Navigation Menu location
  if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'primary' !== $args->theme_location )
    return $menu;

  ob_start();
  global $woocommerce;
  $viewing_cart = __('View your shopping cart', 'your-theme-slug');
  $start_shopping = __('Start shopping', 'your-theme-slug');
  $cart_url = $woocommerce->cart->get_cart_url();
  $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
  $cart_contents_count = $woocommerce->cart->cart_contents_count;
  $cart_contents = sprintf(_n('%d', '%d', $cart_contents_count, 'your-theme-slug'), $cart_contents_count);
  $cart_total = $woocommerce->cart->get_cart_total();
    // Uncomment the line below to hide nav menu cart item when there are no items in the cart
    // if ( $cart_contents_count > 0 ) {
  if ($cart_contents_count == 0) {
    $menu_item = '<li class="right"><a class="wcmenucart-contents" href="'. $shop_page_url .'" title="'. $start_shopping .'">';
  } else {
    $menu_item = '<li class="right"><a class="wcmenucart-contents" href="'. $cart_url .'" title="'. $viewing_cart .'">';
  }

  $menu_item .= '<i class="lc-bag-icon"></i> ';

  $menu_item .= '<span>'.$cart_contents.'</span>';
  $menu_item .= '</a></li>';
    // Uncomment the line below to hide nav menu cart item when there are no items in the cart
    // }
  echo $menu_item;
  $social = ob_get_clean();
  return $menu . $social;

}

/**
 * Change number or products per row to 3
 */
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
  function loop_columns() {
    return 4; // 4 products per row
  }
}

function move_price(){
  ?>
  <script>
    jQuery(window).load(function(){
      jQuery( '.single-product' ).each(function() {
        jQuery( this ).find( '.product.type-product .summary .price' ).insertBefore( jQuery(this).find('.cart .quantity') );
      });
    });
  </script>
  <?php
}
add_action('wp_footer', 'move_price');

add_filter( 'woocommerce_output_related_products_args', 'bbloomer_change_number_related_products', 9999 );
 
function bbloomer_change_number_related_products( $args ) {
 $args['posts_per_page'] = 10; // # of related products
 $args['columns'] = 4; // # of columns per row
 return $args;
}

add_filter( 'script_loader_src', 'remove_script_version_parameter' );

function remove_script_version_parameter( $src )
{
    return remove_query_arg( 'ver', $src );
}

add_query_arg( 'post_type', 'product', 'http://loncani.ca/dashboard-2/products/' );

//remove_action( 'woocommerce_after_my_account', array( Dokan_Pro::init(), 'dokan_account_migration_button' ) ); 


add_action('edit_user_profile_update', 'update_extra_profile_fields');
add_action('personal_options_update', 'update_extra_profile_fields');
add_action( 'user_register', 'update_extra_profile_fields');

function update_extra_profile_fields($user_id) {
   $user_meta=get_userdata($user_id);
   $user_roles=$user_meta->roles;
   
   if (in_array("owner", $user_roles))
    {

       $u = new WP_User( $user_id );
       $u->add_role( 'seller' );
       update_user_meta( $user_id, 'dokan_enable_selling', 'yes');
    }
 /* else
    {
        array_push($user_roles,"seller");
        update_post_meta( $user_id, 'wp_capabilities', $user_roles );
    }*/

   
}
/**
 * Redirect non-admin users to home page
 * This function is attached to the 'admin_init' action hook.
 */
add_action( 'admin_init', 'redirect_non_admin_users' );
function redirect_non_admin_users() {
    $user_meta=wp_get_current_user();
    $user_roles=$user_meta->roles;
    if( !current_user_can( 'administrator' ) && !defined('DOING_AJAX')  ) {
        if (in_array("supporter", $user_roles)){}else{
            wp_redirect( home_url() );
            exit;
        }
    }
}
add_filter('stop_gwp_live_feed', '__return_true');

/*Login to dashboard*/
function admin_default_page() {
    return home_url().'/dashboard/';
}
add_filter('login_redirect', 'admin_default_page');

/**********Redirect to dashboard/add listing as per user role on register *************/
function admin_default_page_register() {
  if (!empty($_GET['registered'])) {
    if($_GET['registered']){
        $user = wp_get_current_user();
        $roles = $user->roles;
        if(in_array("owner",$roles)){
            /** check if user logged first time **/
            $track_user_login_status = get_user_meta($user->ID,"track_user_login_status", true);
            if($track_user_login_status){
                $useurl = home_url().'/dashboard/';
                wp_redirect($useurl);
                exit;
            }else{
                $useurl = home_url().'/signup-flow'; 
                update_user_meta($user->ID,"track_user_login_status", 1);
                wp_redirect($useurl);
                exit;
            }
        }else{
            $useurl = home_url().'/dashboard/';
            wp_redirect($useurl);
            exit;
        }
    }
  }
}
add_filter('wp_head', 'admin_default_page_register');

/**********  Add pagination for autor  *******************/

if ( !function_exists( 'author_pagination' ) ) {
  
  function author_pagination() {
    
    //$prev_arrow = is_rtl() ? '→' : '←';
    //$next_arrow = is_rtl() ? '←' : '→';
    
    global $wp_query;
    $total = $wp_query->max_num_pages;
    $big = 999999999; // need an unlikely integer
    if( $total > 1 )  {
       if( !$current_page = get_query_var('paged') )
         $current_page = 1;
       if( get_option('permalink_structure') ) {
         $format = 'page/%#%/';
       } else {
         $format = '&paged=%#%';
       }
      echo paginate_links(array(
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => $format,
        'current'   => max( 1, get_query_var('paged') ),
        'total'     => $total,
        'mid_size'    => 3,
        'type'      => 'list',
        'prev_text'   => '<i class="sl sl-icon-arrow-left"></i>',
        'next_text'   => '<i class="sl sl-icon-arrow-right"></i>',
       ) );
    }
  }
  
}
add_filter('show_admin_bar', '__return_false');
/*Animated section shortcode for IG page Long*/
add_shortcode("animated_section_long","animated_section_module_long");
function animated_section_module_long(){
    $custom_args = array(
    'post_type' => 'animation',
    'posts_per_page' => 1,
    'tax_query' => array(
              array(
              'taxonomy' => 'Animation_type',
              'field' => 'long',
              'terms' => 329
              )
        )
  );
  $custom_query = new WP_Query( $custom_args ); 
  while ( $custom_query->have_posts() ){
      $custom_query->the_post();
      global $post;
      $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
      $second_featured_image_meta = get_post_meta( $post->ID, 'second_featured_image', true);
      $second_featured_image_url = wp_get_attachment_image_url( $second_featured_image_meta, 'full');
      $third_featured_image_meta = get_post_meta( $post->ID, 'third_featured_image', true);
      $third_featured_image_url = wp_get_attachment_image_url( $third_featured_image_meta, 'full');
      $fourth_featured_image_meta = get_post_meta( $post->ID, 'fourth_featured_image', true);
      $fourth_featured_image_url = wp_get_attachment_image_url( $fourth_featured_image_meta, 'full');

  }
    $animated_section_long = '<div class="device">
   <img src="'.get_stylesheet_directory_uri().'/animationimg/mockup.png" width="320" id="deviceimg">
   <div class="fadeinshowcase socshowcase">
      <img src="'.$feat_image.'" alt="'.$second_featured_image_meta.'">
   </div>
</div>
<div class="fadein2 soc" style="z-index:10;">
   <img src="'.$second_featured_image_url.'">
   <img src="'.$third_featured_image_url.'">
   <img src="'.$fourth_featured_image_url.'">
</div>
<div class="clearfix"></div>';
return($animated_section_long);
}
/*Animated sections shortcode for IG page Small*/
add_shortcode("animated_section_small","animated_section_module_small");
function animated_section_module_small(){
    $custom_args = array(
    'post_type' => 'animation',
    'posts_per_page' => 1,
    'tax_query' => array(
              array(
              'taxonomy' => 'Animation_type',
              'field' => 'small',
              'terms' => 330
              )
        )
  );
  $custom_query = new WP_Query( $custom_args ); 
  while ( $custom_query->have_posts() ){
      $custom_query->the_post();
      global $post;
      $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
      $second_featured_image_meta = get_post_meta( $post->ID, 'second_featured_image', true);
      $second_featured_image_url = wp_get_attachment_image_url( $second_featured_image_meta, 'full');
      $third_featured_image_meta = get_post_meta( $post->ID, 'third_featured_image', true);
      $third_featured_image_url = wp_get_attachment_image_url( $third_featured_image_meta, 'full');
      $fourth_featured_image_meta = get_post_meta( $post->ID, 'fourth_featured_image', true);
      $fourth_featured_image_url = wp_get_attachment_image_url( $fourth_featured_image_meta, 'full');

  }
    $animated_section_small = '<div class="device">
   <img src="'.get_stylesheet_directory_uri().'/animationimg/mockup.png" width="320" id="deviceimg">
   <div class="fadeinshowcase socshowcase">
      <img src="'.$feat_image.'" alt="Micro-landing page of real estate agent created with ContactInBio">
   </div>
</div>
<div class="fadein23microlanding soc" style="z-index:10;">
   <span>
   <strong>Visitors</strong><img src="'.$second_featured_image_url.'" style="vertical-align:middle;padding:5px;" width="80">
   </span>
   <span>
   <strong>Clicks</strong><img src="'.$third_featured_image_url.'" style="vertical-align:middle;padding:5px;" width="68">
   </span>
   <span>
   <strong>Sales</strong><img src="'.$fourth_featured_image_url.'" style="vertical-align:middle;padding:5px;" width="68">
   </span>
</div>
<div class="clearfix"></div>';
return($animated_section_small);
}
/*Custom Animated post type to support Animated Shortcodes*/
add_action( 'init', 'register_cpt_Animation' );
function register_cpt_Animation() {
    $labels = array( 
        'name' => _x( 'All Animation', 'Animation' ),
        'singular_name' => _x( 'All Animation', 'Animation' ),
        'add_new' => _x( 'Add New Animation', 'Animation' ),
        'add_new_item' => _x( 'Add New Animation', 'Animation' ),
        'edit_item' => _x( 'Edit Animation', 'Animation' ),
        'new_item' => _x( 'New Animation', 'Animation' ),
        'view_item' => _x( 'View Animation', 'Animation' ),
        'search_items' => _x( 'Search Animation', 'Animation' ),
        'not_found' => _x( 'No Animation found', 'Animation' ),
        'not_found_in_trash' => _x( 'No Animation found in Trash', 'Animation' ),
        'parent_item_colon' => _x( 'Parent Animation:', 'Animation' ),
        'menu_name' => _x( 'All Animation', 'Animation' ),
    );
    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array( 'category', 'post_tag', 'page-category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    'taxonomies' => array('Animation_type')
    );
    register_post_type( 'Animation', $args );
}
function add_Animation_type_taxonomies() {
  register_taxonomy('Animation_type', 'review', array(
    'hierarchical' => true,
    'labels' => array(
      'name' => _x( 'Animation Category', 'taxonomy general name' ),
      'singular_name' => _x( 'Animation-Category', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Animation-Categories' ),
      'all_items' => __( 'All Animation-Categories' ),
      'parent_item' => __( 'Parent Animation-Category' ),
      'parent_item_colon' => __( 'Parent Animation-Category:' ),
      'edit_item' => __( 'Edit Animation-Category' ),
      'update_item' => __( 'Update Animation-Category' ),
      'add_new_item' => __( 'Add New Animation-Category' ),
      'new_item_name' => __( 'New Animation-Category Name' ),
      'menu_name' => __( 'Categories' ),
    ),
    'rewrite' => array(
      'slug' => 'Animation_type',
      'with_front' => false,
      'hierarchical' => true
    ),
  ));
}
add_action( 'init', 'add_Animation_type_taxonomies', 0 );
/*Multiple Images For Animation Post Type*/
add_action( 'after_setup_theme', 'custom_postimage_setup' );
function custom_postimage_setup(){
    add_action( 'add_meta_boxes', 'custom_postimage_meta_box' );
    add_action( 'save_post', 'custom_postimage_meta_box_save' );
}
function custom_postimage_meta_box(){
    $post_types = array('animation');
    foreach($post_types as $pt){
        add_meta_box('custom_postimage_meta_box',__( 'Moving images', 'yourdomain'),'custom_postimage_meta_box_func',$pt,'side','low');
    }
}
function custom_postimage_meta_box_func($post){
    $meta_keys = array('second_featured_image','third_featured_image','fourth_featured_image');
    foreach($meta_keys as $meta_key){
        $image_meta_val=get_post_meta( $post->ID, $meta_key, true);
        ?>
        <div class="custom_postimage_wrapper" id="<?php echo $meta_key; ?>_wrapper" style="margin-bottom:20px;">
            <img src="<?php echo ($image_meta_val!=''?wp_get_attachment_image_src( $image_meta_val)[0]:''); ?>" style="width:100%;display: <?php echo ($image_meta_val!=''?'block':'none'); ?>" alt="">
            <a class="addimage button" onclick="custom_postimage_add_image('<?php echo $meta_key; ?>');"><?php _e('add image','yourdomain'); ?></a><br>
            <a class="removeimage" style="color:#a00;cursor:pointer;display: <?php echo ($image_meta_val!=''?'block':'none'); ?>" onclick="custom_postimage_remove_image('<?php echo $meta_key; ?>');"><?php _e('remove image','yourdomain'); ?></a>
            <input type="hidden" name="<?php echo $meta_key; ?>" id="<?php echo $meta_key; ?>" value="<?php echo $image_meta_val; ?>" />
        </div>
    <?php } ?>
    <script>
    function custom_postimage_add_image(key){
        var $wrapper = jQuery('#'+key+'_wrapper');
        custom_postimage_uploader = wp.media.frames.file_frame = wp.media({
            title: '<?php _e('select image','yourdomain'); ?>',
            button: {
                text: '<?php _e('select image','yourdomain'); ?>'
            },
            multiple: false
        });
        custom_postimage_uploader.on('select', function() {
            var attachment = custom_postimage_uploader.state().get('selection').first().toJSON();
            var img_url = attachment['url'];
            var img_id = attachment['id'];
            $wrapper.find('input#'+key).val(img_id);
            $wrapper.find('img').attr('src',img_url);
            $wrapper.find('img').show();
            $wrapper.find('a.removeimage').show();
        });
        custom_postimage_uploader.on('open', function(){
            var selection = custom_postimage_uploader.state().get('selection');
            var selected = $wrapper.find('input#'+key).val();
            if(selected){
                selection.add(wp.media.attachment(selected));
            }
        });
        custom_postimage_uploader.open();
        return false;
    }
    function custom_postimage_remove_image(key){
        var $wrapper = jQuery('#'+key+'_wrapper');
        $wrapper.find('input#'+key).val('');
        $wrapper.find('img').hide();
        $wrapper.find('a.removeimage').hide();
        return false;
    }
    </script>
    <?php
    wp_nonce_field( 'custom_postimage_meta_box', 'custom_postimage_meta_box_nonce' );
}
function custom_postimage_meta_box_save($post_id){
    if ( ! current_user_can( 'edit_posts', $post_id ) ){ return 'not permitted'; }
    if (isset( $_POST['custom_postimage_meta_box_nonce'] ) && wp_verify_nonce($_POST['custom_postimage_meta_box_nonce'],'custom_postimage_meta_box' )){
        $meta_keys = array('second_featured_image','third_featured_image','fourth_featured_image');
        foreach($meta_keys as $meta_key){
            if(isset($_POST[$meta_key]) && intval($_POST[$meta_key])!=''){
                update_post_meta( $post_id, $meta_key, intval($_POST[$meta_key]));
            }else{
                update_post_meta( $post_id, $meta_key, '');
            }
        }
    }
}

/**
 * Prevent update notification for plugin
 */
function disable_plugin_updates( $value ) {
  if ( isset($value) && is_object($value) ) {
    if ( isset( $value->response['qty-increment-buttons-for-woocommerce/qty-increment-buttons-for-woocommerce.php'] ) ) {
      unset( $value->response['qty-increment-buttons-for-woocommerce/qty-increment-buttons-for-woocommerce.php'] );
    }
  }
  return $value;
}
add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );
/* Shop product category menu shortcode */
add_shortcode("product_cat_shop","product_cat_shop_fun2");
function product_cat_shop_fun2(){
    $product_cat_shop = '<ul class="mcd-menu">';
    
    $taxonomy     = 'product_cat';
      $orderby      = 'name';  
      $show_count   = 0;
      $pad_counts   = 0;
      $hierarchical = 1;
      $title        = '';  
      $empty        = 1;
    
      $args = array(
             'taxonomy'     => $taxonomy,
             'orderby'      => $orderby,
             'show_count'   => $show_count,
             'pad_counts'   => $pad_counts,
             'hierarchical' => $hierarchical,
             'title_li'     => $title,
             'hide_empty'   => $empty
      );
     $all_categories = get_categories( $args );
     foreach ($all_categories as $cat) {
        $product_cat_shop .= '<li>';
        if($cat->category_parent == 0) {
            $category_id = $cat->term_id;
      if($category_id != 328) {
        $product_cat_shop .= '<a href="'. get_term_link($cat->slug, 'product_cat') .'"><strong>'. $cat->name .'</strong></a>';
        $args2 = array(
            'taxonomy'     => $taxonomy,
            'child_of'     => 0,
            'parent'       => $category_id,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );
        $sub_cats = get_categories( $args2 );
        if($sub_cats) {
          $product_cat_shop .= '<ul>';
          foreach($sub_cats as $sub_category) {
            $product_cat_shop .= '<li>';
            $category_id = $sub_category->term_id;
            $product_cat_shop .= '<a href="'. get_term_link($sub_category->slug, 'product_cat') .'"><strong>'.$sub_category->name.'</strong></a>';
            $args2 = array(
                'taxonomy'     => $taxonomy,
                'child_of'     => 0,
                'parent'       => $category_id,
                'orderby'      => $orderby,
                'show_count'   => $show_count,
                'pad_counts'   => $pad_counts,
                'hierarchical' => $hierarchical,
                'title_li'     => $title,
                'hide_empty'   => $empty
            );
            $sub_cats = get_categories( $args2 );
            if($sub_cats) {
              $product_cat_shop .= '<ul>';
              foreach($sub_cats as $sub_category) {
                $product_cat_shop .= '<li>';
                $product_cat_shop .= '<a href="'. get_term_link($sub_category->slug, 'product_cat') .'"><strong>'.$sub_category->name.'</strong></a>';
                $product_cat_shop .= '</li>';
              }
              $product_cat_shop .= '</ul>';
            }
          }
          $product_cat_shop .= '</li>';
          $product_cat_shop .= '</ul>';
        }
      }
        }
        $product_cat_shop .= '</li>';
    }
    wp_reset_query();
  $product_cat_shop .= '</ul>';
    return($product_cat_shop);
}
/*** Add CSS/JS files only on shop for custom carousel products ***/
function scripts_only_shop() {
  if ( is_shop() ) {
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/owlcarousel/owl.carousel.min.css', array(), '1.1', 'all');
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/owlcarousel/owl.theme.default.min.css', array(), '1.1', 'all');
    wp_enqueue_script( 'script', get_stylesheet_directory_uri() . '/owlcarousel/owl.carousel.min.js', array ( 'jquery' ), 1.1, true);
    }
}
/******** Control for shop carousel *********/
add_action("wp_footer","shop_carousel_control");
function shop_carousel_control() {
    if ( is_shop() ) {
    ?><script>
        jQuery(document).ready(function() {
      if(jQuery(window).width() >= 1200){
        var ttlresp = 4;
      }else if(jQuery(window).width() >= 800){
        var ttlresp = 3;
      }else if(jQuery(window).width() >= 600){
        var ttlresp = 2;
      }else{
        var ttlresp = 1;
      }
          var owl = jQuery('.owl-carousel');
          owl.owlCarousel({
          items: ttlresp,
          loop:true,
          margin: 10,
          nav: true,
          });
        })
    </script><?php
    echo '<style>
        .owl-carousel .owl-item {
            min-height: auto;
        }
        .owl-carousel .owl-item li {
            list-style: none;
        }
        .owl-nav {
            position: absolute;
            right: 0;
            top: -52px;
            background: transparent;
        }
        .owl-nav button {
            box-shadow: 0 0;
        }
        .owl-nav button span {
            font-size: 25px;
            background: transparent;
            line-height: 0px;
        }
        .owl-nav button {
            box-shadow: 0 0;
            border: 1px solid #b87a29 !important;
            border-radius: 5px;
            height: 30px;
            width: 30px;
        }
        .tab_Prod {
            padding-top: 52px;
        }
        .shop_heading.bef::before {
            border: 1px solid #b87a29;
            top: 6.3rem;
            content: " ";
            position: absolute;
            width: 20%;
        }
        .shop_heading.bef {
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        .custom_tb .vc_tta-panels .vc_tta-panel .vc_tta-panel-body {
            margin-top: -60px;
        }
        .custom_tb ul.vc_tta-tabs-list li.vc_tta-tab {
            border: 1px solid #eee;
            padding: 0 !important;
            margin: 0 !important;
            border-bottom: 0;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            margin-right: 10px !important;
        }
        .custom_tb ul.vc_tta-tabs-list li.vc_tta-tab.vc_active {
            background: #b87a29;
            color: #fff;
        }
    .lmp_load_more_button.br_lmp_button_settings, ul.products.columns-4, form.woocommerce-ordering, p.woocommerce-result-count {
      display: none !important;
    }
    </style>';
    }
}
add_action( 'wp_enqueue_scripts', 'scripts_only_shop' );
/*** Best Selling products shorcode ***/
add_shortcode("best_selling","best_selling_products");
function best_selling_products() {
  $best_selling = '<div class="owl-carousel owl-theme shop_prods">';
        $args = array(
            'post_type' => 'product',
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'posts_per_page' => 12,
            'tax_query' => array( array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => array(328),
                'operator' => 'NOT IN',
            )),
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); 
        global $product;
        $feat_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
        require_once('aq_resizer.php');
        $feat_image1 = aq_resize( $feat_image, 253, 322, true );
        $price = get_post_meta( get_the_ID(), '_price', true );
        /**** getting wishlist in custom loop ****/
        $user = wp_get_current_user();
        $wlid = get_user_meta($user->ID,"_alg_wc_wl_item",false);
        if(in_array(get_the_ID(), $wlid)){
            $wlsts = "remove";
        }else{
            $wlsts = "add";
        }
        $terms = get_the_terms( get_the_ID(), 'product_cat' );
        $product_cat_id = array();
        foreach ($terms as $term) {
            if( $term = get_term_by( 'id', $term->term_id, 'product_cat' ) ){
                $product_cat_id[] = $term->name;
            }
        }
        $uni_cats = array_unique($product_cat_id);
        $cat_list = implode(', ', $uni_cats);
        $cat_list = explode(', ', $cat_list);
        $best_selling .= '<div class="item">
            <li class="regular-product product-box post-'.get_the_ID().' product type-product status-publish has-post-thumbnail product_cat-fitness product_cat-shop product_tag-yoga first instock taxable shipping-taxable purchasable product-type-simple">
               <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                  <div class="product-img">
                     <img src="'.esc_url($feat_image1).'">
                  </div>
                  <div class="product-detail">
                     <h3 class="product-name">'.get_the_title().'</h3>
                     <p>
                        '.$cat_list[0].'      
                     </p>
                     <span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>'.$price.'</span></span>
                  </div>
               </a>
               <div class="product-overlay">
                  <div data-item_id="'.get_the_ID().'" data-action="alg-wc-wl-toggle" class="alg-wc-wl-btn '.$wlsts.' alg-wc-wl-thumb-btn alg-wc-wl-thumb-btn-abs alg-wc-wl-thumb-btn-loop" style="left: 17px; top: 17px; right: auto; bottom: auto; display: block;">
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-add">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-remove">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <i class="loading fas fa-sync-alt fa-spin fa-fw"></i>
                  </div>
                  <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                     <!-- <button class="btn btn-white btn-icon-l"><i class="lc-bag-icon"></i>Add to card</button> -->
                  </a>
                  <p class="product woocommerce add_to_cart_inline " style="border:4px solid #ccc; padding: 12px;"><a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"></a><a href="?add-to-cart='.get_the_ID().'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="XFHGOJ85453" aria-label="Add “'.get_the_title().'” to your cart" rel="nofollow">Add to cart</a></p>
                  <a href="'.home_url().'/cart?add-to-cart='.get_the_ID().'">Buy Now</a>
                  <!-- <button class="btn btn-white btn-icon-r">Add to card<i class="fa fa-chevron-circle-right"></i></button> -->
               </div>
            </li>
        </div>';
        endwhile;
        wp_reset_query();
    $best_selling .= '</div>';
  return($best_selling);
}
/*** Recent products shorcode ***/
add_shortcode("latest_products","recent_products_fun");
function recent_products_fun() {
  $latest_products = '<div class="owl-carousel owl-theme shop_prods">';
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'tax_query' => array( array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => array(328),
                'operator' => 'NOT IN',
            )),
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); 
        global $product;
        $feat_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
        require_once('aq_resizer.php');
        $feat_image1 = aq_resize( $feat_image, 253, 322, true );
        $price = get_post_meta( get_the_ID(), '_price', true );
        /**** getting wishlist in custom loop ****/
        $user = wp_get_current_user();
        $wlid = get_user_meta($user->ID,"_alg_wc_wl_item",false);
        if(in_array(get_the_ID(), $wlid)){
            $wlsts = "remove";
        }else{
            $wlsts = "add";
        }
        $terms = get_the_terms( get_the_ID(), 'product_cat' );
        $product_cat_id = array();
        foreach ($terms as $term) {
            if( $term = get_term_by( 'id', $term->term_id, 'product_cat' ) ){
                $product_cat_id[] = $term->name;
            }
        }
        $uni_cats = array_unique($product_cat_id);
        $cat_list = implode(', ', $uni_cats); 
        $cat_list = explode(', ', $cat_list);
        $latest_products .= '<div class="item">
            <li class="regular-product product-box post-'.get_the_ID().' product type-product status-publish has-post-thumbnail product_cat-fitness product_cat-shop product_tag-yoga first instock taxable shipping-taxable purchasable product-type-simple">
               <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                  <div class="product-img">
                     <img src="'.esc_url($feat_image1).'">
                  </div>
                  <div class="product-detail">
                     <h3 class="product-name">'.get_the_title().'</h3>
                     <p>
                        '.$cat_list[0].'      
                     </p>
                     <span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>'.$price.'</span></span>
                  </div>
               </a>
               <div class="product-overlay">
                  <div data-item_id="'.get_the_ID().'" data-action="alg-wc-wl-toggle" class="alg-wc-wl-btn '.$wlsts.' alg-wc-wl-thumb-btn alg-wc-wl-thumb-btn-abs alg-wc-wl-thumb-btn-loop" style="left: 17px; top: 17px; right: auto; bottom: auto; display: block;">
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-add">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-remove">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <i class="loading fas fa-sync-alt fa-spin fa-fw"></i>
                  </div>
                  <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                     <!-- <button class="btn btn-white btn-icon-l"><i class="lc-bag-icon"></i>Add to card</button> -->
                  </a>
                  <p class="product woocommerce add_to_cart_inline " style="border:4px solid #ccc; padding: 12px;"><a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"></a><a href="?add-to-cart='.get_the_ID().'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="XFHGOJ85453" aria-label="Add “'.get_the_title().'” to your cart" rel="nofollow">Add to cart</a></p>
                  <a href="'.home_url().'/cart?add-to-cart='.get_the_ID().'">Buy Now</a>
                  <!-- <button class="btn btn-white btn-icon-r">Add to card<i class="fa fa-chevron-circle-right"></i></button> -->
               </div>
            </li>
        </div>';
        endwhile;
        wp_reset_query();
    $latest_products .= '</div>';
  return($latest_products);
}
/*** Featured products shorcode ***/
add_shortcode("features_product","recent_features_prod");
function recent_features_prod() {
  $features_product = '<div class="owl-carousel owl-theme shop_prods">';
      $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN',
        );
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 12,
            'tax_query' => $tax_query,
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); 
        global $product;
        $feat_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
        require_once('aq_resizer.php');
        $feat_image1 = aq_resize( $feat_image, 253, 322, true );
        $price = get_post_meta( get_the_ID(), '_price', true );
        /**** getting wishlist in custom loop ****/
        $user = wp_get_current_user();
        $wlid = get_user_meta($user->ID,"_alg_wc_wl_item",false);
        if(in_array(get_the_ID(), $wlid)){
            $wlsts = "remove";
        }else{
            $wlsts = "add";
        }
        $terms = get_the_terms( get_the_ID(), 'product_cat' );
        $product_cat_id = array();
        foreach ($terms as $term) {
            if( $term = get_term_by( 'id', $term->term_id, 'product_cat' ) ){
                $product_cat_id[] = $term->name;
            }
        }
        $uni_cats = array_unique($product_cat_id);
        $cat_list = implode(', ', $uni_cats); 
        $cat_list = explode(', ', $cat_list);
        $features_product .= '<div class="item">
            <li class="regular-product product-box post-'.get_the_ID().' product type-product status-publish has-post-thumbnail product_cat-fitness product_cat-shop product_tag-yoga first instock taxable shipping-taxable purchasable product-type-simple">
               <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                  <div class="product-img">
                     <img src="'.esc_url($feat_image1).'">
                  </div>
                  <div class="product-detail">
                     <h3 class="product-name">'.get_the_title().'</h3>
                     <p>
                        '.$cat_list[0].'      
                     </p>
                     <span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>'.$price.'</span></span>
                  </div>
               </a>
               <div class="product-overlay">
                  <div data-item_id="'.get_the_ID().'" data-action="alg-wc-wl-toggle" class="alg-wc-wl-btn '.$wlsts.' alg-wc-wl-thumb-btn alg-wc-wl-thumb-btn-abs alg-wc-wl-thumb-btn-loop" style="left: 17px; top: 17px; right: auto; bottom: auto; display: block;">
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-add">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <div class="alg-wc-wl-view-state alg-wc-wl-view-state-remove">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                      </div>
                      <i class="loading fas fa-sync-alt fa-spin fa-fw"></i>
                  </div>
                  <a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                     <!-- <button class="btn btn-white btn-icon-l"><i class="lc-bag-icon"></i>Add to card</button> -->
                  </a>
                  <p class="product woocommerce add_to_cart_inline " style="border:4px solid #ccc; padding: 12px;"><a href="'.get_the_permalink().'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"></a><a href="?add-to-cart='.get_the_ID().'" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="'.get_the_ID().'" data-product_sku="XFHGOJ85453" aria-label="Add “'.get_the_title().'” to your cart" rel="nofollow">Add to cart</a></p>
                  <a href="'.home_url().'/cart?add-to-cart='.get_the_ID().'">Buy Now</a>
                  <!-- <button class="btn btn-white btn-icon-r">Add to card<i class="fa fa-chevron-circle-right"></i></button> -->
               </div>
            </li>
        </div>';
        endwhile;
        wp_reset_query();
    $features_product .= '</div>';
  return($features_product);
}
/******* Extra column on product category backend admin side *******/
function add_custom_column($columns) {
    $columns['Sethome'] = 'Sethome';

    return $columns; 
} 
add_filter('manage_edit-product_cat_columns', 'add_custom_column'); 

function category_custom_column_value( $columns, $column, $term_id ) { 
    if ($column == 'Sethome') {
        $chk = get_term_meta( $term_id, 'set_home', true );
        if($chk){ $status = "checked"; } else { $status = " "; }
        $col = '<input type="checkbox" class="set_home" id="'.$term_id.'" '.$status.'>';
        return $col;
    }
}
add_filter('manage_product_cat_custom_column', 'category_custom_column_value', 10, 3);
/********** Perform save action on cateofy set home checkbox **********/
add_action( 'admin_footer', 'set_home_script' );
function set_home_script() { 
    global $pagenow; 
    if (( $pagenow == 'edit-tags.php' ) && ($_GET['post_type'] == 'product')) { ?>
      <script type="text/javascript" >
      jQuery(".set_home").click(function(){
          var id = this.id;
          if(jQuery(this).prop("checked") == true){
                var val = 1;
            }else if(jQuery(this).prop("checked") == false){
                var val = 0;
            }
        var data = {
          'action': 'set_home',
          'id': id,
          'val': val
        };
        jQuery.post(ajaxurl, data, function(response) {
        });
      });
      </script> <?php
    }
}
/********** Update category on checkbox click **********/
add_action( 'wp_ajax_set_home', 'set_home' );
function set_home() {
  if($_POST['id']){
    update_term_meta( $_POST['id'], 'set_home', $_POST['val'] );
  }
}
/********* Home Categories slider *********/
add_shortcode("home_cats","home_cats_slider");
function home_cats_slider() {
  $home_cats = '<div class="owl-carousel owl-theme">';
    $taxonomy     = 'product_cat';
    $orderby      = 'name';  
    $show_count   = 0;
    $pad_counts   = 0;
    $hierarchical = 1;
    $title        = '';  
    $empty        = 1;
  
    $args = array(
       'taxonomy'     => $taxonomy,
       'orderby'      => $orderby,
       'show_count'   => $show_count,
       'pad_counts'   => $pad_counts,
       'hierarchical' => $hierarchical,
       'title_li'     => $title,
       'hide_empty'   => $empty,
       'meta_query' => array(
        array(
          'key' => 'set_home',
          'value' => 1
        )
      )
    );
    $all_categories = get_categories( $args );
    foreach ($all_categories as $cat) {
      $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
      $image = wp_get_attachment_url( $thumbnail_id );
      $home_cats .= '<div class="item cats">
        <div class="titl"><b><a href="'. get_term_link($cat->slug, 'product_cat') .'">'. $cat->name .'</a></b></div>
        <div class="imgslide"><img src="'.$image.'"></div>
        <div class="catslide"><ul>';
        $category_id = $cat->term_id;           
        $args2 = array(
            'taxonomy'     => $taxonomy,
            'child_of'     => 0,
            'parent'       => $category_id,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty,
            'posts_per_page' => 5
        );
        $all_category = get_categories( $args2 );
        foreach ($all_category as $all_cats) {
          $home_cats .= '<li><a href="'. get_term_link($all_cats->slug, 'product_cat') .'">'. $all_cats->name .'</a></li>';
        }
        $home_cats .= '<li><b><a href="'. get_term_link($cat->slug, 'product_cat') .'">Show All</a></b></li>
        </ul></div>
      </div>';
    }
    wp_reset_query();
    $home_cats .= '</div>';
  return($home_cats);
}
/*******Custom Brands Post Type*************/
add_action( 'init', 'register_cpt_Brands' );
function register_cpt_Brands() {
    $labels = array( 
        'name' => _x( 'All Brands', 'Brands' ),
        'singular_name' => _x( 'All Brands', 'Brands' ),
        'add_new' => _x( 'Add New Brands', 'Brands' ),
        'add_new_item' => _x( 'Add New Brands', 'Brands' ),
        'edit_item' => _x( 'Edit Brands', 'Brands' ),
        'new_item' => _x( 'New Brands', 'Brands' ),
        'view_item' => _x( 'View Brands', 'Brands' ),
        'search_items' => _x( 'Search Brands', 'Brands' ),
        'not_found' => _x( 'No Brands found', 'Brands' ),
        'not_found_in_trash' => _x( 'No Brands found in Trash', 'Brands' ),
        'parent_item_colon' => _x( 'Parent Brands:', 'Brands' ),
        'menu_name' => _x( 'All Brands', 'Brands' ),
    );
    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'thumbnail' ),
        'taxonomies' => array( 'category', 'post_tag', 'page-category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    'taxonomies' => array('Brands_type')
    );
    register_post_type( 'Brands', $args );
}
function add_Brands_type_taxonomies() {
  register_taxonomy('Brands_type', 'review', array(
    'hierarchical' => true,
    'labels' => array(
      'name' => _x( 'Brands Category', 'taxonomy general name' ),
      'singular_name' => _x( 'Brands-Category', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Brands-Categories' ),
      'all_items' => __( 'All Brands-Categories' ),
      'parent_item' => __( 'Parent Brands-Category' ),
      'parent_item_colon' => __( 'Parent Brands-Category:' ),
      'edit_item' => __( 'Edit Brands-Category' ),
      'update_item' => __( 'Update Brands-Category' ),
      'add_new_item' => __( 'Add New Brands-Category' ),
      'new_item_name' => __( 'New Brands-Category Name' ),
      'menu_name' => __( 'Categories' ),
    ),
    'rewrite' => array(
      'slug' => 'Brands_type',
      'with_front' => false,
      'hierarchical' => true
    ),
  ));
}
add_action( 'init', 'add_Brands_type_taxonomies', 0 );
/*** Featured products shorcode ***/
add_shortcode("brands","display_brands");
function display_brands() {
  $brands = '<div class="owl-carousel owl-theme">';
        $args = array(
            'post_type' => 'brands',
      'posts_per_page' => 12,
      'tax_query' => array(
          array(
          'taxonomy' => 'Brands_type',
          'field' => 'brands',
                    'terms' => 356
        )
      )
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); 
        global $product;
        $feat_image = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
        $brands .= '<div class="item">
            <img src="'.$feat_image.'">
        </div>';
        endwhile;
        wp_reset_query();
    $brands .= '</div>';
  return($brands);
}
/*********** Handling coupon code applied by customers during booking ***********/
function coupon_code_by_customers( $order_id, $title, $amount, $tax_class = '' ) {
    $order    = wc_get_order($order_id);
    $subtotal = $order->get_subtotal();
    $item = new WC_Order_Item_Fee();

    if ( strpos($amount, '%') !== false ) {
        $percentage = (float) str_replace( array('%', ' '), array('', ''), $amount );
        $percentage = $percentage > 100 ? -100 : -$percentage;
        $discount   = $percentage * $subtotal / 100;
    } else {
        $discount = (float) str_replace( ' ', '', $amount );
        $discount = $discount > $subtotal ? -$subtotal : -$discount;
    }

    $item->set_tax_class( $tax_class );
    $item->set_name( $title );
    $item->set_amount( $discount );
    $item->set_total( $discount );

    if ( '0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled() ) {
        $tax_for   = array(
            'country'   => $order->get_shipping_country(),
            'state'     => $order->get_shipping_state(),
            'postcode'  => $order->get_shipping_postcode(),
            'city'      => $order->get_shipping_city(),
            'tax_class' => $item->get_tax_class(),
        );
        $tax_rates = WC_Tax::find_rates( $tax_for );
        $taxes     = WC_Tax::calc_tax( $item->get_total(), $tax_rates, false );

        if ( method_exists( $item, 'get_subtotal' ) ) {
            $subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, false );
            $item->set_taxes( array( 'total' => $taxes, 'subtotal' => $subtotal_taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        } else {
            $item->set_taxes( array( 'total' => $taxes ) );
            $item->set_total_tax( array_sum($taxes) );
        }
        $has_taxes = true;
    } else {
        $item->set_taxes( false );
        $has_taxes = false;
    }
    $item->save();

    $order->add_item( $item );
    $order->calculate_totals( $has_taxes );
    $order->save();
}
/********** Coupon applied via ajax if customers hit the apply button **********/
add_action( 'wp_ajax_apply_custom_coupon', 'apply_custom_coupon' );
function apply_custom_coupon() {
    /** validate the coupon **/
    $coupon_code = $_POST['coupon_val'];
    global $woocommerce;
    $coupon_data = new WC_Coupon($coupon_code);
    
    $coupon_amount = $coupon_data->amount;
    $discount_type = $coupon_data->discount_type;
    $individual_use = $coupon_data->individual_use;
    $usage_count = $coupon_data->usage_count;
    $usage_limit = $coupon_data->usage_limit;
    $coupon_desc = $coupon_data->description;
    if($discount_type == "fixed_cart"){
        $coupon_amount = $coupon_amount;
    }else{
        $coupon_amount = $coupon_amount.'%';
    }
    $user = wp_get_current_user();

  if($_POST['coupon_val'] && $_POST['order_id']){
      if($coupon_amount){
          $status = get_post_meta($_POST['order_id'], "coupon_used", true);
          $status_user = get_user_meta($user->ID, "coupon_used_user", true);
          if($status && $status_user){
              echo "Coupon already used on this order!";
          }else{
                coupon_code_by_customers( $_POST['order_id'], __("Coupon applied"), $coupon_amount );
                update_post_meta($_POST['order_id'], "coupon_used", 1);
                update_user_meta($user->ID, "coupon_used_user", 1);
                echo "Coupon applied successfully on this order!";
          }
      }else{
          echo "Invalid copon code!";
      }
  }
}
/********** Category sidebar created ************/
function category_sidebar_code() {
  register_sidebar(
    array(
      'name'          => __( 'Product Category Sidebar', 'loncani' ),
      'id'            => 'cats-sidebar',
      'description'   => __( 'Add widgets here to appear in your category page.', 'loncani' ),
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h2 class="widget-title">',
      'after_title'   => '</h2>',
    )
  );
}
add_action( 'widgets_init', 'category_sidebar_code' );
/*******Custom Circle Post Type*************/
add_action( 'init', 'register_cpt_Circle' );
function register_cpt_Circle() {
    $labels = array( 
        'name' => _x( 'All Circle', 'Circle' ),
        'singular_name' => _x( 'All Circle', 'Circle' ),
        'add_new' => _x( 'Add New Circle', 'Circle' ),
        'add_new_item' => _x( 'Add New Circle', 'Circle' ),
        'edit_item' => _x( 'Edit Circle', 'Circle' ),
        'new_item' => _x( 'New Circle', 'Circle' ),
        'view_item' => _x( 'View Circle', 'Circle' ),
        'search_items' => _x( 'Search Circle', 'Circle' ),
        'not_found' => _x( 'No Circle found', 'Circle' ),
        'not_found_in_trash' => _x( 'No Circle found in Trash', 'Circle' ),
        'parent_item_colon' => _x( 'Parent Circle:', 'Circle' ),
        'menu_name' => _x( 'All Circle', 'Circle' ),
    );
    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'editor' ),
        'taxonomies' => array( 'category', 'post_tag', 'page-category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    'taxonomies' => array('Circle_type')
    );
    register_post_type( 'Circle', $args );
}
function add_Circle_type_taxonomies() {
  register_taxonomy('Circle_type', 'review', array(
    'hierarchical' => true,
    'labels' => array(
      'name' => _x( 'Circle Category', 'taxonomy general name' ),
      'singular_name' => _x( 'Circle-Category', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Circle-Categories' ),
      'all_items' => __( 'All Circle-Categories' ),
      'parent_item' => __( 'Parent Circle-Category' ),
      'parent_item_colon' => __( 'Parent Circle-Category:' ),
      'edit_item' => __( 'Edit Circle-Category' ),
      'update_item' => __( 'Update Circle-Category' ),
      'add_new_item' => __( 'Add New Circle-Category' ),
      'new_item_name' => __( 'New Circle-Category Name' ),
      'menu_name' => __( 'Categories' ),
    ),
    'rewrite' => array(
      'slug' => 'Circle_type',
      'with_front' => false,
      'hierarchical' => true
    ),
  ));
}
add_action( 'init', 'add_Circle_type_taxonomies', 0 );
/******************* Shortcodes added for Circle **********************/
add_shortcode("aboutcircle","circle_section_for_aboutpage");
function circle_section_for_aboutpage(){
    $custom_args = array(
    'post_type' => 'circle',
    'tax_query' => array(
              array(
              'taxonomy' => 'Circle_type',
              'field' => 'circle',
              'terms' => 397
              )
        )
  );
  $custom_query = new WP_Query( $custom_args ); 
  while ( $custom_query->have_posts() ){ 
      $custom_query->the_post();
      global $post;
      if($post->ID == "3808"){
          $ttl_1 = $post->post_title;
          $cont_1 = $post->post_content;
      } else if($post->ID == "3809"){
          $ttl_2 = $post->post_title;
          $cont_2 = $post->post_content;
      }
      if($post->ID == "3810"){
          $ttl_3 = $post->post_title;
          $cont_3 = $post->post_content;
      }
      if($post->ID == "3811"){
          $ttl_4 = $post->post_title;
          $cont_4 = $post->post_content;
      }
      if($post->ID == "3812"){
          $ttl_5 = $post->post_title;
          $cont_5 = $post->post_content;
      }
      if($post->ID == "3816"){
          $ttl_6 = $post->post_title;
          $cont_6 = $post->post_content;
      }
      if($post->ID == "3813"){
          $ttl_7 = $post->post_title;
          $cont_7 = $post->post_content;
      }
      if($post->ID == "3814"){
          $ttl_8 = $post->post_title;
          $cont_8 = $post->post_content;
      }
      if($post->ID == "3815"){
          $ttl_9 = $post->post_title;
          $cont_9 = $post->post_content;
      }
  }
    $aboutcircle = '<div class="core-values">
       <div class="core-values-content about-layer-forward core-values-slider">
          <div class="core-values__item item1" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide00">
             <div class="core-values__item-number">'.$ttl_1.'</div>
             <div class="core-values__item-text">'.$cont_1.'</div>
          </div>
          <div class="core-values__item item2" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide01">
             <div class="core-values__item-number">'.$ttl_2.'</div>
             <div class="core-values__item-text">'.$cont_2.'</div>
          </div>
          <div class="core-values__item item3" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide02">
             <div class="core-values__item-number">'.$ttl_3.'</div>
             <div class="core-values__item-text">'.$cont_3.'</div>
          </div>
          <div class="core-values__item item4" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide03">
             <div class="core-values__item-number">'.$ttl_4.'</div>
             <div class="core-values__item-text">'.$cont_4.'</div>
          </div>
          <div class="core-values__item item5" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide04">
             <div class="core-values__item-number">'.$ttl_5.'</div>
             <div class="core-values__item-text">'.$cont_5.'</div>
          </div>
          <div class="core-values__item item6" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide05">
             <div class="core-values__item-number">'.$ttl_6.'</div>
             <div class="core-values__item-text">'.$cont_6.'</div>
          </div>
          <div class="core-values__item item7" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide06">
             <div class="core-values__item-number">'.$ttl_7.'</div>
             <div class="core-values__item-text">'.$cont_7.'</div>
          </div>
          <div class="core-values__item item8" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide07">
             <div class="core-values__item-number">'.$ttl_8.'</div>
             <div class="core-values__item-text">'.$cont_8.'</div>
          </div>
          <div class="core-values__item item9" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide08">
             <div class="core-values__item-number">'.$ttl_9.'</div>
             <div class="core-values__item-text">'.$cont_9.'</div>
          </div>
          <div class="core-values__item item10" style="" aria-hidden="true" tabindex="-1" role="option" aria-describedby="slick-slide09"></div>
       </div>
    </div>';
    return($aboutcircle);
}
/****************************** Help and Support at Dashboard Side ******************************/
add_action("wp_head","help_and_support_popup");
function help_and_support_popup(){
    if ( is_page_template( 'template-dashboard.php' ) || function_exists( 'dokan_dashboard_nav' ) ) {
       echo get_template_part("helpsupport","popup");
       ?><script>
       jQuery(document).ready(function(){
            jQuery(".helpnsupport").click(function(){
                jQuery(".help_popup").toggle();
            });
        });
       
       </script><?php
    }
}
/*******Custom Help Post Type*************/
add_action( 'init', 'register_cpt_Help' );
function register_cpt_Help() {
    $labels = array( 
        'name' => _x( 'All Help', 'Help' ),
        'singular_name' => _x( 'All Help', 'Help' ),
        'add_new' => _x( 'Add New Help', 'Help' ),
        'add_new_item' => _x( 'Add New Help', 'Help' ),
        'edit_item' => _x( 'Edit Help', 'Help' ),
        'new_item' => _x( 'New Help', 'Help' ),
        'view_item' => _x( 'View Help', 'Help' ),
        'search_items' => _x( 'Search Help', 'Help' ),
        'not_found' => _x( 'No Help found', 'Help' ),
        'not_found_in_trash' => _x( 'No Help found in Trash', 'Help' ),
        'parent_item_colon' => _x( 'Parent Help:', 'Help' ),
        'menu_name' => _x( 'All Help', 'Help' ),
    );
    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        'supports' => array( 'title', 'editor'),
        'taxonomies' => array( 'category', 'post_tag', 'page-category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    'taxonomies' => array('Help_type')
    );
    register_post_type( 'Help', $args );
}
function add_Help_type_taxonomies() {
  register_taxonomy('Help_type', 'review', array(
    'hierarchical' => true,
    'labels' => array(
      'name' => _x( 'Help Category', 'taxonomy general name' ),
      'singular_name' => _x( 'Help-Category', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Help-Categories' ),
      'all_items' => __( 'All Help-Categories' ),
      'parent_item' => __( 'Parent Help-Category' ),
      'parent_item_colon' => __( 'Parent Help-Category:' ),
      'edit_item' => __( 'Edit Help-Category' ),
      'update_item' => __( 'Update Help-Category' ),
      'add_new_item' => __( 'Add New Help-Category' ),
      'new_item_name' => __( 'New Help-Category Name' ),
      'menu_name' => __( 'Categories' ),
    ),
    'rewrite' => array(
      'slug' => 'Help_type',
      'with_front' => false,
      'hierarchical' => true
    ),
  ));
}
add_action( 'init', 'add_Help_type_taxonomies', 0 );
/*************************** Meta box for Help settings ****************************/
function help_meta_box()
{
    $screens = ['help'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wporg_box_id',
            'Help & Support Configurations',
            'call_help_elements',
            $screen
        );
    }
}
add_action('add_meta_boxes', 'help_meta_box');
/*********************  Elements add on help meta box*****************/
function call_help_elements($post)
{
    $post_data = get_post(3913); 
    $below_header = get_post_meta($post_data->ID,"below_header",true);
    $whats_new = get_post_meta($post_data->ID,"whats_new",true);
    $icon_01 = get_post_meta($post_data->ID,"icon_01",true);
    $name_01 = get_post_meta($post_data->ID,"name_01",true);
    $link_01 = get_post_meta($post_data->ID,"link_01",true);
    $icon_02 = get_post_meta($post_data->ID,"icon_02",true);
    $name_02 = get_post_meta($post_data->ID,"name_02",true);
    $link_02 = get_post_meta($post_data->ID,"link_02",true);
    $icon_03 = get_post_meta($post_data->ID,"icon_03",true);
    $name_03 = get_post_meta($post_data->ID,"name_03",true);
    $link_03 = get_post_meta($post_data->ID,"link_03",true);
    $icon_1 = get_post_meta($post_data->ID,"icon_1",true);
    $name_1 = get_post_meta($post_data->ID,"name_1",true);
    $link_1 = get_post_meta($post_data->ID,"link_1",true);
    $icon_2 = get_post_meta($post_data->ID,"icon_2",true);
    $name_2 = get_post_meta($post_data->ID,"name_2",true);
    $link_2 = get_post_meta($post_data->ID,"link_2",true);
    $icon_3 = get_post_meta($post_data->ID,"icon_3",true);
    $name_3 = get_post_meta($post_data->ID,"name_3",true);
    $link_3 = get_post_meta($post_data->ID,"link_3",true);
    $icon_4 = get_post_meta($post_data->ID,"icon_4",true);
    $name_4 = get_post_meta($post_data->ID,"name_4",true);
    $link_4 = get_post_meta($post_data->ID,"link_4",true);
    $icon_5 = get_post_meta($post_data->ID,"icon_5",true);
    $name_5 = get_post_meta($post_data->ID,"name_5",true);
    $link_5 = get_post_meta($post_data->ID,"link_5",true);
    $icon_6 = get_post_meta($post_data->ID,"icon_6",true);
    $name_6 = get_post_meta($post_data->ID,"name_6",true);
    $link_6 = get_post_meta($post_data->ID,"link_6",true);
    $still_need = get_post_meta($post_data->ID,"still_need",true);
    $still_need_link = get_post_meta($post_data->ID,"still_need_link",true);
    $support = get_post_meta($post_data->ID,"support",true);
    $s_icon_1 = get_post_meta($post_data->ID,"s_icon_1",true);
    $s_name_1 = get_post_meta($post_data->ID,"s_name_1",true);
    $s_link_1 = get_post_meta($post_data->ID,"s_link_1",true);
    $s_icon_2 = get_post_meta($post_data->ID,"s_icon_2",true);
    $s_name_2 = get_post_meta($post_data->ID,"s_name_2",true);
    $s_link_2 = get_post_meta($post_data->ID,"s_link_2",true);
    $s_icon_3 = get_post_meta($post_data->ID,"s_icon_3",true);
    $s_name_3 = get_post_meta($post_data->ID,"s_name_3",true);
    $s_link_3 = get_post_meta($post_data->ID,"s_link_3",true);
    ?><table>
        <tr><td>
            <input style="width: 100%;" type="text" value="<?php echo $below_header; ?>" placeholder="Header line below name" name="below_header">
        </td><td><input value="<?php echo $whats_new; ?>" type="text" placeholder="Whats New" name="whats_new">
        </td><td>
            <input type="text" value='<?php echo $s_link_1; ?>' placeholder="Whats new link" name="s_link_1">
        </td></tr>
        <tr><th>Icon</th><th>Name</th><th>Link</th></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_01; ?>' placeholder="Fontawesome" name="icon_01">
        </td><td>
            <input type="text" value='<?php echo $name_01; ?>' placeholder="Name" name="name_01">
        </td><td>
            <input type="text" value='<?php echo $link_01; ?>' placeholder="Link" name="link_01">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_02; ?>' placeholder="Fontawesome" name="icon_02">
        </td><td>
            <input type="text" value='<?php echo $name_02; ?>' placeholder="Name" name="name_02">
        </td><td>
            <input type="text" value='<?php echo $link_02; ?>' placeholder="Link" name="link_02">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_03; ?>' placeholder="Fontawesome" name="icon_03">
        </td><td>
            <input type="text" value='<?php echo $name_03; ?>' placeholder="Name" name="name_03">
        </td><td>
            <input type="text" value='<?php echo $link_03; ?>' placeholder="Link" name="link_03">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_1; ?>' placeholder="Fontawesome" name="icon_1">
        </td><td>
            <input type="text" value='<?php echo $name_1; ?>' placeholder="Name" name="name_1">
        </td><td>
            <input type="text" value='<?php echo $link_1; ?>' placeholder="Link" name="link_1">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_2; ?>' placeholder="Fontawesome" name="icon_2">
        </td><td>
            <input type="text" value='<?php echo $name_2; ?>' placeholder="Name" name="name_2">
        </td><td>
            <input type="text" value='<?php echo $link_2; ?>' placeholder="Link" name="link_2">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_3; ?>' placeholder="Fontawesome" name="icon_3">
        </td><td>
            <input type="text" value='<?php echo $name_3; ?>' placeholder="Name" name="name_3">
        </td><td>
            <input type="text" value='<?php echo $link_3; ?>' placeholder="Link" name="link_3">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_4; ?>' placeholder="Fontawesome" name="icon_4">
        </td><td>
            <input type="text" value='<?php echo $name_4; ?>' placeholder="Name" name="name_4">
        </td><td>
            <input type="text" value='<?php echo $link_4; ?>' placeholder="Link" name="link_4">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_5; ?>' placeholder="Fontawesome" name="icon_5">
        </td><td>
            <input type="text" value='<?php echo $name_5; ?>' placeholder="Name" name="name_5">
        </td><td>
            <input type="text" value='<?php echo $link_5; ?>' placeholder="Link" name="link_5">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $icon_6; ?>' placeholder="Fontawesome" name="icon_6">
        </td><td>
            <input type="text" value='<?php echo $name_6; ?>' placeholder="Name" name="name_6">
        </td><td>
            <input type="text" value='<?php echo $link_6; ?>' placeholder="Link" name="link_6">
        </td></tr>
        <tr><td colspan="2"><input style="width: 100%;" type="text" value='<?php echo $still_need; ?>' placeholder="Still need help" name="still_need"></td><td colspan="1"><input style="width: 100%;" value='<?php echo $still_need_link; ?>' type="text" placeholder="Link" name="still_need_link"></td></tr>
        <tr><td colspan="3"><input style="width: 100%;" type="text" value='<?php echo $support; ?>' placeholder="Support" name="support"></td></tr>
        <tr><td>
            <input type="text" value='<?php echo $s_icon_1; ?>' placeholder="Fontawesome" name="s_icon_1">
        </td><td colspan="2">
            <input style="width:100%;" type="text" value='<?php echo $s_name_1; ?>' placeholder="Name" name="s_name_1">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $s_icon_2; ?>' placeholder="Fontawesome" name="s_icon_2">
        </td><td>
            <input type="text" value='<?php echo $s_name_2; ?>' placeholder="Name" name="s_name_2">
        </td><td>
            <input type="text" value='<?php echo $s_link_2; ?>' placeholder="Link" name="s_link_2">
        </td></tr>
        <tr><td>
            <input type="text" value='<?php echo $s_icon_3; ?>' placeholder="Fontawesome" name="s_icon_3">
        </td><td>
            <input type="text" value='<?php echo $s_name_3; ?>' placeholder="Name" name="s_name_3">
        </td><td>
            <input type="text" value='<?php echo $s_link_3; ?>' placeholder="Link" name="s_link_3">
        </td></tr>
    </table><?php
}
/****************** Saving help meta box ******************/
function help_meta_saving($post_id)
{
    if (array_key_exists('below_header', $_POST)) {
        update_post_meta($post_id,'below_header',$_POST['below_header']);
    }
    if (array_key_exists('whats_new', $_POST)) {
        update_post_meta($post_id,'whats_new',$_POST['whats_new']);
    }
    if (array_key_exists('icon_01', $_POST)) {
        update_post_meta($post_id,'icon_01',$_POST['icon_01']);
    }
    if (array_key_exists('name_01', $_POST)) {
        update_post_meta($post_id,'name_01',$_POST['name_01']);
    }
    if (array_key_exists('link_01', $_POST)) {
        update_post_meta($post_id,'link_01',$_POST['link_01']);
    }
    if (array_key_exists('icon_02', $_POST)) {
        update_post_meta($post_id,'icon_02',$_POST['icon_02']);
    }
    if (array_key_exists('name_02', $_POST)) {
        update_post_meta($post_id,'name_02',$_POST['name_02']);
    }
    if (array_key_exists('link_02', $_POST)) {
        update_post_meta($post_id,'link_02',$_POST['link_02']);
    }
    if (array_key_exists('icon_03', $_POST)) {
        update_post_meta($post_id,'icon_03',$_POST['icon_03']);
    }
    if (array_key_exists('name_03', $_POST)) {
        update_post_meta($post_id,'name_03',$_POST['name_03']);
    }
    if (array_key_exists('link_03', $_POST)) {
        update_post_meta($post_id,'link_03',$_POST['link_03']);
    }
    if (array_key_exists('icon_1', $_POST)) {
        update_post_meta($post_id,'icon_1',$_POST['icon_1']);
    }
    if (array_key_exists('name_1', $_POST)) {
        update_post_meta($post_id,'name_1',$_POST['name_1']);
    }
    if (array_key_exists('link_1', $_POST)) {
        update_post_meta($post_id,'link_1',$_POST['link_1']);
    }
    if (array_key_exists('icon_2', $_POST)) {
        update_post_meta($post_id,'icon_2',$_POST['icon_2']);
    }
    if (array_key_exists('name_2', $_POST)) {
        update_post_meta($post_id,'name_2',$_POST['name_2']);
    }
    if (array_key_exists('link_2', $_POST)) {
        update_post_meta($post_id,'link_2',$_POST['link_2']);
    }
    if (array_key_exists('icon_3', $_POST)) {
        update_post_meta($post_id,'icon_3',$_POST['icon_3']);
    }
    if (array_key_exists('name_3', $_POST)) {
        update_post_meta($post_id,'name_3',$_POST['name_3']);
    }
    if (array_key_exists('link_3', $_POST)) {
        update_post_meta($post_id,'link_3',$_POST['link_3']);
    }
    if (array_key_exists('icon_4', $_POST)) {
        update_post_meta($post_id,'icon_4',$_POST['icon_4']);
    }
    if (array_key_exists('name_4', $_POST)) {
        update_post_meta($post_id,'name_4',$_POST['name_4']);
    }
    if (array_key_exists('link_4', $_POST)) {
        update_post_meta($post_id,'link_4',$_POST['link_4']);
    }
    if (array_key_exists('icon_5', $_POST)) {
        update_post_meta($post_id,'icon_5',$_POST['icon_5']);
    }
    if (array_key_exists('name_5', $_POST)) {
        update_post_meta($post_id,'name_5',$_POST['name_5']);
    }
    if (array_key_exists('link_5', $_POST)) {
        update_post_meta($post_id,'link_5',$_POST['link_5']);
    }
    if (array_key_exists('icon_6', $_POST)) {
        update_post_meta($post_id,'icon_6',$_POST['icon_6']);
    }
    if (array_key_exists('name_6', $_POST)) {
        update_post_meta($post_id,'name_6',$_POST['name_6']);
    }
    if (array_key_exists('link_6', $_POST)) {
        update_post_meta($post_id,'link_6',$_POST['link_6']);
    }
    if (array_key_exists('still_need', $_POST)) {
        update_post_meta($post_id,'still_need',$_POST['still_need']);
    }
    if (array_key_exists('still_need_link', $_POST)) {
        update_post_meta($post_id,'still_need_link',$_POST['still_need_link']);
    }
    if (array_key_exists('support', $_POST)) {
        update_post_meta($post_id,'support',$_POST['support']);
    }
     if (array_key_exists('s_icon_1', $_POST)) {
        update_post_meta($post_id,'s_icon_1',$_POST['s_icon_1']);
    }
    if (array_key_exists('s_name_1', $_POST)) {
        update_post_meta($post_id,'s_name_1',$_POST['s_name_1']);
    }
    if (array_key_exists('s_link_1', $_POST)) {
        update_post_meta($post_id,'s_link_1',$_POST['s_link_1']);
    }
    if (array_key_exists('s_icon_2', $_POST)) {
        update_post_meta($post_id,'s_icon_2',$_POST['s_icon_2']);
    }
    if (array_key_exists('s_name_2', $_POST)) {
        update_post_meta($post_id,'s_name_2',$_POST['s_name_2']);
    }
    if (array_key_exists('s_link_2', $_POST)) {
        update_post_meta($post_id,'s_link_2',$_POST['s_link_2']);
    }
    if (array_key_exists('s_icon_3', $_POST)) {
        update_post_meta($post_id,'s_icon_3',$_POST['s_icon_3']);
    }
    if (array_key_exists('s_name_3', $_POST)) {
        update_post_meta($post_id,'s_name_3',$_POST['s_name_3']);
    }
    if (array_key_exists('s_link_3', $_POST)) {
        update_post_meta($post_id,'s_link_3',$_POST['s_link_3']);
    }
}
add_action('save_post', 'help_meta_saving');
/************************* SYNC contacts to getresponse via APIs during signup ***************************/
add_action("user_register","add_contacts_to_getresponse");
function add_contacts_to_getresponse($userid) {
    $user_role = $_POST['user_role'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $flname = $first_name." ".$last_name;
    $email = $_POST['email'];
    if(($user_role == "owner") || ($user_role == "seller")) {
        $args = '{
            "name": "'.$flname.'",
            "campaign": {
                "campaignId": "zhvkj"
            },
            "email": "'.$email.'"
        }';
    }else{
        $args = '{
            "name": "'.$flname.'",
            "campaign": {
                "campaignId": "zhvXI"
            },
            "email": "'.$email.'"
        }';
    }
    $getresp = curl_init();
        curl_setopt($getresp, CURLOPT_URL, 'https://api.getresponse.com/v3/contacts');
        curl_setopt($getresp, CURLOPT_POSTFIELDS, $args);
        curl_setopt($getresp, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($getresp, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($getresp, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($getresp, CURLOPT_HEADER, false);
        curl_setopt($getresp, CURLOPT_USERAGENT, 'PHP GetResponse client 0.0.2');
        curl_setopt($getresp, CURLOPT_HTTPHEADER, array('X-Auth-Token: api-key jz1mcrqgt3js5vr80468qi58zb1uot1b', 'Content-Type: application/json'));
    curl_exec($getresp);
}
/***************** From 6.29.26 SMTP configurations ****************/
add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = SMTP_HOST;
    $phpmailer->SMTPAuth   = SMTP_AUTH;
    $phpmailer->Port       = SMTP_PORT;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->Username   = SMTP_USERNAME;
    $phpmailer->Password   = SMTP_PASSWORD;
    $phpmailer->From       = SMTP_FROM;
    $phpmailer->FromName   = SMTP_FROMNAME;
}
/************************* Issue the ticket to customer if they made payments for booking *******************************/
function booking_payments_recieved_send_ticket_to_customer( $order_id ) {
    if($order_id){
        $listing_id = get_post_meta($order_id,"listing_id",true);
        $listing_type = get_post_meta($listing_id,"_listing_type",true);
        if($listing_type == "event"){
            $order_total = get_post_meta($order_id,"_order_total",true);
            $owner_id = get_post_meta($order_id,"owner_id",true);
            $owner = get_user_by('id',$owner_id);
            $fname = $owner->first_name;
            $lname = $owner->last_name;
            
            $customer_user = get_post_meta($order_id,"_customer_user",true);

            $billing_email = get_post_meta($order_id,"_billing_email",true);
            $cfname = get_post_meta($order_id,"_billing_first_name",true);
            $clname = get_post_meta($order_id,"_billing_last_name",true);
            
            $listing_title = get_post_meta($listing_id,"listing_title",true);
            $friendly_address = get_post_meta($listing_id,"_friendly_address",true);
            $listing_description = get_post_meta($listing_id,"listing_description",true);
            $event_date = get_post_meta($listing_id,"_event_date",true);
            $event_tickets_sold = get_post_meta($listing_id,"_event_tickets_sold",true);
            
            $message = '<div style="height: 600px;width: 300px;background:whitesmoke;box-shadow: 0px 0px 100px rgba(0,0,0,0.2);display:block;margin: auto;margin-top: 60px;border-radius: 40px;border: 15px solid #b87a29;">
                  <h2 style="text-align:center;">Event Booking Ticket</h2>
                  <hr/>
                  <div class="pass">
                  <div style="text-align: center;">
                    <h3 style="margin: 0;font-size: 18px;">'.$listing_title.'</h3>
                    <h4 style="margin: 0;font-size: 25px;">'.$order_total.'</h4>
                  </div>
                  <div style="shipping-info-head1">
                    <h3 style="text-align: center;margin-bottom: 0;font-size: 30px;">'.$cfname.' '.$clname.'</h3>
                  </div>
                  <div style="text-align: center;">
                    <p style="font-size: 12px;font-weight: 200;margin-left: 10px;margin-top: 0px;font-size: 18px;">'.$event_date.'</p>
                  </div>
                  </div>
                  <div style="height: 250px;width:250px;background: #d1b26b;display: block;margin: auto;margin-top: 55px;border-radius: 50%;margin-bottom: 55px;">
                    <div>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Email</b>' .$billing_email.'</p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Host:</b>' .$fname.' '.$lname.' </p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Event:</b> ' .$listing_title.'</p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Location:</b>' .$friendly_address.' </p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Date:</b>' .$event_date.' </p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Ticket no:</b>' .$event_tickets_sold.' </p>
                      <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Booking no:</b>' .$order_id.' </p>
                    </div>
                  </div>
                </div>';
            $to = $billing_email;
            $subject = "Online event ticket recieved";
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );
            $reminder_user = get_user_meta(1,"reminder_users",true);
            $reminder_users = $reminder_user.','.$customer_user;
            update_user_meta(1,"reminder_users",$reminder_users);
            /**add meta**/
            update_user_meta($customer_user,"reminder_users_listing",$listing_title);
            update_user_meta($customer_user,"reminder_users_total",$order_total);
            update_user_meta($customer_user,"reminder_users_fname",$fname);
            update_user_meta($customer_user,"reminder_users_lname",$lname);
            update_user_meta($customer_user,"reminder_users_cfname",$cfname);
            update_user_meta($customer_user,"reminder_users_clname",$clname);
            update_user_meta($customer_user,"reminder_users_date",$event_date);
            update_user_meta($customer_user,"reminder_users_email",$billing_email);
            update_user_meta($customer_user,"reminder_users_address",$friendly_address);
            update_user_meta($customer_user,"reminder_users_sold",$event_tickets_sold);
            update_user_meta($customer_user,"reminder_users_order",$order_id);
        }
        if($listing_type == "service" || $listing_type == "rental"){
            $order_total = get_post_meta($order_id,"_order_total",true);
            
            $customer_user = get_post_meta($order_id,"_customer_user",true);
            $cfname = get_post_meta($order_id,"_billing_first_name",true);
            $clname = get_post_meta($order_id,"_billing_last_name",true);
            
            $billing_email = get_post_meta($order_id,"_billing_email",true);
            
            $listing_title = get_post_meta($listing_id,"listing_title",true);
            $friendly_address = get_post_meta($listing_id,"_friendly_address",true);
            $event_date = get_post_meta($listing_id,"_event_date",true);
            
            $reminder_user2 = get_user_meta(1,"reminder_users2",true);
            $reminder_users2 = $reminder_user2.','.$customer_user;

            update_user_meta(1,"reminder_users2",$reminder_users2);
            /**add meta**/
            update_user_meta($customer_user,"reminder_users_listing2",$listing_title);
            update_user_meta($customer_user,"reminder_users_total2",$order_total);
            update_user_meta($customer_user,"reminder_users_cfname2",$cfname);
            update_user_meta($customer_user,"reminder_users_clname2",$clname);
            update_user_meta($customer_user,"reminder_users_date2",$event_date);
            update_user_meta($customer_user,"reminder_users_email2",$billing_email);
            update_user_meta($customer_user,"reminder_users_address2",$friendly_address);
            update_user_meta($customer_user,"reminder_users_order2",$order_id);
        }
    }
}
add_action( 'woocommerce_order_status_completed', 'booking_payments_recieved_send_ticket_to_customer', 10, 1 );
add_action( 'woocommerce_payment_complete', 'booking_payments_recieved_send_ticket_to_customer', 10, 1 );
add_action( 'woocommerce_checkout_update_order_meta', 'booking_payments_recieved_send_ticket_to_customer', 10, 1 );
/************************* Reminder emails send to customer for booking before one day *******************************/
function reminder_emails_send_customer() {
    /***** Email only in case of Event reminder ****/
    $reminder_users = get_user_meta(1,"reminder_users",true);
    $reminder_users_trim = ltrim($reminder_users, ',');
    $reminder_users_arr = explode(",",$reminder_users_trim);
    for($i=0; $i<count($reminder_users_arr); $i++) {
        $event_date = get_user_meta($reminder_users_arr[$i],"reminder_users_date",true);
        if($event_date) {
            $today = date("m/d/Y h:i").'T<br/>';
            $event_before = date( 'm/d/Y h:i', strtotime( $event_date . ' -1 day' ) ).'E<br/>';
            $event_before_ex = explode(" ", $event_before);
            $today_ex = explode(" ", $today);
            if($today_ex[0] >= $event_before_ex[0]) {
                $listing_title = get_user_meta($reminder_users_arr[$i],"reminder_users_listing",true);
                $order_total = get_user_meta($reminder_users_arr[$i],"reminder_users_total",true);
                $fname = get_user_meta($reminder_users_arr[$i],"reminder_users_fname",true);
                $clname = get_user_meta($reminder_users_arr[$i],"reminder_users_lname",true);
                $cfname = get_user_meta($reminder_users_arr[$i],"reminder_users_cfname",true);
                $lname = get_user_meta($reminder_users_arr[$i],"reminder_users_clname",true);
                $event_date = get_user_meta($reminder_users_arr[$i],"reminder_users_date",true);
                $billing_email = get_user_meta($reminder_users_arr[$i],"reminder_users_email",true);
                $friendly_address = get_user_meta($reminder_users_arr[$i],"reminder_users_address",true);
                $event_tickets_sold = get_user_meta($reminder_users_arr[$i],"reminder_users_sold",true);
                $order_id = get_user_meta($reminder_users_arr[$i],"reminder_users_order",true);
    
                $message = '<div style="height: 600px;width: 300px;background:whitesmoke;box-shadow: 0px 0px 100px rgba(0,0,0,0.2);display:block;margin: auto;margin-top: 60px;border-radius: 40px;border: 15px solid #b87a29;">
                      <h2 style="text-align:center;">Event Booking Ticket Reminder</h2>
                      <hr/>
                      <div class="pass">
                      <div style="text-align: center;">
                        <h3 style="margin: 0;font-size: 18px;">'.$listing_title.'</h3>
                        <h4 style="margin: 0;font-size: 25px;">'.$order_total.'</h4>
                      </div>
                      <div style="shipping-info-head1">
                        <h3 style="text-align: center;margin-bottom: 0;font-size: 30px;">'.$cfname.' '.$clname.'</h3>
                      </div>
                      <div style="text-align: center;">
                        <p style="font-size: 12px;font-weight: 200;margin-left: 10px;margin-top: 0px;font-size: 18px;">'.$event_date.'</p>
                      </div>
                      </div>
                      <div style="height: 250px;width:250px;background: #d1b26b;display: block;margin: auto;margin-top: 55px;border-radius: 50%;margin-bottom: 55px;">
                        <div>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Email</b>' .$billing_email.'</p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Host:</b>' .$fname.' '.$lname.' </p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Event:</b> ' .$listing_title.'</p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Location:</b>' .$friendly_address.' </p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Date:</b>' .$event_date.' </p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Ticket no:</b>' .$event_tickets_sold.' </p>
                          <p style="font-size: 16px;margin: 7px;"><b style="font-size: 16px;margin-right: 5px;">Booking no:</b>' .$order_id.' </p>
                        </div>
                      </div>
                    </div>';
                $to = $billing_email;
                $subject = "Event booking reminder";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail( $to, $subject, $message, $headers );
                $reminder_users1 = get_user_meta(1,"reminder_users",true);
                $remstr = ','.$reminder_users_arr[$i];
                $reminder_users_trim1 = str_replace($remstr, '', $reminder_users1);
                update_user_meta(1,"reminder_users",$reminder_users_trim1);
            }
        }
    }
    /***** Email only in case of service/rental reminder ****/
    $reminder_users2 = get_user_meta(1,"reminder_users2",true);
    $reminder_users_trim2 = ltrim($reminder_users2, ',');
    $reminder_users_arr2 = explode(",",$reminder_users_trim2);
    for($i=0; $i<count($reminder_users_arr2); $i++) {
        $event_date2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_date2",true);
        if($event_date2) {
            $today = date("m/d/Y h:i");
            $event_before2 = date( 'm/d/Y h:i', strtotime( $event_date2 . ' -1 day' ) );
            $event_before_ex2 = explode(" ", $event_before2);
            $today_ex2 = explode(" ", $today);
            if($today_ex2[0] >= $event_before_ex2[0]) {
                $listing_title2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_listing2",true);
                $order_total2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_total2",true);
                $cfname2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_cfname2",true);
                $lname2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_clname2",true);
                $event_date2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_date2",true);
                $billing_email2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_email2",true);
                $friendly_address2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_address2",true);
                $order_id2 = get_user_meta($reminder_users_arr2[$i],"reminder_users_order2",true);
    
                $message2 = '<div class="services-rental">
                    Hi, '.$cfname2.' '.$clname2.'<br/><br/>
                    Just a friendly reminder about your coming booking, please find below details:<br/>
                    Booking Title : '.$listing_title2.'<br/>
                    Order Total : '.$order_total2.'<br/>
                    Booking Date : '.$event_date2.'<br/>
                    Address : '.$friendly_address2.'<br/>
                    Booking No. : '.$order_id2.'<br/><br/>
                    Best,<br/>
                    LONCANI Beauty Marketplace.
                    
                </div>';
                $to = $billing_email2;
                $subject = "Online booking reminder";
                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail( $to, $subject, $message2, $headers );
                $reminder_users1 = get_user_meta(1,"reminder_users2",true);
                $remstr = ','.$reminder_users_arr2[$i];
                $reminder_users_trim1 = str_replace($remstr, '', $reminder_users1);
                update_user_meta(1,"reminder_users2",$reminder_users_trim1);
            }
        }
    }

}
add_action("wp_head","reminder_emails_send_customer");
/******************** WP default search widget only search posts ******************/
if (!is_admin()) {
    function wpb_search_filter($query) {
        if ($query->is_search) {
            $query->set('post_type', 'post');
        }
        return $query;
    }
    add_filter('pre_get_posts','wpb_search_filter');
}
/******** Add extra descriptions to menus *******/
function extra_desc_for_menus( $item_output, $item, $depth, $args ) {
    if ( !empty( $item->description ) ) {
        $item_output = str_replace( $args->link_after . '</a>', '<span class="menu-item-description">' . $item->description . '</span>' . $args->link_after . '</a>', $item_output );
    }
    return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'extra_desc_for_menus', 10, 4 );
/********* Images resizing for shop/products pages **********/
add_filter( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : array(800,800) );
/****** Help and support menu at dokan dashboard ******/
add_filter( 'dokan_dashboard_nav_common_link', 'dokan_dashboard_nav_common_link_fun' );
function dokan_dashboard_nav_common_link_fun($common_links){
    $common_links ='
        <li class="helpnsupport"><a href="#"><i class="fa fa-question-circle"></i> Help</a></li>
        <li class="dokan-common-links dokan-clearfix">
            <a title="' . __( 'Visit Store', 'dokan-lite' ) . '" class="tips" data-placement="top" href="' . dokan_get_store_url( dokan_get_current_user_id() ) .'" target="_blank"><i class="fa fa-external-link"></i></a>
            <a title="' . __( 'Edit Account', 'dokan-lite' ) . '" class="tips" data-placement="top" href="' . dokan_get_navigation_url( 'edit-account' ) . '"><i class="fa fa-user"></i></a>
            <a title="' . __( 'Log out', 'dokan-lite' ) . '" class="tips" data-placement="top" href="' . wp_logout_url( home_url() ) . '"><i class="fa fa-power-off"></i></a>
        </li>
    ';
    return($common_links);
}
/************** If listing published then update its featured image to related product featured image *****************/
add_action( 'transition_post_status', 'update_feat_img_function', 10, 3 );
function update_feat_img_function( $newstatus, $oldstatus, $post ) {
    if ( ( $newstatus == 'publish' ) && ( $post->post_type == 'listing' ) ) {
        $image_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        $product_id = get_post_meta($post->ID, "product_id", true);
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        $img_id = $attachment[0];
        set_post_thumbnail( $product_id, $img_id );
    } else {
        return;
    }
}
/************ Add aq_resizer to resize equal images on category pages ***********/
add_action("wp_head","aq_resizer_to_category_page");
function aq_resizer_to_category_page() {
    if( is_product_category() ) {
      require_once('aq_resizer.php');
    }
}

/* ---------------------- Additional ------------------------ */

/***********  Partial payment for booking   ************************/

// Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'cron_daily_service' ) ) {
    wp_schedule_event( time(), 'daily', 'cron_daily_service' );
}

// Hook into that action that'll fire every three minutes
add_action( 'cron_daily_service', 'cron_daily_service_func');
function cron_daily_service_func() {

  global $wpdb;

  // get remaining partial payment
  $results = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_value = 'remaining' OR meta_value = 'full'", ARRAY_A );

  foreach ($results as $value) {
    $order_id =  $value['post_id'];

    // get booking date
    $booking_data = $wpdb->get_results( "select * from wp_bookings_calendar where order_id = ".$order_id."");

    $end_date = $booking_data[0]->date_end;
    $booking_date = date('Y-m-d', strtotime($end_date));     
    //$next_date = date('Y-m-d', strtotime($end_date . "+1 days"));

    $current_date = date('Y-m-d');

    if ($current_date == $booking_date) {

        $order_key = get_post_meta($order_id, '_order_key', true);
        $stripe_customer_id = get_post_meta($order_id, '_stripe_customer_id', true);
        $stripe_source_id = get_post_meta($order_id, '_stripe_source_id', true);
        $stripe_intent_id = get_post_meta($order_id, '_stripe_intent_id', true);
        $stripe_currency = get_post_meta($order_id, '_order_currency', true);
        $billing_first_name = get_post_meta($order_id, '_billing_first_name', true);
        $billing_last_name = get_post_meta($order_id, '_billing_last_name', true);
        $full_name = $billing_first_name.' '.$billing_last_name;
        $billing_email = get_post_meta($order_id, '_billing_email', true);
        $booking_option = get_post_meta($order_id, '_booking_option', true);
        $listing_id = get_post_meta($order_id, 'listing_id', true);
        $listing_name = get_post_meta($listing_id, 'listing_title', true);
        //$listing_type = get_post_meta( $listing_id, '_listing_type', true );
        $booking_id = get_post_meta($order_id, 'booking_id', true);
        $partial_payment_status = get_post_meta( $order_id, '_partial_payment_status', true );
        
        $total_cost = get_post_meta($order_id, '_total_cost', true);
        
        $owner_id = get_post_meta($order_id, 'owner_id', true);
        $owner_stripe_customer_id = get_user_meta($owner_id, 'wp__stripe_customer_id', true);
        $owner_stripe_vendor_id = get_user_meta($owner_id, 'dokan_connected_vendor_id', true);
        $owner_email = get_user_meta($owner_id, 'billing_email', true);
        $owner_first_name = get_user_meta($owner_id, 'first_name', true);
        $owner_last_name = get_user_meta($owner_id, 'last_name', true);
        $owner_full_name = $owner_first_name.' '.$owner_last_name;

        /*if ($remaining_cost == '0') {
          $remaining_cost = $total_cost;
        }else{
          $remaining_cost = $remaining_cost1;
        }*/

        if (!empty($stripe_customer_id)) {

          // Get Stripe Key


          $stripe_details = get_option('woocommerce_dokan-stripe-connect_settings');
          //$stripe_details = get_option('woocommerce_stripe_settings');

          $testmode = $stripe_details['testmode'];
          if ($testmode == 'yes') {
              $publishable_key = $stripe_details['test_publishable_key'];
              $secret_key = $stripe_details['test_secret_key'];
          } else {
              $publishable_key = $stripe_details['publishable_key'];
              $secret_key = $stripe_details['secret_key'];
          }

          /*echo "<br>".$publishable_key;
          echo "<br>".$secret_key;*/

          // Connect with stripe API
          $admin_rate = (float) get_option('listeo_commission_rate');

          $total_cost_percent = ($admin_rate / 100) * $total_cost;
          $total_cost_after_commissoion = $total_cost - $total_cost_percent;

          //echo "<br>". $total_cost_after_commissoion = (float) $total_cost * $admin_rate;

          $admin_email = get_bloginfo('admin_email');

          \Stripe\Stripe::setApiKey($secret_key);
          try { 
           
            if ($booking_option != 'full') {

              $remaining_cost = get_post_meta($order_id, '_remaining_cost', true);

              $customer = \Stripe\Customer::retrieve($stripe_customer_id );
              $cents =  (int) ( ( (string) ( $remaining_cost * 100 ) ) );

              \Stripe\PaymentIntent::create([
                //'currency' => 'CAD',

                //'source'          => $stripe_source_id,
                'customer'        => $stripe_customer_id,
                'amount'          => $cents,
                'currency'        => 'CAD',
                'description'     =>'LONCANI - Partial Payment - order '.$order_id,
                'capture_method'  => 'automatic',
                //'off_session'     => true,
                'confirm'         => true,
                'payment_method_types' => ['card'],
              ]);

              // Update partial payment status

              update_post_meta( $order_id, '_partial_payment_status', 'success' );
              update_post_meta( $order_id, 'cron_partial_payment_date', date('d/m/Y h:m:s') );

              // Send email for user
              $user_mail_temp='<html>   
                <body style="background:#F6F6F6; >
                  <div style="background:#F6F6F6; >
                  <table width="600" height="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td valign="top" align="center" style="padding:20px 0 20px 0">
                   <tr>
                    <td bgcolor="#FFF">
                      <p>Hello '.$full_name.',</p>
                      <p>Your listing booking was completed. Remaining payment is deducted in your account. Please check below details for your booking.
                    </td>
                   </tr>
                  <table width="600" cellspacing="0" cellpadding="10" border="0" bgcolor="FFFFFF" style="border:1px solid #E0E0E0;background:#F6F6F6;">
                  <tbody>
                  <tr>
                  <td align="center" style="text-align: center;" bgcolor="" valign="top" color:#fff; ><a href="'.site_url().'" style ="text-decoration:none;color:#96588a;font-weight:normal"><div class=""><img height="" width="" src="'.get_stylesheet_directory_uri().'/asset/images/logo.png" alt="Loncani"></div></a>
                  </td>
                  </tr>
                  <tr>
                  <td style="border-top: 1px solid #b88e29;border-bottom: 1px solid #b88e29;background:#b88e29;padding: 25px 40px;color: #fff;"><span style="font-size: 30px;color:#fff;">Partial Payment Details</span></td>
                  </tr>
                  <tr>
                  <td style="padding: 20px 40px;"><table width="600" cellspacing="3" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td width="170" style=""><strong>Name :</strong></td>
                  <td>'.$full_name.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Email Address :</strong></td>
                  <td><a href="mailto:'.$billing_email.'">'.$billing_email.'</a></td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Order Number :</strong></td>
                  <td>'.$order_id.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Listing Name :</strong></td>
                  <td>'.$listing_name.'</td>
                  </tr>    
                  <tr>
                  <td width="170" style=""><strong>Price :</strong></td>
                  <td style="vertical-align: top;">$'.$remaining_cost.'</td>
                  </tr>
                  </tbody></table></td>
                  </tr>
                  <td align="center" valign="middle" bgcolor="#b88e29" style="text-align: center; color: #fff; font-size: 14px;">
                  <div id="copyright" class="clr" role="contentinfo"> Copyright 2020 - <span class="copyright-company"><a href="'.site_url().'" style="color: #fff;" >Loncani</a></span>. All rights reserved.</div>
                  </td>
                  <tr>
                  </tbody>
                  </table></td>
                  </tr>
                  </tbody>
                  </table>
                  </div> 
                  </body>
                  </html>';
                
              $to = $billing_email;
              $subject = "Partial payment for ORDER-".$order_id;
              $message = $user_mail_temp;
              $from = $admin_email;
              $headers= "From: Loncani <".$admin_email.">\r\n"; 
              $headers .= "MIME-Version: 1.0\r\n";
              $headers .= "Content-Type: text/html; charset=UTF-8";
              mail($to,$subject,$message,$headers); 

             // Send email for admin
              $admin_user_mail_temp='<html>    
                <body style="background:#F6F6F6; >
                  <div style="background:#F6F6F6; >
                  <table width="600" height="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td valign="top" align="center" style="padding:20px 0 20px 0">
                   <tr>
                    <td bgcolor="#FFF">
                      <p>Hello Admin,</p>
                      <p>Listing booking completed for '.$full_name.'. Remaining payment is deducted for there account. Please check below booking details.
                    </td>
                   </tr>
                  <table width="600" cellspacing="0" cellpadding="10" border="0" bgcolor="FFFFFF" style="border:1px solid #E0E0E0;background:#F6F6F6;">
                  <tbody>
                  <tr>
                  <td align="center" style="text-align: center;" bgcolor="" valign="top" color:#fff; ><a href="'.site_url().'" style ="text-decoration:none;color:#96588a;font-weight:normal"><div class=""><img height="" width="" src="'.get_stylesheet_directory_uri().'/asset/images/logo.png" alt="Loncani"></div></a>
                  </td>
                  </tr>
                  <tr>
                  <td style="border-top: 1px solid #b88e29;border-bottom: 1px solid #b88e29;background:#b88e29;padding: 25px 40px;color: #fff;"><span style="font-size: 30px;color:#fff;">Partial Payment Details</span></td>
                  </tr>
                  <tr>
                  <td style="padding: 20px 40px;"><table width="600" cellspacing="3" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td width="170" style=""><strong>Name :</strong></td>
                  <td>'.$full_name.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Email Address :</strong></td>
                  <td><a href="mailto:'.$billing_email.'">'.$billing_email.'</a></td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Order Number :</strong></td>
                  <td>'.$order_id.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Listing Name :</strong></td>
                  <td>'.$listing_name.'</td>
                  </tr>    
                  <tr>
                  <td width="170" style=""><strong>Price :</strong></td>
                  <td style="vertical-align: top;">$'.$remaining_cost.'</td>
                  </tr>
                  </tbody></table></td>
                  </tr>
                  <td align="center" valign="middle" bgcolor="#b88e29" style="text-align: center; color: #fff; font-size: 14px;">
                  <div id="copyright" class="clr" role="contentinfo"> Copyright 2020 - <span class="copyright-company"><a href="'.site_url().'" style="color: #fff;" >Loncani</a></span>. All rights reserved.</div>
                  </td>
                  <tr>
                  </tbody>
                  </table></td>
                  </tr>
                  </tbody>
                  </table>
                  </div> 
                  </body>
                  </html>';
                
                $to1 = $admin_email;
                $subject1 = "Partial payment for ORDER-".$order_id;
                $message1 = $admin_user_mail_temp;
                $headers= "From: Loncani <".$admin_email.">\r\n"; 
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8";
                mail($to1,$subject1,$message1,$headers); 
            }
            
            $commition_cents =  (int) ( ( (string) ( $total_cost_after_commissoion * 100 ) ) );

            if (!empty($owner_stripe_vendor_id)) {
              $transfer = \Stripe\Transfer::create([
                'amount' => $commition_cents,
                'currency' => 'CAD',
                'destination' => $owner_stripe_vendor_id,
                'transfer_group' => $order_id,
                'description' =>'LONCANI - Payment Transfer to Owner - order '.$order_id,
              ]);
            }

            //$success = 1;

            // add comission for table = wp_listeo_core_commissions
              global $wpdb;

              /*$args['order_id'] = $order_id;
              $args['user_id'] = $owner_id;
              $args['booking_id'] = $booking_id;
              $args['listing_id'] = $listing_id;
              $args['status'] = 'paid';
              $args['type'] = 'booking';   
              $defaults = array(
                  'type'          => 'booking',
                  'date'          => current_time('mysql')
              );
              $args = wp_parse_args( $args, $defaults );
              $wpdb->insert( $wpdb->prefix . "listeo_core_commissions", (array) $args );*/
              
              $wpdb->update( $wpdb->prefix . "listeo_core_commissions", array('status'=>'paid', 'type'=>'booking'), array( 'order_id' => $order_id ) );

              // Send email for Owner  
              $owner_mail_temp='<html>   
                <body style="background:#F6F6F6; >
                  <div style="background:#F6F6F6; >
                  <table width="600" height="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td valign="top" align="center" style="padding:20px 0 20px 0">
                   <tr>
                    <td bgcolor="#FFF">
                      <p>Hello '.$owner_full_name.',</p>
                      <p>Your listing booking was completed. Please check below details for your booking.
                    </td>
                   </tr>
                  <table width="600" cellspacing="0" cellpadding="10" border="0" bgcolor="FFFFFF" style="border:1px solid #E0E0E0;background:#F6F6F6;">
                  <tbody>
                  <tr>
                  <td align="center" style="text-align: center;" bgcolor="" valign="top" color:#fff; ><a href="'.site_url().'" style ="text-decoration:none;color:#96588a;font-weight:normal"><div class=""><img height="" width="" src="'.get_stylesheet_directory_uri().'/asset/images/logo.png" alt="Loncani"></div></a>
                  </td>
                  </tr>
                  <tr>
                  <td style="border-top: 1px solid #b88e29;border-bottom: 1px solid #b88e29;background:#b88e29;padding: 25px 40px;color: #fff;"><span style="font-size: 30px;color:#fff;">Payment Details</span></td>
                  </tr>
                  <tr>
                  <td style="padding: 20px 40px;"><table width="600" cellspacing="3" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td width="170" style=""><strong>Name :</strong></td>
                  <td>'.$owner_full_name.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Email Address :</strong></td>
                  <td><a href="mailto:'.$owner_email.'">'.$owner_email.'</a></td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Order Number :</strong></td>
                  <td>'.$order_id.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Listing Name :</strong></td>
                  <td>'.$listing_name.'</td>
                  </tr>    
                  <tr>
                  <td width="170" style=""><strong>Price :</strong></td>
                  <td style="vertical-align: top;">$'.$total_cost_after_commissoion.'</td>
                  </tr>
                  </tbody></table></td>
                  </tr>
                  <td align="center" valign="middle" bgcolor="#b88e29" style="text-align: center; color: #fff; font-size: 14px;">
                  <div id="copyright" class="clr" role="contentinfo"> Copyright 2020 - <span class="copyright-company"><a href="'.site_url().'" style="color: #fff;" >Loncani</a></span>. All rights reserved.</div>
                  </td>
                  <tr>
                  </tbody>
                  </table></td>
                  </tr>
                  </tbody>
                  </table>
                  </div> 
                  </body>
                  </html>';
                
                $to1 = $owner_email;
                $subject1 = "Payment for ORDER-".$order_id;
                $message1 = $owner_mail_temp;
                $headers= "From: Loncani <".$admin_email.">\r\n"; 
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8";
                mail($to1,$subject1,$message1,$headers); 

             // Send email for admin
              $admin_owner_mail_temp='<html>    
                <body style="background:#F6F6F6; >
                  <div style="background:#F6F6F6; >
                  <table width="600" height="100%" cellspacing="0" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td valign="top" align="center" style="padding:20px 0 20px 0">
                   <tr>
                    <td bgcolor="#FFF">
                      <p>Hello Admin,</p>
                      <p>Listing booking completed for '.$full_name.'. Remaining payment is deducted for there account. Please check below booking details.
                    </td>
                   </tr>
                  <table width="600" cellspacing="0" cellpadding="10" border="0" bgcolor="FFFFFF" style="border:1px solid #E0E0E0;background:#F6F6F6;">
                  <tbody>
                  <tr>
                  <td align="center" style="text-align: center;" bgcolor="" valign="top" color:#fff; ><a href="'.site_url().'" style ="text-decoration:none;color:#96588a;font-weight:normal"><div class=""><img height="" width="" src="'.get_stylesheet_directory_uri().'/asset/images/logo.png" alt="Loncani"></div></a>
                  </td>
                  </tr>
                  <tr>
                  <td style="border-top: 1px solid #b88e29;border-bottom: 1px solid #b88e29;background:#b88e29;padding: 25px 40px;color: #fff;"><span style="font-size: 30px;color:#fff;">Payment Transfer Details</span></td>
                  </tr>
                  <tr>
                  <td style="padding: 20px 40px;"><table width="600" cellspacing="3" cellpadding="0" border="0">
                  <tbody>
                  <tr>
                  <td width="170" style=""><strong>Name :</strong></td>
                  <td>'.$owner_full_name.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Email Address :</strong></td>
                  <td><a href="mailto:'.$owner_email.'">'.$owner_email.'</a></td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Order Number :</strong></td>
                  <td>'.$order_id.'</td>
                  </tr>
                  <tr>
                  <td width="170" style=""><strong>Listing Name :</strong></td>
                  <td>'.$listing_name.'</td>
                  </tr>    
                  <tr>
                  <td width="170" style=""><strong>Price :</strong></td>
                  <td style="vertical-align: top;">$'.$total_cost_after_commissoion.'</td>
                  </tr>
                  </tbody></table></td>
                  </tr>
                  <td align="center" valign="middle" bgcolor="#b88e29" style="text-align: center; color: #fff; font-size: 14px;">
                  <div id="copyright" class="clr" role="contentinfo"> Copyright 2020 - <span class="copyright-company"><a href="'.site_url().'" style="color: #fff;" >Loncani</a></span>. All rights reserved.</div>
                  </td>
                  <tr>
                  </tbody>
                  </table></td>
                  </tr>
                  </tbody>
                  </table>
                  </div> 
                  </body>
                  </html>';
                
                $to2 = $admin_email;
                $subject2 = "Payment transfer for ORDER-".$order_id;
                $message2 = $admin_owner_mail_temp;
                $headers= "From: Loncani <".$admin_email.">\r\n"; 
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8";
                mail($to2,$subject2,$message2,$headers); 

            } catch(Stripe_CardError $e) {
                $error1 = $e->getMessage();
            } catch (Stripe_InvalidRequestError $e) {
                $error1 = $e->getMessage();
            } catch (Stripe_AuthenticationError $e) {
                $error1 = $e->getMessage();
            } catch (Stripe_ApiConnectionError $e) {
                $error1 = $e->getMessage();
            } catch (Stripe_Error $e) {
                $error1 = $e->getMessage();
            } catch (Exception $e) {
                $error1 = $e->getMessage();
            }  
           // echo $error1;
          } else {
          //update_post_meta( $order_id, 'cron_partial_payment_date_empty', date('d/m/Y h:m') );
        }
    }
  }
}


// Onload check partial book date available or not
add_action('wp_ajax_check_date', 'check_date', 0);
add_action('wp_ajax_nopriv_check_date', 'check_date');
function check_date() {
  global $wpdb;
  $listing_id = $_POST['listing_id'];
  $select_date = $_POST['select_date'];
  $booking_data = $wpdb->get_results( "select * from wp_bookings_calendar where listing_id = ".$listing_id."");
  $select_date = date('Y-m-d', strtotime($select_date));     
  $msg = array();
  foreach ($booking_data as $value) {
    $end_date = $value->date_end;
    $order_id = $value->order_id;
    $end_book_date = date('Y-m-d', strtotime($end_date));  
    $partial_payment_status = get_post_meta( $order_id, '_partial_payment_status', true );   
    if ($select_date == $end_book_date && $partial_payment_status == 'remaining') {
        $msg['msg'] = 1;
    }
  }
  echo json_encode($msg);
  die();
}
/* ---------------------- Additional ------------------------ */

/*-- Edited by Ravi extra two buttons for provider side to update service status --*/

add_action( 'wp_ajax_custom_review_statusyes', 'custom_review_statusyes' );
function custom_review_statusyes() {
    if($_POST['action'] == "custom_review_statusyes"){
        $status_id = $_POST['status_id'];
        $listing_status_id = $_POST['listing_status_id'];
        
        if($status_id && $listing_status_id){
            update_post_meta($listing_status_id,"custom_listing_review_status",$status_id);
            echo '1';
            $message = '<div class="services-rental" style="margin:0 auto;max-width:500px;">
                    <h2 style="text-align:center;border-bottom:1px solid #b87a29;background:#eee;"><img src="https://loncani.ca/wp-content/uploads/2019/12/logo-1.png"></h2>
                    Hi, '.$_POST['client_name'].' 👋 ...<br/><br/>
                    Thank you for your recent booking on LONCANI. You’re Awesome 👏 <br/><br/>
                    Can you take a moment to leave a feedback about your recent experience? Your feedback will help the professional improve his offer and help our users to make the wise choices when their book with the same beautician!<br/><br/>
                    Please share your experinece and rate the service on the following booking:<br/><br/>
                    Booking : '.$_POST['listing_name'].'<br/><br/>
                    Please <a href="'.$_POST['listing_link'].'">click here</a> to add your reviews.<br/><br/>
                    Thank you for shopping with us!<br/><br/>
                    Best,<br/>
                    LONCANI Team<br/>
                    <a href="https://loncani.ca/">loncani.ca</a><br/>
                    <p align="center" valign="top" style="background:#b87a29;color:#fff;font-size:12px;font-weight:600;padding:10px 0">
				    		© 2020 by <span class="il">LONCANI</span>. All Right Reserved.
					</p>
                </div>';
            $to = $_POST['client_email'];
            $subject = "Tell us how you really feel about your experience with LONCANI?";
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $to, $subject, $message, $headers );
        }else{
            echo '0';
        }
    }
}

add_action( 'wp_ajax_custom_review_statusno', 'custom_review_statusno' );
function custom_review_statusno() {
    if($_POST['action'] == "custom_review_statusno"){
        $status_id = $_POST['status_id'];
        $listing_status_id = $_POST['listing_status_id'];
        if($status_id && $listing_status_id){
            update_post_meta($listing_status_id,"custom_listing_review_status",$status_id);
            echo '1';
        }else{
            echo '0';
        }
    }
}

/*-- Edited by Ravi extra two buttons for provider side to update service status end --*/