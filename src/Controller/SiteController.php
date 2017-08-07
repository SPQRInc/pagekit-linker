<?php

namespace Spqr\Linker\Controller;

use Pagekit\Application as App;
use Spqr\Linker\Model\Marker;
use Spqr\Linker\Model\Statistic;
use Spqr\Linker\Model\Target;

/**
 * Class SiteController
 * @package Spqr\Linker\Controller
 */
class SiteController
{
	
	/**
	 * @Route("/{id}", name="id", defaults={"id": NULL, "marker_id" = NULL}, methods="GET")
	 * @Request({"id": "int", "marker_id":"int"})
	 * @param string $id
	 * @param string $marker_id
	 *
	 * @return mixed
	 */
	public function redirectAction( $id = '', $marker_id = null )
	{
		$module = App::module( 'spqr/linker' );
		
		if ( !$target =
			Target::where( [ 'id = ?', 'status = ?', 'date < ?' ], [ $id, Target::STATUS_PUBLISHED, new \DateTime ] )
			      ->first() ) {
			App::abort( 404, __( 'Target not found!' ) );
		}
		
		if ( $module->config( 'statistics.collect_statistics' ) && $module->config(
				'statistics.collect_clicks'
			) ) {
			$statistic = new Statistic();
			if ( Marker::where( [ 'id = ?', 'target_id = ?' ], [ (int) $marker_id, (int) $id ] )->first() ) {
				$statistic->marker_id = (int) $marker_id;
			} else {
				$statistic->marker_id =
					Marker::where( [ 'target_id = ?', 'type = ?' ], [ (int) $id, 'default' ] )->first()->id;
			}
			
			$statistic->type = 'click';
			$statistic->date = new \DateTime();
			
			if ( $module->config( 'statistics.collect_referrer' ) ) {
				$statistic->referrer = App::request()->headers->get( 'referer' );
			}
			
			if ( $module->config( 'statistics.collect_ips' ) ) {
				$statistic->ip = App::request()->getClientIp();
			}
			
			$statistic->save();
		}
		
		return App::redirect( $target->target_url, [], $target->get( 'redirect' ) );
	}
	
}