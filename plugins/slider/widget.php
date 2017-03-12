<?php

class Hugeit_ReSlider_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'hugeit_reslider_widget',
            'Huge IT Responsive Slider',
            array('description' => __('Huge IT Responsive Slider', 'R-slider'),)
        );
    }

    public function widget($args, $instance)
    {
        extract($args);

        if (isset($instance['sliderid'])) {
            $sliderid = $instance['sliderid'];

            $title = apply_filters('widget_title', $instance['title']);
            /**
             * @var $before_widget
             * @var $after_title
             * @var $before_title
             * @var $after_widget
             */
            echo $before_widget;
            if (!empty($title))
                echo $before_title . $title . $after_title;

            echo do_shortcode("[R-slider id={$sliderid}]");
            echo $after_widget;
        }
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['sliderid'] = strip_tags($new_instance['sliderid']);
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

    public function form($instance)
    {
        $selected_slider = 0;
        $title = "";
        $sliders = false;

        if (isset($instance['sliderid'])) {
            $selected_slider = $instance['sliderid'];
        }

        if (isset($instance['title'])) {
            $title = $instance['title'];
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <label
            for="<?php echo $this->get_field_id('sliderid'); ?>"><?php _e('Select Responsive Slider:', 'R-slider'); ?></label>
        <select id="<?php echo $this->get_field_id('sliderid'); ?>"
                name="<?php echo $this->get_field_name('sliderid'); ?>">
            <?php
            global $wpdb;
            $query = "SELECT * FROM " . RESLIDE_TABLE_SLIDERS . " ";
            $rowwidget = $wpdb->get_results($query);
            foreach ($rowwidget as $rowwidgetecho) : ?>
                <option <?php if ($rowwidgetecho->id == $selected_slider) {
                    echo 'selected';
                } ?> value="<?php echo $rowwidgetecho->id; ?>"><?php echo $rowwidgetecho->title; ?></option>
            <?php endforeach; ?>

        </select>
        <?php
    }
}

?>