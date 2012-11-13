<?php
/*
Plugin Name: Annual Archive
Text Domain: anarch
Domain Path: /languages
Plugin URI: http://plugins.twinpictures.de/plugins/annual-archive-widget/
Description: Display daily, weekly, monthly or annual archives with a sidebar widget or shortcode.
Version: 1.3
Author: Twinpictures
Author URI: http://www.twinpictures.de/
License: GPL2
*/

/*  Copyright 2012 Twinpictures (www.twinpictures.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class AnualArchives extends WP_Widget {

	function AnualArchives() {
		load_plugin_textdomain( 'anarch', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		$widget_ops = array('classname' => 'widget_anual_archive', 'description' => __( 'Display weekly, monthly or yearly post archives.', 'anarch') );
		$this->WP_Widget('widget_anual_archive', __('Annual Archive', 'anarch'), $widget_ops);

	}

	function widget( $args, $instance ) {
		extract($args);
		$c = $instance['count'] ? '1' : '0';
		//$d = $instance['dropdown'] ? '1' : '0';
		$format = empty($instance['format']) ? 'html' : apply_filters('widget_type', $instance['format']);
		$type = empty($instance['type']) ? 'yearly' : apply_filters('widget_type', $instance['type']);
		$before = empty($instance['before']) ? '' : apply_filters('widget_type', $instance['before']);
		$after = empty($instance['after']) ? '' : apply_filters('widget_type', $instance['after']);
		$limit = apply_filters('widget_limit', $instance['limit']);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Annual Archive', 'anarch') : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		if ($format == 'option') {
			$dtitle = __('Select Year', 'anarch');
			if ($type == 'monthly'){
				$dtitle = __('Select Month', 'anarch');
			}
			else if($type == 'weekly'){
				$dtitle = __('Select Week', 'anarch');
			}
			else if($type == 'daily'){
				$dtitle = __('Select Day', 'anarch');
			}
			else if($type == 'postbypost' || $type == 'alpha'){
				$dtitle = __('Select Post', 'anarch');
			}
		?>
		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo esc_attr(__($dtitle, 'anarch')); ?></option> <?php wp_get_archives(apply_filters('widget_archive_dropdown_args', array('type' => $type, 'format' => 'option', 'show_post_count' => $c, 'limit' => $limit))); ?> </select>
		<?php
		} else {
		?>
		<ul>
		<?php wp_get_archives(apply_filters('widget_archive_args', array('type' => $type, 'limit' => $limit, 'format' => $format, 'before' => $before, 'after' => $after, 'show_post_count' => $c))); ?>
		</ul>
		<?php
		}

		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = array_merge($old_instance, $new_instance);		
		return array_map('mysql_real_escape_string', $instance);
    }

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$title = strip_tags($instance['title']);
		$count = $instance['count'] ? 'checked="checked"' : '';
		//$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
		$format = empty($instance['format']) ? 'html' : strip_tags($instance['format']);
		$before = empty($instance['before']) ? '' : $instance['before'];
		$after = empty($instance['after']) ? '' : $instance['after'];
		$type = empty($instance['type']) ? ' ' : strip_tags($instance['type']); 
		$limit = strip_tags($instance['limit']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'anarch'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label><input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" />&nbsp;&nbsp;<?php _e('Show post counts', 'anarch'); ?></label></p>
		<p>
			<label><?php _e('Archive type:', 'anarch'); ?> <select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
			<?php
			$types_arr = array(
				'daily' => __('Daily', 'anarch'),
				'weekly' => __('Weekly', 'anarch'),
				'monthly' => __('Monthly', 'anarch'),
				'yearly' => __('Yearly', 'anarch'),
				'postbypost' => __('Post By Post', 'anarch'),
				'alpha' => __('Alpha', 'anarch')
			);
			foreach($types_arr as $key => $value){
				$selected = '';
				if($key == $type || (!$type && $key == 'yearly')){
					$selected = 'SELECTED';
				}
				echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			?>
			</select></lable>
		</p>
		
		<p>
			<label><?php _e('Format:', 'anarch'); ?> <select name="<?php echo $this->get_field_name('format'); ?>" id="<?php echo $this->get_field_id('format'); ?>">
			<?php
			$format_arr = array(
				'html' => __('HTML', 'anarch'),
				'option' => __('Option', 'anarch'),
				'link' => __('Link', 'anarch'),
				'custom' => __('Custom', 'anarch')
			);
			foreach($format_arr as $key => $value){
				$selected = '';
				if($key == $format || (!$format && $key == 'html')){
					$selected = 'SELECTED';
				}
				echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			?>
			</select></lable><br/>
			<span class="description"><a href="http://codex.wordpress.org/Function_Reference/wp_get_archives#Parameters" target="_blank"><?php _e('Format details'); ?></a></span>
		</p>
		<p><label for="<?php echo $this->get_field_id('before'); ?>"><?php _e('Text Before Link:', 'anarch'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('before'); ?>" name="<?php echo $this->get_field_name('before'); ?>" type="text" value="<?php echo $before; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('after'); ?>"><?php _e('Text After Link:', 'anarch'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('after'); ?>" name="<?php echo $this->get_field_name('after'); ?>" type="text" value="<?php echo $after; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of archives to display:', 'anarch'); ?></label> <input class="widefat" style="width: 50px;" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($limit); ?>" /></p>
		<?php
	}
}

// register Archive By Year widget
add_action('widgets_init', create_function('', 'return register_widget("AnualArchives");'));

// the shortcode
function annual_archive($atts, $content=null) {
	extract(shortcode_atts(array(
		'type' => 'yearly',
		'limit' => '',
		'format' => 'html', //html, option, link, custom
		'before' => '',
		'after' => '',
		'showcount' => '0',
		'tag' => 'ul',
	), $atts));
	
	if ($format == 'option') {
		$dtitle = __('Select Year', 'anarch');
		if ($type == 'monthly'){
			$dtitle = __('Select Month', 'anarch');
		}
		else if($type == 'weekly'){
			$dtitle = __('Select Week', 'anarch');
		}
		else if($type == 'daily'){
			$dtitle = __('Select Day', 'anarch');
		}
		else if($type == 'postbypost' || $type == 'alpha'){
			$dtitle = __('Select Post', 'anarch');
		}
		$arc = '<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"> <option value="">'.esc_attr(__($dtitle, 'anarch')).'</option>';
		$arc .= wp_get_archives(array('type' => $type, 'limit' => $limit, 'format' => 'option', 'show_post_count' => $showcount, 'echo' => 0)).'</select>';
	} else {
		$arc = '<'.$tag.'>';
		//$arc .= wp_get_archives(array('type' => $type, 'limit' => $limit, 'show_post_count' => $showcount, 'echo' => 0));
		$arc .= wp_get_archives(array('type' => $type, 'limit' => $limit, 'format' => $format, 'before' => $before, 'after' => $after, 'show_post_count' => $showcount, 'echo' => 0));
		$arc .= '</'.$tag.'>';
	}
	return $arc;
}

add_shortcode('archives', 'annual_archive');
	
?>