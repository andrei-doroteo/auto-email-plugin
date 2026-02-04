<?php
/**
 * This file contains helper functions to interact with the WordPress API.
 */

namespace DoroteoDigital\AutoEmail\api\utils;

/**
 * @param string $namespace The base path after the WordPress installation's domain name (i.e. "/plugin/v1").
 * @param string $path The path appended to the base path (i.e. "/users/posts").
 * @param array $methods An array of the allowed methods of the REST path as strings (i.e. ['GET','POST','OPTIONS']).
 * @param callable $callback A callable that takes in a request and executes the logic for an endpoint. !!! TODO: Confirm the behaviour of this
 * @param bool $is_public If true: Allows anyone to access this endpoint. If false, uses $permission_callback to handle access. !!! TODO: Confirm the behaviour of this
 * @param callable|null $permission_callback A callback function that returns a boolean. Decides when to allow access to an endpoint. !!! TODO: Confirm the behaviour of this
 *
 * @return bool
 *
 * Extends the WordPress REST API with a custom endpoint
 */

// TODO: Implement this function
function register_wp_endpoint( string $namespace, string $path, array $methods, callable $callback, bool $is_public = false, ?callable $permission_callback = null ): void {

/* Notes:
- validate namespace and path start with "/" and do not end with "/"
- change default permission callback values
- Add a warning in the documentation about 
- Add example usage
- Add a @throws documentation
*/

//	---- Sample partial implementation ----
	add_action( 'rest_api_init', function () use ( $namespace, $path, $methods, $callback, $is_public, $permission_callback ) {

		register_rest_route( $namespace, $path, [
			'methods'             => $methods,
			'callback'            => $callback,
			'permission_callback' => $is_public ? '__return_true' : ($permission_callback ?? "__return_false"),
		] );

	} );
	

}