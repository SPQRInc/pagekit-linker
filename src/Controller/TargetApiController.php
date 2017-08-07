<?php

namespace Spqr\Linker\Controller;

use Pagekit\Application as App;
use Spqr\Linker\Model\Target;

/**
 * @Access("linker: manage targets")
 * @Route("target", name="target")
 */
class TargetApiController
{
	/**
	 * @param array $filter
	 * @param int   $page
	 * @param int   $limit
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page":"int", "limit":"int"})
	 *
	 * @return mixed
	 */
	public function indexAction( $filter = [], $page = 0, $limit = 0 )
	{
		$query  = Target::query();
		$filter = array_merge( array_fill_keys( [ 'status', 'search', 'limit', 'order' ], '' ), $filter );
		extract( $filter, EXTR_SKIP );
		if ( is_numeric( $status ) ) {
			$query->where( [ 'status' => (int) $status ] );
		}
		if ( $search ) {
			$query->where(
				function( $query ) use ( $search ) {
					$query->orWhere(
						[
							'title LIKE :search'
						],
						[ 'search' => "%{$search}%" ]
					);
				}
			);
		}
		if ( preg_match( '/^(title|viewcount|clickcount)\s(asc|desc)$/i', $order, $match ) ) {
			$order = $match;
		} else {
			$order = [ 1 => 'title', 2 => 'asc' ];
		}
		$default = App::module( 'spqr/linker' )->config( 'items_per_page' );
		$limit   = min( max( 0, $limit ), $default ) ? : $default;
		$count   = $query->count();
		$pages   = ceil( $count / $limit );
		$page    = max( 0, min( $pages - 1, $page ) );
		$targets = array_values(
			$query->offset( $page * $limit )
			      ->related( 'marker' )
			      ->limit( $limit )
			      ->orderBy( $order[ 1 ], $order[ 2 ] )
			      ->get()
		);
		
		return compact( 'targets', 'pages', 'count' );
	}
	
	/**
	 * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
	 * @param $id
	 *
	 * @return static
	 */
	public function getAction( $id )
	{
		if ( !$target = Target::where( compact( 'id' ) )->related( 'marker' )->first() ) {
			App::abort( 404, 'Target not found.' );
		}
		
		return $target;
	}
	
	/**
	 * @Route(methods="POST")
	 * @Request({"ids": "int[]"}, csrf=true)
	 * @param array $ids
	 *
	 * @return array
	 */
	public function copyAction( $ids = [] )
	{
		foreach ( $ids as $id ) {
			if ( $target = Target::find( (int) $id ) ) {
				if ( !App::user()->hasAccess( 'linker: manage targets' ) ) {
					continue;
				}
				$target         = clone $target;
				$target->id     = null;
				$target->status = $target::STATUS_UNPUBLISHED;
				$target->title  = $target->title . ' - ' . __( 'Copy' );
				$target->date   = new \DateTime();
				$target->save();
			}
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/bulk", methods="POST")
	 * @Request({"targets": "array"}, csrf=true)
	 * @param array $targets
	 *
	 * @return array
	 */
	public function bulkSaveAction( $targets = [] )
	{
		foreach ( $targets as $data ) {
			$this->saveAction( $data, isset( $data[ 'id' ] ) ? $data[ 'id' ] : 0 );
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/", methods="POST")
	 * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
	 * @Request({"target": "array", "id": "int"}, csrf=true)
	 */
	public function saveAction( $data, $id = 0 )
	{
		if ( !$id || !$target = Target::find( $id ) ) {
			if ( $id ) {
				App::abort( 404, __( 'Target not found.' ) );
			}
			$target = Target::create();
		}
		if ( !$data[ 'slug' ] = App::filter( $data[ 'slug' ] ? : $data[ 'title' ], 'slugify' ) ) {
			App::abort( 400, __( 'Invalid slug.' ) );
		}
		
		$target->save( $data );
		$target->saveMarker( $data[ 'marker' ] );
		
		return [ 'message' => 'success', 'target' => $target ];
	}
	
	/**
	 * @Route("/bulk", methods="DELETE")
	 * @Request({"ids": "array"}, csrf=true)
	 * @param array $ids
	 *
	 * @return array
	 */
	public function bulkDeleteAction( $ids = [] )
	{
		foreach ( array_filter( $ids ) as $id ) {
			$this->deleteAction( $id );
		}
		
		return [ 'message' => 'success' ];
	}
	
	/**
	 * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
	 * @Request({"id": "int"}, csrf=true)
	 * @param $id
	 *
	 * @return array
	 */
	public function deleteAction( $id )
	{
		if ( $target = Target::find( $id ) ) {
			if ( !App::user()->hasAccess( 'linker: manage targets' ) ) {
				App::abort( 400, __( 'Access denied.' ) );
			}
			
			$target->delete();
		}
		
		return [ 'message' => 'success' ];
	}
	
}