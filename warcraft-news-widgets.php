<?php
/*
   Copyright 2010-present Jakob Lenfers <jakob@drss.de>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class WarcraftNewsGuildWidget extends WP_Widget{
	function WarcraftNewsGuildWidget() {
		$widget_ops = array('classname' => 'widget_warcraft_news_guild', 'description' => 'Shows the guild news gathered from the armory');
	    $control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('warcraft_news_guild', "Warcraft Guild News", $widget_ops, $control_ops);
	}

	function widget($args, $instance){
		extract($args, EXTR_SKIP);
		// before widget stuff
		echo $before_widget;

		// title
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		// widget content
		if(empty($instance['guild']) ||
		   empty($instance['realm']) ||
		   empty($instance['region']) ||
		   empty($instance['lang']) ||
		   empty($instance['item_count'])){
			_e('Please configure the widget settings in the widget screen.');
		}
		else{
			echo warcraft_news_html($instance);
		}
		
		// after widget stuff
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['guild'] = rawurlencode(strip_tags($new_instance['guild']));
		$instance['realm'] = rawurlencode(strip_tags($new_instance['realm']));
		$instance['lang'] = rawurlencode(strip_tags($new_instance['lang']));
		$instance['region'] = strip_tags($new_instance['region']);
		$item_count = strip_tags($new_instance['item_count']);
		if(is_numeric($item_count)){
			$instance['item_count'] = intval($item_count);
		}
		else{
			$instance['item_count'] = 5;
		}
		return $instance;
	}

	function form($instance) {
        $title = esc_attr($instance['title']);
        $guild = rawurldecode(esc_attr($instance['guild']));
        $realm = rawurldecode(esc_attr($instance['realm']));
        $lang = esc_attr($instance['lang']);
        $region = esc_attr($instance['region']);
        $item_count = $instance['item_count'];
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

            <p><label for="<?php echo $this->get_field_id('guild'); ?>"><?php _e('Guild:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('guild'); ?>" name="<?php echo $this->get_field_name('guild'); ?>" type="text" value="<?php echo $guild; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('realm'); ?>"><?php _e('Realm:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('realm'); ?>" name="<?php echo $this->get_field_name('realm'); ?>" type="text" value="<?php echo $realm; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('region'); ?>"><?php _e('Region:'); ?>
				<select id="<?php echo $this->get_field_id('region'); ?>" name="<?php echo $this->get_field_name('region'); ?>" size="1">
					<option <?php selected('eu', $region); ?> value="eu"><?php _e('EU') ?></option>
					<option <?php selected('us', $region); ?> value="us"><?php _e('US')?></option>
				</select>
			</label></p>
            <p><label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language:'); ?>
				<select id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" size="1">
					<option <?php selected('en', $lang); ?> value="en"><?php _e('English') ?></option>
					<option <?php selected('de', $lang); ?> value="de"><?php _e('German')?></option>
					<option <?php selected('fr', $lang); ?> value="fr"><?php _e('French')?></option>
					<option <?php selected('es', $lang); ?> value="es"><?php _e('Spanish')?></option>
					<option <?php selected('ru', $lang); ?> value="ru"><?php _e('Russian')?></option>
				</select>
			</label></p>
            <p><label for="<?php echo $this->get_field_id('item_count'); ?>"><?php _e('How many items would you like to display?'); ?>
				<select id="<?php echo $this->get_field_id('item_count'); ?>" name="<?php echo $this->get_field_name('item_count'); ?>" size="1">
		<?php
					for ( $i = 1; $i <= 25; ++$i )
			echo "					<option ". ( $item_count == $i ? 'selected="selected" ' : '' ) ."value='$i'>$i</option>";
		?>
				</select>
			</label></p>
        <?php 
	}
}

class WarcraftNewsCharWidget extends WP_Widget{
	function WarcraftNewsCharWidget() {
		$widget_ops = array('classname' => 'widget_warcraft_news_char', 'description' => 'Shows the character news gathered from the armory');
	    $control_ops = array('width' => 300, 'height' => 300);
		$this->WP_Widget('warcraft_news_char', "Warcraft Char News", $widget_ops, $control_ops);
	}

	function widget($args, $instance){
		extract($args, EXTR_SKIP);
		// before widget stuff
		echo $before_widget;

		// title
		$title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		// widget content
		if(empty($instance['char']) ||
		   empty($instance['realm']) ||
		   empty($instance['region']) ||
		   empty($instance['lang']) ||
		   empty($instance['item_count'])){
			_e('Please configure the widget settings in the widget screen.');
		}
		else{
			echo warcraft_news_html($instance);
		}
		
		// after widget stuff
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['char'] = rawurlencode(strip_tags($new_instance['char']));
		$instance['realm'] = rawurlencode(strip_tags($new_instance['realm']));
		$instance['lang'] = rawurlencode(strip_tags($new_instance['lang']));
		$instance['region'] = strip_tags($new_instance['region']);
		$item_count = strip_tags($new_instance['item_count']);
		if(is_numeric($item_count)){
			$instance['item_count'] = intval($item_count);
		}
		else{
			$instance['item_count'] = 5;
		}
		return $instance;
	}

	function form($instance) {
        $title = esc_attr($instance['title']);
        $char = rawurldecode(esc_attr($instance['char']));
        $realm = rawurldecode(esc_attr($instance['realm']));
        $lang = esc_attr($instance['lang']);
        $region = esc_attr($instance['region']);
        $item_count = $instance['item_count'];
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

            <p><label for="<?php echo $this->get_field_id('char'); ?>"><?php _e('Character name:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('char'); ?>" name="<?php echo $this->get_field_name('char'); ?>" type="text" value="<?php echo $char; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('realm'); ?>"><?php _e('Realm:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('realm'); ?>" name="<?php echo $this->get_field_name('realm'); ?>" type="text" value="<?php echo $realm; ?>" /></label></p>
            <p><label for="<?php echo $this->get_field_id('region'); ?>"><?php _e('Region:'); ?>
				<select id="<?php echo $this->get_field_id('region'); ?>" name="<?php echo $this->get_field_name('region'); ?>" size="1">
					<option <?php selected('eu', $region); ?> value="eu"><?php _e('EU') ?></option>
					<option <?php selected('us', $region); ?> value="us"><?php _e('US')?></option>
				</select>
			</label></p>
            <p><label for="<?php echo $this->get_field_id('lang'); ?>"><?php _e('Language:'); ?>
				<select id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" size="1">
					<option <?php selected('en', $lang); ?> value="en"><?php _e('English') ?></option>
					<option <?php selected('de', $lang); ?> value="de"><?php _e('German')?></option>
					<option <?php selected('fr', $lang); ?> value="fr"><?php _e('French')?></option>
					<option <?php selected('es', $lang); ?> value="es"><?php _e('Spanish')?></option>
					<option <?php selected('ru', $lang); ?> value="ru"><?php _e('Russian')?></option>
				</select>
			</label></p>
            <p><label for="<?php echo $this->get_field_id('item_count'); ?>"><?php _e('How many items would you like to display?'); ?>
				<select id="<?php echo $this->get_field_id('item_count'); ?>" name="<?php echo $this->get_field_name('item_count'); ?>" size="1">
		<?php
					for ( $i = 1; $i <= 25; ++$i )
			echo "					<option ". ( $item_count == $i ? 'selected="selected" ' : '' ) ."value='$i'>$i</option>";
		?>
				</select>
			</label></p>
        <?php 
	}
}

// register the widgets
add_action('widgets_init', create_function('', 'return register_widget("WarcraftNewsGuildWidget");'));
add_action('widgets_init', create_function('', 'return register_widget("WarcraftNewsCharWidget");'));
?>
