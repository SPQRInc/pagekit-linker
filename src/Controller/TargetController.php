<?php

namespace Spqr\Linker\Controller;

use Pagekit\Application as App;
use Spqr\Linker\Model\Target;


/**
 * @Access(admin=true)
 * @return string
 */
class TargetController
{
	/**
	 * @Access("linker: manage targets")
	 * @Request({"filter": "array", "page":"int"})
	 * @param null $filter
	 * @param int  $page
	 *
	 * @return array
	 */
	public function targetAction( $filter = null, $page = 0 )
	{
		return [
			'$view' => [ 'title' => 'Targets', 'name' => 'spqr/linker:views/admin/target-index.php' ],
			'$data' => [
				'statuses' => Target::getStatuses(),
				'config'   => [
					'filter'     => (object) $filter,
					'page'       => $page,
					'statistics' => App::module( 'spqr/linker' )->config( 'statistics' )
				]
			]
		];
	}
	
	/**
	 * @Route("/target/edit", name="target/edit")
	 * @Access("linker: manage targets")
	 * @Request({"id": "int"})
	 * @param int $id
	 *
	 * @return array
	 */
	public function editAction( $id = 0 )
	{
		try {
			$module = App::module( 'spqr/linker' );
			
			if ( !$target = Target::where( compact( 'id' ) )->related( 'marker' )->first() ) {
				if ( $id ) {
					App::abort( 404, __( 'Invalid target id.' ) );
				}
				$target = Target::create(
					[
						'status' => Target::STATUS_DRAFT,
						'date'   => new \DateTime(),
						'marker' => [['type' => 'default', 'value' => 'default']]
					]
				);
				
				$target->set( 'nofollow', $module->config( 'nofollow' ) );
				$target->set( 'target', $module->config( 'target' ) );
				$target->set( 'redirect', $module->config( 'redirect' ) );
				$target->set( 'href_class', $module->config( 'href_class' ) );
				$target->set( 'mask', $module->config( 'mask' ) );
			}
			
			return [
				'$view' => [
					'title' => $id ? __( 'Edit Target' ) : __( 'Add Target' ),
					'name'  => 'spqr/linker:views/admin/target-edit.php'
				],
				'$data' => [
					'target'   => $target,
					'statuses' => Target::getStatuses()
				]
			];
		} catch ( \Exception $e ) {
			App::message()->error( $e->getMessage() );
			
			return App::redirect( '@linker/target' );
		}
	}
}