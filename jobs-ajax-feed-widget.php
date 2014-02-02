<?php
/**
 * Plugin Name: Jobs Ajax Feed Widget
 * Plugin URI: https://github.com/infiniteschema/Jobs-Ajax-Feed-Widget
 * Description: Display job listings in an Ajax-powered RSS feed widget.
 * Version: 1.0
 * Author: Calen Fretts
 * Author URI: http://infiniteschema.com
 * License: GPLv2
 * GitHub Plugin URI: https://github.com/infiniteschema/Jobs-Ajax-Feed-Widget
 */
 
/* 
Copyright (C) 2014 Calen Fretts

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

add_action('widgets_init', 'jobs_ajax_feed_widget_init');

function jobs_ajax_feed_widget_init() {
	register_widget('JobsAjaxFeed_Widget');
	add_action('wp_head', 'jobs_ajax_feed_wp_head');
	add_action('wp_footer', 'jobs_ajax_feed_wp_footer');
}

function jobs_ajax_feed_wp_head(){
	echo '<script src="http://www.google.com/jsapi" type="text/javascript"></script>';
}

function jobs_ajax_feed_wp_footer(){
	echo '<script type="text/javascript">google.load("feeds", "1");</script>';
}

class JobsAjaxFeed_Widget extends WP_Widget {

	function __construct(){
		parent::WP_Widget( 'jobs-ajax-feed-widget', 'Jobs Ajax Feed', array( 'classname' => 'jobs-ajax-feed-widget', 'description' => __('Display job listings in an Ajax-powered RSS feed widget.') ) );
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
		$id = 'jobs-ajax-feed-widget_' . uniqid();	
		$feed_url = html_entity_decode("http://pipes.yahoo.com/pipes/pipe.run?_id=73cfb93650ba001eca64ccec9e892944&_render=rss");
		if($_SERVER['REMOTE_ADDR'])
			$feed_url .= html_entity_decode("&userip=" . $_SERVER['REMOTE_ADDR']);
		if($_SERVER['HTTP_USER_AGENT'])
			$feed_url .= html_entity_decode("&useragent=" . $_SERVER['HTTP_USER_AGENT']);
		if($options['param_query'])
			$feed_url .= html_entity_decode("&q=" . $options['param_query']);
		if($options['param_location'])
			$feed_url .= html_entity_decode("&l=" . $options['param_location']);
		if($options['param_sort'])
			$feed_url .= html_entity_decode("&sort=" . $options['param_sort']);
		if($options['param_radius'])
			$feed_url .= html_entity_decode("&radius=" . $options['param_radius']);
		if($options['param_jobtype'])
			$feed_url .= html_entity_decode("&jt=" . $options['param_jobtype']);
		if($options['param_start'])
			$feed_url .= html_entity_decode("&start=" . $options['param_start']);
		if($options['param_limit'])
			$feed_url .= html_entity_decode("&limit=" . $options['param_limit']);
		if($options['param_fromage'])
			$feed_url .= html_entity_decode("&fromage=" . $options['param_fromage']);
		if($options['param_highlight'])
			$feed_url .= html_entity_decode("&highlight=" . $options['param_highlight']);
		if($options['param_filter'])
			$feed_url .= html_entity_decode("&filter=" . $options['param_filter']);
		if($options['param_country'])
			$feed_url .= html_entity_decode("&co=" . $options['param_country']);
		if($options['param_channel'] && ($instance['param_publisher'] && $instance['param_key']))
			$feed_url .= html_entity_decode("&chnl=" . $options['param_channel']);
		else
			$feed_url .= html_entity_decode("&chnl=plugin");
		if($options['param_publisher'])
			$feed_url .= html_entity_decode("&publisher=" . $options['param_publisher']);
		if($options['param_key'])
			$feed_url .= html_entity_decode("&key=" . $options['param_key']);

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
						list.setAttribute('class', 'jobs-ajax-feed-list');
						container.innerHTML = '';
						for (var i = 0; i < result.feed.entries.length; i++) {
							var entry = result.feed.entries[i];
							var item = document.createElement('li');
							item.setAttribute('class', 'jobs-ajax-feed-item');
							var link = document.createElement('a');
							link.innerHTML = entry.title;
							link.href = entry.link;
							link.setAttribute('target', '{$options[link_target]}')
							var text = document.createElement('span');
							text.setAttribute('class', 'jobs-ajax-feed-text');
							text.appendChild(link);
							item.appendChild(text);					  
							var date = document.createElement('span');
							date.setAttribute('class', 'jobs-ajax-feed-date');
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
			.widget ul.jobs-ajax-feed-list li {
				padding-bottom: 10px;
				margin: 0;".($options['icon']?"
				padding-left:25px;
				background:url({$options['icon']}) no-repeat left 2px;
				":"")."
			}
			.widget ul.jobs-ajax-feed-list .jobs-ajax-feed-text {
				display: block;
			}
			.widget ul.jobs-ajax-feed-list .jobs-ajax-feed-date {
				display: block;
				color: #5c5c5c;
			}
			".$options['custom_css']."
		</style>
		<div id=\"{$id}\" class=\"jobs-ajax-feed-div\">{$options['loading_text']}</div>
		";
		return $output;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['param_query'] 	= $new_instance['param_query'];
		$instance['param_location'] = $new_instance['param_location'];
		$instance['param_sort'] 	= $new_instance['param_sort'];
		$instance['param_radius'] 	= $new_instance['param_radius'];
		$instance['param_jobtype'] 	= $new_instance['param_jobtype'];
		$instance['param_start'] 	= $new_instance['param_start'];
		$instance['param_limit'] 	= $new_instance['param_limit'];
		$instance['param_fromage'] 	= $new_instance['param_fromage'];
		$instance['param_highlight'] 	= $new_instance['param_highlight'];
		$instance['param_filter'] 	= $new_instance['param_filter'];
		$instance['param_country'] 	= $new_instance['param_country'];
		$instance['param_channel'] 	= $new_instance['param_channel'];
		$instance['param_publisher'] 	= $new_instance['param_publisher'];
		$instance['param_key'] 	= $new_instance['param_key'];
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
			'param_query'	=> '',
			'param_location'	=> '',
			'param_sort'	=> '',
			'param_radius'	=> '',
			'param_jobtype'	=> '',
			'param_start'	=> '',
			'param_limit'	=> '',
			'param_fromage'	=> '',
			'param_highlight'	=> '',
			'param_filter'	=> '',
			'param_country'	=> '',
			'param_channel'	=> '',
			'param_publisher'	=> '',
			'param_key'	=> '',
			'loading_text'	=> __('Loading...'),
			'custom_css'	=> ''
		);
		$instance = wp_parse_args((array)$instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_query'); ?>"><?php _e('Search Param - query:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_query'); ?>" name="<?php echo $this->get_field_name('param_query'); ?>" value="<?php echo $instance['param_query']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_location'); ?>"><?php _e('Search Param - location:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_location'); ?>" name="<?php echo $this->get_field_name('param_location'); ?>" value="<?php echo $instance['param_location']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_sort'); ?>"><?php _e('Search Param - sort:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_sort'); ?>" name="<?php echo $this->get_field_name('param_sort'); ?>" value="<?php echo $instance['param_sort']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_radius'); ?>"><?php _e('Search Param - radius:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_radius'); ?>" name="<?php echo $this->get_field_name('param_radius'); ?>" value="<?php echo $instance['param_radius']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_jobtype'); ?>"><?php _e('Search Param - jobtype:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_jobtype'); ?>" name="<?php echo $this->get_field_name('param_jobtype'); ?>" value="<?php echo $instance['param_jobtype']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_start'); ?>"><?php _e('Search Param - start:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_start'); ?>" name="<?php echo $this->get_field_name('param_start'); ?>" value="<?php echo $instance['param_start']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_limit'); ?>"><?php _e('Search Param - limit:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_limit'); ?>" name="<?php echo $this->get_field_name('param_limit'); ?>" value="<?php echo $instance['param_limit']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_fromage'); ?>"><?php _e('Search Param - fromage:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_fromage'); ?>" name="<?php echo $this->get_field_name('param_fromage'); ?>" value="<?php echo $instance['param_fromage']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_highlight'); ?>"><?php _e('Search Param - highlight:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_highlight'); ?>" name="<?php echo $this->get_field_name('param_highlight'); ?>" value="<?php echo $instance['param_highlight']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_filter'); ?>"><?php _e('Search Param - filter:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_filter'); ?>" name="<?php echo $this->get_field_name('param_filter'); ?>" value="<?php echo $instance['param_filter']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_country'); ?>"><?php _e('Search Param - country:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_country'); ?>" name="<?php echo $this->get_field_name('param_country'); ?>" value="<?php echo $instance['param_country']; ?>" class="widefat"/>
		</p>
		<?php if ($instance['param_publisher'] && $instance['param_key']) { ?>
		<p>
			<label for="<?php echo $this->get_field_id('param_channel'); ?>"><?php _e('Search Param - channel:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_channel'); ?>" name="<?php echo $this->get_field_name('param_channel'); ?>" value="<?php echo $instance['param_channel']; ?>" class="widefat"/>
		</p>
		<?php } ?>
		<p>
			<label for="<?php echo $this->get_field_id('param_publisher'); ?>"><?php _e('Search Param - publisher:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_publisher'); ?>" name="<?php echo $this->get_field_name('param_publisher'); ?>" value="<?php echo $instance['param_publisher']; ?>" class="widefat"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('param_key'); ?>"><?php _e('Search Param - key:'); ?></label>
			<input type="text" id="<?php echo $this->get_field_id('param_key'); ?>" name="<?php echo $this->get_field_name('param_key'); ?>" value="<?php echo $instance['param_key']; ?>" class="widefat"/>
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
            <em>jobs-ajax-feed-widget</em><br /><em>jobs-ajax-feed-div</em><br /><em>jobs-ajax-feed-list</em><br /><em>jobs-ajax-feed-item</em><br /><em>jobs-ajax-feed-text</em><br /><em>jobs-ajax-feed-date</em>
		</p>
	<?php
	}
}
?>
