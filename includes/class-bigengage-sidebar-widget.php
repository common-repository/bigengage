<?php
class Bigengage_Sidebar_Widget extends WP_Widget {

  /**
   * Register widget with WordPress.
   */
  function __construct() {
    parent::__construct(
      'bigengage_widget', // Base ID
      __('Bigengage Sidebar Form', 'text_domain'), // Name
      array( 'description' => __( 'BigEngage Sidebar Form', 'text_domain' ), ) // Args
    );
  }

  /**
   * Front-end display of widget.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance Saved values from database.
   */
  public function widget( $args, $instance )
	{
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Call subscriber form
		//echo do_shortcode("[USM_form]");
		echo "<div class='bigengage-wordpress-sidebar-widget-form' style='display: none !important;'></div>";
		
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance )
	{
		if ( isset( $instance[ 'title' ] ))
		{
			$title = $instance[ 'title' ];
		}
		else
		{
			$title = '';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $newInstance, $oldInstance )
	{
		$instance = array();
		$instance['title'] = ( ! empty( $newInstance['title'] ) ) ? strip_tags( $newInstance['title'] ) : '';
		return $instance;
	}

}
