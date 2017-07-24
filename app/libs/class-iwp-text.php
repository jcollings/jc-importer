<?php
/**
 * Text library
 *
 * @package ImportWP
 * @author James Collings
 * @created 24/07/2017
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class IWP_Text
 */
class IWP_Text{

	/**
	 * Default text
	 *
	 * @var array|null
	 */
	protected $_default = null;

	/**
	 * WPDF_Text constructor.
	 */
	public function __construct() {

		$this->_default = array(
			'import.settings.start_line' => __('Set the row you wish to start your import from.', 'iwp'),
			'import.settings.row_count' => __('Maximum number of rows to import, leave "0" to ignore.', 'iwp'),
			'import.settings.record_import_count' => __('Number of items to import at a time, increasing this number may speed up the import, or if your import is timing out decrease it.', 'iwp'),
			'import.settings.template_type' => __('Set the type of import you are running, once changed hit save all for the page to be changed.', 'iwp'),
			'import.settings.csv_delimiter' => __('The character which separates the CSV record elements.', 'iwp'),
			'import.settings.csv_enclosure' => __('The character which is wrapper around the CSV record elements.', 'iwp'),
			'template.default.post_title' => __('Name of the %s.', 'iwp'),
			'template.default.post_name' => __('The slug is the user friendly and URL valid name of a %s.', 'iwp'),
			'template.default.post_date' => __('The date the %s was created, enter in the format "YYYY-MM-DD HH:ii:ss"', 'iwp')
		);
	}

	/**
	 * Get text string
	 *
	 * @param string $key String key.
	 *
	 * @return mixed|string
	 */
	public function get( $key) {

		return isset( $this->_default[ $key ] ) ? $this->_default[ $key ] : '';
	}
}