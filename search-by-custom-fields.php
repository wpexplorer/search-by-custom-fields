<?php

/*
 * Plugin Name: Search by Custom Fields
 */

// Prevent direct file access.
defined( 'ABSPATH' ) || exit;

/**
 * Allow searching by custom fields.
 *
 * @link https://www.wpexplorer.com/how-to-include-custom-field-values-in-wordpress-search/
 */
final class Search_By_Custom_Fields {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'posts_join', [ $this, 'filter_posts_join' ], 10, 2 );
		add_filter( 'posts_where', [ $this, 'filter_posts_where' ], 10, 2 );
		add_filter( 'posts_distinct', [ $this, 'filter_posts_distinct' ], 10, 2 );
	}

	/**
	 * Adds the postmeta table to the search query.
	 */
	public function filter_posts_join( $join, $query ) {
		if ( $query->is_search() ) {
			global $wpdb;
			$join .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id";
		}
		return $join;
	}

	/**
	 * Adds meta values to the search query.
	 */
	public function filter_posts_where( $where, $query ) {
		if ( $query->is_search() ) {
			global $wpdb;
			$where = preg_replace(
				"/\(\s*{$wpdb->posts}.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
				"({$wpdb->posts}.post_title LIKE $1) OR ({$wpdb->postmeta}.meta_value LIKE $1)",
				$where
			);
		}
		return $where;
	}

	/**
	 * Prevent duplicate posts in search results.
	 */
	public function filter_posts_distinct( $where, $query ) {
		if ( $query->is_search() ) {
			return "DISTINCT";
		}
		return $where;
	}

}
new Search_By_Custom_Fields();
