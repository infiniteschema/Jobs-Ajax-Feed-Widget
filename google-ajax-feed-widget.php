<?php
/**
 * Plugin Name: Google AJAX Feed Widget
 * Plugin URI: http://naveedakram.info/widgets/wordpress/google-ajax-feed-widget/
 * Description: This plugin adds Google AJAX Feed Widget to the list of available widgets. This widget can be used to show RSS feeds. This plugin is based on Google AJAX Feed API. Its advantage over the native RSS widget is it can fetch Secure(https) RSS Feeds.
 * Version: 1.0
 * Author: M Naveed Akram
 * Author URI: http://naveedakram.info/
 *
 */
 
 /*  Copyright 2011  M Naveed Akram  (email : mail@naveedakram.info)

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

add_action('widgets_init', 'google_ajax_feed_widget_init');

function google_ajax_feed_widget_init() {
	register_widget('GoogleAJAXFeed_Widget');
	add_action('wp_head', 'google_ajax_feed_wp_head');
	add_action('wp_footer', 'google_ajax_feed_wp_footer');
}

function google_ajax_feed_wp_head(){
	echo '<script src="http://www.google.com/jsapi" type="text/javascript"></script>';
}

function google_ajax_feed_wp_footer(){
	echo '<script type="text/javascript">google.load("feeds", "1");</script>';
}

class GoogleAJAXFeed_Widget extends WP_Widget {

	function __construct(){
		parent::WP_Widget( 'google-ajax-feed-widget', 'Google AJAX Feed', array( 'classname' => 'google-ajax-feed-widget', 'description' => __('Use this widget to add RSS Feeds. This widget uses Google AJAX Feed API') ) );
	}
	
	function widget($args, $instance){
		extract($args);
		$output = $this->output($instance);
		if(!$output)
			echo '';
		else{
			$title = apply_filters('widget_title', $instance['title']);
			echo $before_widget;
			if ($title)
				echo $before_title . $title . $after_title;
			echo $output;
			if ($instance['html_after'])
				echo $instance['html_after'];
			echo $after_widget;
		}
	}
	
	function output($options){
		$id = 'google-ajax-feed-widget_' . uniqid();	
		$feed_url = html_entity_decode($options['feed_url']);
		if(!$feed_url)
			return false;
		$num_entries = $options['num_entries']?$options['num_entries']:5;
		$output = "
		<script type=\"text/javascript\">  
			google.setOnLoadCallback(function(){
				var feed = new google.feeds.Feed('{$feed_url}');
				var numEntries = {$num_entries};
				feed.setNumEntries(numEntries);
				feed.load(function(result){
					if (!result.error) {
						var container = document.getElementById('{$id}');
						var list = document.createElement('ul');
						list.setAttribute('class', 'google-ajax-feed-list');
						container.innerHTML = '';
						for (var i = 0; i < result.feed.entries.length; i++) {
							var entry = result.feed.entries[i];
							var item = document.createElement('li');
							item.setAttribute('class', 'google-ajax-feed-item');
							var link = document.createElement('a');
							link.innerHTML = entry.title;
							link.href = entry.link;
							link.setAttribute('target', '{$options[link_target]}')
							var text = document.createElement('span');
							text.setAttribute('class', 'google-ajax-feed-text');
							text.appendChild(link);
							item.appendChild(text);					  
							var date = document.createElement('span');
							date.setAttribute('class', 'google-ajax-feed-date');
							date.appendChild(document.createTextNode(relativeDate(entry.publishedDate)));
							item.appendChild(date);
							list.appendChild(item);
						}
						container.appendChild(list);
					}
				});
			});
			function relativeDate(time){
				var system_date = new Date(time); 
				var user_date = new Date();
				var prefix = 'about ';
				var diff = Math.floor((user_date - system_date) / 1000); 
				if (diff <= 1) return 'just now'; 
				if (diff < 20) return diff + ' seconds ago'; 
				if (diff < 40) return 'half a minute ago'; 
				if (diff < 60) return 'less than a minute ago'; 
				if (diff <= 90) return 'one minute ago'; 
				if (diff <= 3540) return Math.round(diff / 60) + ' minutes ago'; 
				if (diff <= 5400) return 'about an hour ago'; 
				if (diff <= 86400) return Math.round(diff / 3600) + ' hours ago'; 
				if (diff <= 129600) return '1 day ago'; 
				if (diff < 604800) return Math.round(diff / 86400) + ' days ago'; 
				if (diff <= 777600) return '1 week ago'; 
				return 'on ' + time; 
			}
		</script>
		<style>
			.widget ul.google-ajax-feed-list li {
				padding-bottom: 10px;
				margin: 0;".($options['icon']?"
				padding-left:25px;
				background:url({$options['icon']}) no-repeat left 2px;
				":"")."
			}
			.widget ul.google-ajax-feed-list .google-ajax-feed-text {
				display: block;
			}
			.widget ul.google-ajax-feed-list .google-ajax-feed-date {
				display: block;
				color: #5c5c5c;
			}
			".$options['custom_css']."
		</style>
		<div id=\"{$id}\" class=\"google-ajax-feed-div\">{$options['loading_text']}</div>
		";
		return $output;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['feed_url'] 		= $new_instance['feed_url'];
		$instance['icon']			= $new_instance['icon'];
		$instance['link_target'] 	= $new_instance['link_target'];
		$instance['num_entries'] 	= $new_instance['num_entries'];
		$instance['loading_text'] 	= $new_instance['loading_text'];
		$instance['custom_css'] 	= $new_instance['custom_css'];
		return $instance;
	}
	
	function form($instance){
		$defaults = array(
			'title' 		=> '',
			'icon'		 	=> '',
			'link_target'	=> '_blank',
			'num_entries'	=> 5,
			'feed_url'		=> '',
			'loading_text'	=> __('Loading...'),
			'custom_css'	=> ''
		);
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('feed_url'); ?>"><?php _e('Feed URL:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('feed_url'); ?>" name="<?php echo $this->get_field_name('feed_url'); ?>" value="<?php echo $instance['feed_url']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('num_entries'); ?>"><?php _e('Number of Feed Entries:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('num_entries'); ?>" name="<?php echo $this->get_field_name('num_entries'); ?>" value="<?php echo $instance['num_entries']; ?>" style="width:30px;"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('icon'); ?>"><?php _e('Icon URL(16x16):'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('icon'); ?>" name="<?php echo $this->get_field_name('icon'); ?>" value="<?php echo $instance['icon']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('link_target'); ?>"><?php _e('Link Target:'); ?></label> 
			<select id="<?php echo $this->get_field_id('link_target'); ?>" name="<?php echo $this->get_field_name('link_target'); ?>" class="widefat">
				<option value="_blank" <?php if ( '_blank' == $instance['link_target'] ) echo 'selected="selected"'; ?>>Open in new page</option>
				<option value="_self" <?php if ( '_self' == $instance['link_target'] ) echo 'selected="selected"'; ?>>Open in current page</option>
			</select>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('loading_text'); ?>"><?php _e('Loading Text:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('loading_text'); ?>" name="<?php echo $this->get_field_name('loading_text'); ?>" value="<?php echo $instance['loading_text']; ?>" class="widefat"/>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('custom_css'); ?>"><?php _e('Custom CSS:'); ?></label>
			<textarea id="<?php echo $this->get_field_id('custom_css'); ?>" name="<?php echo $this->get_field_name('custom_css'); ?>" class="widefat"><?php echo $instance['custom_css']; ?></textarea>
            <span>This widget uses following classes:</span>
            <em>google-ajax-feed-widget</em><br /><em>google-ajax-feed-div</em><br /><em>google-ajax-feed-list</em><br /><em>google-ajax-feed-item</em><br /><em>google-ajax-feed-text</em><br /><em>google-ajax-feed-date</em>
		</p>
	<?php
	}
}
?>