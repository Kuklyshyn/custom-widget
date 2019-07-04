<?php
/**
 * Plugin Name: Custom Widget
 * Description: Sort users for coments
 * Author:      Mykola Kuklyshyn
 * Version:     1.0
 */

class Count_Widget extends WP_Widget {


    function __construct() {
        parent::__construct(
            'custom_widget',
            esc_html__( 'Count Widget', 'text_domain' ),
            array( 'description' => esc_html__( 'Count comments for user', 'text_domain' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 1;
        if ( ! $number ) {
            $number = 1;
        }

        $show_count = isset( $instance['show_count'] ) ? $instance['show_count'] : false;

        $params = array(
            'number' => $number
        );
        $uq = new WP_User_Query( $params );
        if ( ! empty( $uq->results ) ) {

            $users = [];

            foreach ( $uq->results as $u ) {

                $args = array(
                    'user_id' => $u->ID,
                    'count' => true
                );
                $comments = get_comments($args);

                $users = [
                    $u->user_login => $comments
                ];

                foreach ($users as $key => $value) {
                    ?><p class="name"><?php echo $key; ?>
                    <?php if ( $show_count ) : ?>
                        <span class="post-count"><?php echo '(' . $value . ')' ; ?></span>
                    <?php endif;
                    ?></p><?php
                }

            }

        } else {
            echo 'Empty';
        }
    }


    public function form( $instance ) {
        $show_count = isset( $instance['show_count'] ) ? (bool) $instance['show_count'] : false;
        $show_count_zero = isset( $instance['show_count_zero'] ) ? (bool) $instance['show_count_zero'] : false;
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 1;
        ?>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_count ); ?> id="<?php echo $this->get_field_id( 'show_count' ); ?>" name="<?php echo $this->get_field_name( 'show_count' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _e( 'Display comment count?' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $show_count_zero ); ?> id="<?php echo $this->get_field_id( 'show_count_zero' ); ?>" name="<?php echo $this->get_field_name( 'show_count_zero' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_count_zero' ); ?>"><?php _e( 'Display comment users without comment?' ); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of users to show:' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
        </p>
        <?php
    }


    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_count'] = isset( $new_instance['show_count'] ) ? (bool) $new_instance['show_count'] : false;
        $instance['show_count_zero'] = isset( $new_instance['show_count_zero'] ) ? (bool) $new_instance['show_count_zero'] : false;

        return $instance;
    }

}
function register_custom_widget() {
    register_widget( 'Count_Widget' );
}
add_action( 'widgets_init', 'register_custom_widget' );