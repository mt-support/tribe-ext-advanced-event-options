<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Extensions\EventsHappeningNow\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'events-happening-now.views.filters' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Extensions\EventsHappeningNow\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'events-happening-now.views.hooks' ), 'some_method' ] );
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\EventsHappeningNow
 */

namespace Tribe\Extensions\EventsControl;

use TEC\Common\Contracts\Service_Provider;
use Tribe__Template as Template;
use Tribe__Events__Main as Events_Plugin;
use WP_Post;

/**
 * Class Hooks
 *
 * @since 1.0.0
 *
 * @package Tribe\Extensions\EventsHappeningNow
 */
class Hooks extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by the extension.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'add_meta_boxes', [ $this, 'action_add_metabox' ] );
		add_action( 'init', [ $this, 'action_register_metabox_fields' ] );
		add_action( 'save_post_' . Events_Plugin::POSTTYPE, [ $this, 'action_save_metabox' ], 15, 3 );

		// List View
		add_action( 'tribe_template_after_include:events/v2/list/event/venue', [ $this, 'action_add_online_event' ], 15, 3 );

		// Day View
		add_action( 'tribe_template_after_include:events/v2/day/event/description', [ $this, 'action_add_archive_online_link' ], 15, 3 );
		add_action( 'tribe_template_after_include:events/v2/day/event/venue', [ $this, 'action_add_online_event' ], 15, 3 );

		// Photo View
		add_action( 'tribe_template_before_include:events-pro/v2/photo/event/date-time', [ $this, 'action_add_online_event' ], 20, 3 );

		// Map View
		add_action( 'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/event/venue', [ $this, 'action_add_online_event' ], 15, 3 );
		add_action( 'tribe_template_after_include:events-pro/v2/map/event-cards/event-card/tooltip/venue', [ $this, 'action_add_online_event' ], 15, 3 );

		// Week View
		add_action( 'tribe_template_after_include:events-pro/v2/week/mobile-events/day/event/venue', [ $this, 'action_add_online_event' ], 15, 3 );
		add_action( 'tribe_template_after_include:events-pro/v2/week/grid-body/events-day/event/tooltip/description', [ $this, 'action_add_archive_online_link' ], 15, 3 );
	}

	/**
	 * Adds the actions required by the extension.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_template_origin_namespace_map', [ $this, 'filter_add_template_origin_namespace' ], 15, 3 );
		add_filter( 'tribe_template_path_list', [ $this, 'filter_template_path_list' ], 15, 2 );
		add_filter( 'tribe_the_notices', [ $this, 'filter_include_single_control_markers' ], 15, 2 );
		add_filter( 'tribe_json_ld_event_object', [ $this, 'filter_json_ld_modifiers' ], 15, 3 );
		add_filter( 'post_class', [ $this, 'filter_add_post_class' ], 15, 3 );
		add_filter( 'tribe_rest_event_data', [ $this, 'filter_rest_event_data'], 10, 2 );

		// List View
		add_filter( 'tribe_template_html:events/v2/list/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );

		// Day View
		add_filter( 'tribe_template_html:events/v2/day/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );

		// Month View
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/date', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/date', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/multiday-events/multiday-event', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/mobile-events/mobile-day/mobile-event/date', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/calendar-events/calendar-event/tooltip/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/calendar-body/day/multiday-events/multiday-event', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events/v2/month/mobile-events/mobile-day/mobile-event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );

		// Photo View
		add_filter( 'tribe_template_html:events-pro/v2/photo/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );

		// Map View
		add_filter( 'tribe_template_html:events-pro/v2/map/event-cards/event-card/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/map/event-cards/event-card/tooltip/title', [ $this, 'filter_insert_status_label' ], 15, 4 );

		// Week View
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/date', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/tooltip/date', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/multiday-events-day/multiday-event', [ $this, 'filter_insert_online_event' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/events-day/event/tooltip/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/grid-body/multiday-events-day/multiday-event', [ $this, 'filter_insert_status_label' ], 15, 4 );
		add_filter( 'tribe_template_html:events-pro/v2/week/mobile-events/day/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
		
		//Widget
		add_filter( 'tribe_template_html:events/v2/widgets/widget-events-list/event/title', [ $this, 'filter_insert_status_label' ], 15, 4 );
	}

	/**
	 * Add the control classes for the views v2 elements
	 *
	 * @since 1.0.0
	 *
	 * @param string|string[]  $classes   Space-separated string or array of class names to add to the class list.
	 * @param int|WP_Post      $post      Post ID or post object.
	 *
	 * @return string[]
	 */
	public function filter_add_post_class( $classes, $class, $post ) {
		$new_classes = $this->container->make( Template_Modifications::class )->get_post_classes( $post );
		return array_merge( $classes, $new_classes );
	}

	/**
	 * Includes Pro into the path namespace mapping, allowing for a better namespacing when loading files.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $namespace_map Indexed array containing the namespace as the key and path to `strpos`.
	 * @param string    $path          Path we will do the `strpos` to validate a given namespace.
	 * @param Template  $template      Current instance of the template class.
	 *
	 * @return array  Namespace map after adding Pro to the list.
	 */
	public function filter_add_template_origin_namespace( $namespace_map, $path, Template $template ) {
		$namespace_map[ Main::SLUG ] = Main::PATH;
		return $namespace_map;
	}

	/**
	 * Filters the list of folders TEC will look up to find templates to add the ones defined by PRO.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $folders  The current list of folders that will be searched template files.
	 * @param Template $template Which template instance we are dealing with.
	 *
	 * @return array The filtered list of folders that will be searched for the templates.
	 */
	public function filter_template_path_list( array $folders, Template $template ) {
		$path = (array) rtrim( Main::PATH, '/' );

		// Pick up if the folder needs to be added to the public template path.
		$folder = [ 'src/views' ];

		if ( ! empty( $folder ) ) {
			$path = array_merge( $path, $folder );
		}

		$folders[ Main::SLUG ] = [
			'id'        => Main::SLUG,
			'namespace' => Main::SLUG,
			'priority'  => 10,
			'path'      => implode( DIRECTORY_SEPARATOR, $path ),
		];

		return $folders;
	}

	/**
	 * Modifiers to the JSON LD object we use.
	 *
	 * @since 1.0.0
	 *
	 * @param object  $data The JSON-LD object.
	 * @param array   $args The arguments used to get data.
	 * @param WP_Post $post The post object.
	 *
	 * @return object JSON LD object after modifications.
	 */
	public function filter_json_ld_modifiers( $data, $args, $post ) {
		$data = $this->container->make( JSON_LD::class )->modify_canceled_event( $data, $args, $post );
		$data = $this->container->make( JSON_LD::class )->modify_postponed_event( $data, $args, $post );
		return $this->container->make( JSON_LD::class )->modify_online_event( $data, $args, $post );
	}

	/**
	 * Filters event REST data to include new fields.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data The event data array.
	 * @param WP_Post $event The post object.
	 *
	 * @return array The event data array after modification.
	 */
	public function filter_rest_event_data( $data, $event ) {
		$meta = tribe( Event_Meta::class )->get_meta( $event );
		if ( empty( $meta ) ) {
			return $data;
		}

		return array_merge( $data, $meta );
	}

	/**
	 * Register the metabox in the correct action.
	 *
	 * @since 1.0.0
	 *
	 * @return void Action hook with no return.
	 */
	public function action_add_metabox() {
		$this->container->make( Metabox::class )->register();
	}

	/**
	 * Register the metabox fields in the correct action.
	 *
	 * @since 1.0.0
	 *
	 * @return void Action hook with no return.
	 */
	public function action_register_metabox_fields() {
		$this->container->make( Metabox::class )->register_fields();
	}

	/**
	 * Register the metabox fields in the correct action.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Which post ID we are dealing with when saving.
	 * @param WP_Post $post    WP Post instance we are saving.
	 * @param boolean $update  If we are updating the post or not.
	 *
	 * @return void Action hook with no return.
	 */
	public function action_save_metabox( $post_id, $post, $update ) {
		$this->container->make( Metabox::class )->save( $post_id, $post, $update );
	}

	/**
	 * Include the online now url anchor for the archive pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 *
	 * @return void  Template render has no return/
	 */
	public function action_add_archive_online_link( $file, $name, $template ) {
		$this->container->make( Template_Modifications::class )->add_archive_online_link( $file, $name, $template );
	}

	/**
	 * Include the online now url anchor for the archive pages.
	 *
	 * @since TBD
	 *
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 *
	 * @return void  Template render has no return/
	 */
	public function action_add_online_event( $file, $name, $template ) {
		$this->container->make( Template_Modifications::class )->add_online_event( $file, $name, $template );
	}

	/**
	 * Insert the online event for the archive pages.
	 *
	 * @param string   $html      HTML of the template.
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 *
	 * @return string
	 */
	public function filter_insert_online_event( $html, $file, $name, $template ) {
		return $this->container->make( Template_Modifications::class )->regex_insert_template( 'online-event', $html, $file, $name, $template );
	}

	/**
	 * Insert the status label for the archive pages.
	 *
	 * @param string   $html      HTML of the template.
	 * @param string   $file      Complete path to include the PHP File.
	 * @param array    $name      Template name.
	 * @param Template $template  Current instance of the Template.
	 *
	 * @return string
	 */
	public function filter_insert_status_label( $html, $file, $name, $template ) {
		return $this->container->make( Template_Modifications::class )->regex_insert_template( 'status-label', $html, $file, $name, $template );
	}

	/**
	 * Include the control markers for the single pages.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $notices_html  Previously set HTML.
	 * @param  array   $notices       Array of notices added previously.
	 *
	 * @return string  Before event html with the new markers.
	 */
	public function filter_include_single_control_markers( $notices_html, $notices ) {
		return $this->container->make( Template_Modifications::class )->add_single_control_markers( $notices_html, $notices );
	}
}
