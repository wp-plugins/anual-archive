<?php
/*
Plugin Name: Annual Archive
Plugin URI: http://www.twinpictures.de/anual-archive-widget
Description: Like the default Archive Widget, but grouped by year.
Version: 1.0
Author: Twinpictures
Author URI: http://www.twinpictures.de
License: GPL2
*/

/*  Copyright 2011 Twinpictures (www.twinpictures.de)

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
		$widget_ops = array('classname' => 'widget_anual_archive', 'description' => __( 'A monthly or yearly archive of your site&#8217;s posts') );
		$this->WP_Widget('widget_anual_archive', __('Annual Archive'), $widget_ops);

	}

	function widget( $args, $instance ) {
		extract($args);
		$c = $instance['count'] ? '1' : '0';
		$d = $instance['dropdown'] ? '1' : '0';
		$y = $instance['yearly'] ? 'monthly' : 'yearly';
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Annual Archive') : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		if ( $d ) {
			$dtitle = 'Select Year';
			if ( $y == 'monthly')
				$dtitle = 'Select Month';
		?>
		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo esc_attr(__($dtitle)); ?></option> <?php wp_get_archives(apply_filters('widget_anual_archive_dropdown_args', array('type' => $y, 'format' => 'option', 'show_post_count' => $c))); ?> </select>
		<?php
		} else {
		?>
		<ul>
		<?php wp_get_archives(apply_filters('widget_anual_archive_args', array('type' => $y, 'show_post_count' => $c))); ?>
		</ul>
		<?php
		}

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args( (array) $new_instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;
		$instance['yearly'] = $new_instance['yearly'] ? 1 : 0;
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'count' => 0, 'dropdown' => '') );
		$title = strip_tags($instance['title']);
		$count = $instance['count'] ? 'checked="checked"' : '';
		$dropdown = $instance['dropdown'] ? 'checked="checked"' : '';
		$yearly= $instance['yearly'] ? 'checked="checked"' : '';
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>
			<input class="checkbox" type="checkbox" <?php echo $count; ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show post counts'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Display as a drop down'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" <?php echo $yearly; ?> id="<?php echo $this->get_field_id('yearly'); ?>" name="<?php echo $this->get_field_name('yearly'); ?>" /> <label for="<?php echo $this->get_field_id('yearly'); ?>"><?php _e('Display as a monthly archive'); ?></label>
		</p>
		<?php
	}
}

// register Archive By Year widget
add_action('widgets_init', create_function('', 'return register_widget("AnualArchives");'));
?>