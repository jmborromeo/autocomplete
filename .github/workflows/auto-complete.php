<?php

// Auto Complete
function theme_autocomplete_dropdown_shortcode( $atts ) {
	if ( is_front_page() || is_home() || is_404() ) {
	$output .= '<div class="elementor-element elementor-element-2d9a635c elementor-search-form--skin-classic elementor-search-form--button-type-icon elementor-search-form--icon-search elementor-widget elementor-widget-search-form" data-id="7c98ef41" data-element_type="widget" data-settings="{&quot;skin&quot;:&quot;classic&quot;}" data-widget_type="search-form.default">';
	} else {
		$output = '<div class="elementor-element elementor-element-7c98ef41 elementor-search-form--skin-classic elementor-search-form--button-type-icon elementor-search-form--icon-search elementor-widget elementor-widget-search-form" data-id="7c98ef41" data-element_type="widget" data-settings="{&quot;skin&quot;:&quot;classic&quot;}" data-widget_type="search-form.default">';
	}
	$output .=	'<div class="elementor-search-form">
					<div class="elementor-widget-container">
						<div class="elementor-search-form__container">
							<input class="elementor-search-form__input" type="search" name="autocomplete" id="autocomplete" value="" placeholder="What can we help you find?">
							<button class="elementor-search-form__submit" type="submit" title="Search" aria-label="Search">
								<i class="fa fa-search" aria-hidden="true"></i>
								<span class="elementor-screen-only">Search</span>
							</button>
						</div>
					</div>
				</div>
			</div>';

	return $output;
}
add_shortcode( 'autocomplete', 'theme_autocomplete_dropdown_shortcode' );

// Auto Complete
function theme_autocomplete_js() {
	if ( is_home() || is_front_page() || is_404() ) :
	$args1 = array(
	    'taxonomy' => 'tool-category',
	    'orderby' => 'name',
	    'order' => 'DESC',
	);

	$args2 = array(
	    'post_type'=> 'tools',
	    'orderby'    => 'title',
	    'post_status' => 'publish',
	    'posts_per_page' => -1
	);

	$args3 = array(
	    'post_type'=> 'post',
	    'orderby'    => 'title',
	    'post_status' => 'publish',
	    'posts_per_page' => -1
	);

	$categories = get_categories( $args1 );
	$post_item = get_posts( $args2 );
	$blog_item = get_posts( $args3 );

	if( $post_item ) :
		foreach( $categories as $k => $category ) {
			$source_cat[$k]['ID'] = $category->term_id;
			$source_cat[$k]['label'] = $category->name;
			$source_cat[$k]['name'] = $category->name;
			$source_cat[$k]['permalink'] = get_category_link( $category->term_id );
			$source_cat[$k]['type'] = 'categories';
		}
		foreach( $post_item as $k => $item ) {

			$post_tags = wp_get_post_terms($item->ID, 'product-tag');
			if ($post_tags) {
				foreach( $post_tags as $tag ) {
					$source_post[$k]['ID'] = $tag->term_id;
					$source_post[$k]['label'] = $item->post_title.' '.$tag->name;
					$source_post[$k]['name'] = $item->post_title;
					$source_post[$k]['permalink'] = get_permalink($item->ID);
					$source_post[$k]['type'] = 'tools';	
				}
			} else {
				$source_post[$k]['ID'] = $item->ID;
				$source_post[$k]['label'] = $item->post_title;
				$source_post[$k]['name'] = $item->post_title;
				$source_post[$k]['permalink'] = get_permalink($item->ID);
				$source_post[$k]['type'] = 'tools';	
			}
			
			foreach( $blog_item as $k => $content ) {
				$source_content[$k]['ID'] = $content->ID;
				$source_content[$k]['label'] = $content->post_title;
				$source_content[$k]['name'] = $content->post_title;
				$source_content[$k]['permalink'] = get_permalink($content->ID);
				$source_content[$k]['type'] = 'discussion';
			}
			
		}
		
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){

		var catObj = <?php echo json_encode( array_values( $source_cat ) ); ?>;
		var postObj = <?php echo json_encode( array_values( $source_post ) ); ?>;
		//var postTag = <?php //echo json_encode( array_values( $source_tags ) ); ?>;
		var contentObj = <?php echo json_encode( array_values( $source_content ) ); ?>;

		var merged = $.merge(catObj, postObj);
		var merged2 = $.merge(merged, contentObj)

	    $( "#autocomplete" ).autocomplete({
	        minLength: 1,
	        source: merged2,
	        select: function( event, ui ) {
	            var permalink = ui.item.permalink;
	           	window.location.replace(permalink);
	        }
	    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	    
	    	if ( item.type == 'categories' ) {
	    		var disabled = $('.ui-autocomplete').find('li.category');
	    		if (disabled.length) {
	    			var search_output = $( "<li>" ).append( "<a><span>" + item.name + "</span></a>").appendTo( ul );
	    		} else {
	    			$( "<li class='title'>" ).append( "<span>"+ item.type +"</span>").appendTo( ul );
	    			var search_output = $( "<li class='category'>" ).append( "<a><span>" + item.name + "</span></a>").appendTo( ul );
	    		}
	    	
	    	} else if ( item.type == 'discussion' ) {
	    		var disabled = $('.ui-autocomplete').find('li.discussion');
	    		if (disabled.length) {
	    			var search_output = $( "<li>" ).append( "<a><span>" + item.label + "</span></a>").appendTo( ul );
	    		} else {
	    			$( "<li class='title'>" ).append( "<span>"+ item.type +"</span>").appendTo( ul );
	    			var search_output = $( "<li class='discussion'>" ).append( "<a><span>" + item.label + "</span></a>").appendTo( ul );
	    		}
	    	
	    	} else {
	    		var disabled = $('.ui-autocomplete').find('li.tool');
	    		if (disabled.length) {
	    			var search_output = $( "<li>" ).append( "<a><span>" + item.name + "</span></a>").appendTo( ul );
	    		} else {
	    			$( "<li class='title'>" ).append( "<span>"+ item.type +"</span>").appendTo( ul );
	    			var search_output = $( "<li class='tool'>" ).append( "<a><span>" + item.name + "</span></a>").appendTo( ul );
	    		}
	    	}

	        return search_output;

	    };
		 
	});    
	</script>
	<?php
	endif;
	endif;
}
add_action( 'wp_footer', 'theme_autocomplete_js' );
?>
