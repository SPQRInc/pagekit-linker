<?php

namespace Spqr\Linker\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 * @return string
 */
class LinkerController
{
	/**
	 * @Access("linker: manage settings")
	 */
	public function settingsAction()
	{
		return [
			'$view' => [
				'title' => __( 'Linker Settings' ),
				'name'  => 'spqr/linker:views/admin/settings.php'
			],
			'$data' => [
				'config' => App::module( 'spqr/linker' )->config()
			]
		];
	}
	
	/**
	 * @Request({"config": "array"}, csrf=true)
	 * @param array $config
	 *
	 * @return array
	 */
	public function saveAction( $config = [] )
	{
		App::config()->set( 'spqr/linker', $config );
		
		return [ 'message' => 'success' ];
	}
	
}