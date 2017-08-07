<?php

namespace Spqr\Linker\Controller;

use Pagekit\Application as App;
use Spqr\Linker\Model\Statistic;

/**
 * @Access("linker: manage statistics")
 * @Route("statistic", name="statistic")
 */
class StatisticApiController
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
		$query  = Statistic::query();
		$filter = array_merge( array_fill_keys( [ 'status', 'search', 'limit', 'order' ], '' ), $filter );
		extract( $filter, EXTR_SKIP );
		if ( is_numeric( $status ) ) {
			$query->where( [ 'status' => (int) $status ] );
		}
		
		// TODO: Add more search and ordering options
		/*
		if ( $search ) {
			$query->where(
				function( $query ) use ( $search ) {
					$query->orWhere(
						[
							'marker.target.title LIKE :search'
						],
						[ 'search' => "%{$search}%" ]
					);
				}
			);
		}
		*/
		
		if ( preg_match( '/^(id|date)\s(asc|desc)$/i', $order, $match ) ) {
			$order = $match;
		} else {
			$order = [ 1 => 'id', 2 => 'asc' ];
		}
		
		
		$default    = App::module( 'spqr/linker' )->config( 'items_per_page' );
		$limit      = min( max( 0, $limit ), $default ) ? : $default;
		$count      = $query->count();
		$pages      = ceil( $count / $limit );
		$page       = max( 0, min( $pages - 1, $page ) );
		$statistics = array_values(
			$query->offset( $page * $limit )->related( 'marker.target' )->limit( $limit )->orderBy(
				$order[ 1 ],
				$order[ 2 ]
			)->get()
		);
		
		return compact( 'statistics', 'pages', 'count' );
	}
	
	/**
	 * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
	 * @param $id
	 *
	 * @return static
	 */
	public function getAction( $id )
	{
		if ( !$statistic = Statistic::where( compact( 'id' ) )->related( 'marker', 'marker.target' )->first() ) {
			App::abort( 404, 'Statistic not found.' );
		}
		
		return $statistic;
	}
	
	/**
	 * @Route("/clear", methods="DELETE")
	 * @Request(csrf=true)
	 * @return array
	 */
	public function clearAction()
	{
		$statistics = Statistic::findAll();
		
		foreach ( $statistics as $statistic ) {
			$this->deleteAction( $statistic->id );
		}
		
		return [ 'message' => 'success' ];
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
		if ( $statistic = Statistic::find( $id ) ) {
			if ( !App::user()->hasAccess( 'linker: manage statistics' ) ) {
				App::abort( 400, __( 'Access denied.' ) );
			}
			
			$statistic->delete();
		}
		
		return [ 'message' => 'success' ];
	}
	
}