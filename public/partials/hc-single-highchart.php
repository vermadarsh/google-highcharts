<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit, if accessed directly

get_header();
$chartid = $post->ID;
$title = get_post_meta( $chartid, 'chart_title', true );
$default_currency = get_post_meta( $chartid, 'default_currency', true );
?>
    <div id="main-content">
        <div class="container">
            <div id="content-area" class="clearfix">
                <div id="left-area">
                    <article id="post-<?php echo $chartid;?>" <?php post_class();?>>
                        <h1 class="entry-title main_title"><?php echo $post->post_title;?></h1>
                        <div class="entry-content">
                            <?php echo do_shortcode( '[highchart chartid="' . $chartid . '" currency="' . $default_currency . '" title="' . $title . '"]' );?>
                        </div> <!-- .entry-content -->
                    </article> <!-- .et_pb_post -->
                </div> <!-- #left-area -->
            </div> <!-- #content-area -->
        </div> <!-- .container -->
    </div>
<?php
get_footer();