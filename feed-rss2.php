<?php
/**
 * LINE APP RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';

/**
 * Fires between the xml and rss tags in a feed.
 *
 * @since 4.0.0
 *
 * @param string $context Type of feed. Possible values include 'rss2', 'rss2-comments',
 *                        'rdf', 'atom', and 'atom-comments'.
 */
do_action( 'rss_tag_pre', 'rss2' );

function add_featured_image_url($output) {
    global $post;
    if ( has_post_thumbnail( $post->ID ) ){
        $output =  wp_strip_all_tags(wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ));
    }
    return $output;
}
add_filter('the_excerpt_rss', 'add_featured_image_url');

//remove class from the_post_thumbnail
function the_post_thumbnail_remove_class($output) {
		$alt = get_the_title($id); // gets the post thumbnail title
		$output = preg_replace('/class=".*?"/', '', $output);
		$output = preg_replace('/alt=".*?"/', 'alt="'.$alt.'"', $output);
				return $output;
}
add_filter('post_thumbnail_html', 'the_post_thumbnail_remove_class');
add_filter( 'wp_calculate_image_srcset', '__return_null' ); //remove srcset, sizes

function remove_thumbnail_dimensions( $html ) {
	$html = preg_replace( '/(width|height|sizes|srcset|class)=\"\d*\"\s/', "", $html ); return $html;
}
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );

add_filter('the_content', function( $content ){
 //--Remove all inline styles--
 $content = preg_replace('/ size=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ srcset=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ sizes=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ style=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ width=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ height=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('/ class=("|\')(.*?)("|\')/','',$content);
 $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
 return $content;
}, 20);

add_image_size( 'custom-size', 120, 140 );
$thumb_id = get_post_thumbnail_id( $post->ID );
if ( '' != $thumb_id ) {
  $thumb_url_array  = wp_get_attachment_image_src( $thumb_id, 'thumbnail', true );
  $image = $post[$i]['thumbnail'] = $thumb_url_array[0];
	$altImage = $post[$i]['thumbnail'] = get_the_title($id);
}

//static value here
$lineNativeCountry = "TW";
$lineLanguage = "zh";
$lineCountry = "taiwan";
?>

<articles>
	<UUID><?php echo wp_generate_uuid4(); ?> </UUID> 
	<time> <?php echo strtotime(current_time( 'mysql', 1 )) . "000";?> </time>
<?php while( have_posts()) : the_post(); ?>
	<article>
	<ID><?php global $post;	if(!empty($post)) {	echo $page_id = $post->ID; }?></ID>
	<nativeCountry><?php echo $lineNativeCountry; ?></nativeCountry>
	<language><?php echo $lineLanguage; ?></language>
	<publishCountries>
		<country><?php echo $lineCountry; ?></country>
	</publishCountries>

	<startYmdtUnix><?php $startDateUnix = strtotime(mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false)) . "000"; echo $startDateUnix;?></startYmdtUnix>
  	<endYmdtUnix><?php $startDateUnix = strtotime( mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false)); $newStartDateUnix = strtotime('+ 1 year', $startDateUnix); echo $newStartDateUnix . "000";?></endYmdtUnix>

 	<title><?php the_title_rss() ?></title>
	<category><?php $categories = get_the_category(); $category_names = array(); foreach ($categories as $category){ $category_names[] = $category->cat_name;}echo implode(', ', $category_names);?></category>
	<publishTimeUnix><?php $publishTime = strtotime(mysql2date('D, d M Y H:i:s +0000', get_post_time('GMT'), false)) . "000"; echo $publishTime;?></publishTimeUnix>
	<contentType>0</contentType>


  <thumbnail><img src="<?php echo $image;?>" alt="<?php echo $altImage; ?>" /></thumbnail>

	<contents>
    <image>
     <title><?php wp_title_rss(); ?></title>
		 <url><?php echo the_excerpt_rss(); ?></url>
     <thumbnail><img src="<?php echo $image;?>" alt="<?php echo $altImage; ?>" /></thumbnail>

    </image>
    <text>
		<?php $content = get_the_content_feed('rss2'); ?><?php if ( strlen( $content ) > 0 ) : ?><content><![CDATA[<?php echo html_entity_decode(strip_tags( $content, '<p><a><table><tbody><tr><td><img><ul><ol><li><u><em><strong><h1><h2><h3><h4><h5><h6><br>' )); ?>]]></content><?php else : ?><content><![CDATA[<?php the_excerpt_rss(); ?>]]></content><?php endif; ?>
    </text>
	</contents>
	<sourceUrl><?php echo get_permalink( $post->ID ); ?></sourceUrl>
</article>
<?php endwhile; ?>
</articles>
