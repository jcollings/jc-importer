<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/04/2018
 * Time: 16:09
 */

class UserMapper extends AbstractMapper implements \ImportWP\Importer\MapperInterface {

	protected $_user_fields = array(
		'ID',
		'user_pass',
		'user_login',
		'user_nicename',
		'user_url',
		'user_email',
		'display_name',
		'nickname',
		'first_name',
		'last_name',
		'description',
		'rich_editing',
		'user_registered',
		'role',
		'jabber',
		'aim',
		'yim'
	);
	protected $_user_required = array( 'user_login' );

	public function exists( \ImportWP\Importer\ParsedData $data ) {

		$unique_fields = $this->template->_unique;
		$default_group = $data->getData('default');

		$query_args = array();
		$meta_args  = array();

		$search         = array(); // store search values
		$search_columns = array(); // store search columns

		foreach ( $unique_fields as $field ) {

			if ( in_array( $field, $this->_user_fields ) ) {

				$search_columns[] = $field;
				$search[]         = $default_group[ $field ];
			} else {
				$meta_args[] = array(
					'key'     => $field,
					'value'   => $default_group[ $field ],
					'compare' => '=',
					'type'    => 'CHAR'
				);
			}
		}

		// create search
		$query_args['search']         = implode( ', ', $search );
		$query_args['search_columns'] = $search_columns;
		$query_args['meta_query']     = $meta_args;

		$query = new WP_User_Query( $query_args );

		if ( $query->total_users == 1 ) {
			$this->ID = $query->results[0]->ID;
			return true;
		}

		return false;
	}

	public function insert( \ImportWP\Importer\ParsedData $data ) {

		$fields = $data->getData('default');

		if ( ! isset( $fields['user_login'] ) || empty( $fields['user_login'] ) ) {
			throw new JCI_Exception( "No username present", JCI_ERR );
		}

		if ( ! isset( $fields['user_pass'] ) || empty( $fields['user_pass'] ) ) {

			throw new JCI_Exception( "No password present", JCI_ERR );
		}

		$result = wp_insert_user( $fields );
		if ( is_wp_error( $result ) ) {
			throw new JCI_Exception( $result->get_error_message(), JCI_ERR );
		}

		$this->ID = $result;

		// add user meta
		foreach ( $fields as $meta_key => $meta_value ) {
			if ( ! in_array( $meta_key, $this->_user_fields ) ) {
				$this->update_custom_field( $this->ID, $meta_key, $meta_value, true );
			}
		}

		$this->add_version_tag();

		do_action( 'jci/after_user_insert', $result, $fields );

		return $result;

	}

	public function update( \ImportWP\Importer\ParsedData $data ) {

		$fields = $data->getData('default');

		$fields['ID'] = $this->ID;
		$result       = wp_update_user( $fields );
		if ( is_wp_error( $result ) ) {
			throw new JCI_Exception( $result->get_error_message(), JCI_ERR );
		}

		// update user meta
		foreach ( $fields as $meta_key => $meta_value ) {
			if ( ! in_array( $meta_key, $this->_user_fields ) ) {

				$this->update_custom_field( $this->ID, $meta_key, $meta_value );
			}
		}

		$this->add_version_tag();

		return $this->ID;

	}

	public function delete( \ImportWP\Importer\ParsedData $data ) {
		// TODO: Implement delete() method.
	}

	function add_version_tag() {

		if ( ! isset( JCI()->importer ) ) {
			return;
		}

		$importer_id = JCI()->importer->get_ID();
		$version     = JCI()->importer->get_version();

		update_user_meta( $this->ID, '_jci_version_' . $importer_id, $version );
	}

	public function update_custom_field( $user_id, $meta_key, $meta_value, $unique = false ) {
		$old_meta_version = get_user_meta( $user_id, $meta_key, true );
		if ( $old_meta_version ) {
			update_user_meta( $user_id, $meta_key, $meta_value, $old_meta_version );
		} else {
			add_user_meta( $user_id, $meta_key, $meta_value, $unique );
		}
	}
}