<?php

namespace Spqr\Linker\Model;

use Pagekit\Database\ORM\ModelTrait;

/**
 * Class TargetModelTrait
 * @package Spqr\Linker\Model
 */
trait TargetModelTrait
{
	use ModelTrait;
	
	public static function updateStatistic($id)
	{
		$stats  = [];
		$views  = 0;
		$clicks = 0;
		
		$target = self::query()->related('marker')->where('id = ?', [$id])->first();
		
		if($target && !empty($target->marker)){
			foreach ( $target->marker as $marker ) {
				$views             =
					$views + Statistic::where( [ 'marker_id' => $marker->id, 'type' => 'view' ] )->count();
				$stats[ 'views' ]  = $views;
				$clicks            =
					$clicks + Statistic::where( [ 'marker_id' => $marker->id, 'type' => 'click' ] )->count();
				$stats[ 'clicks' ] = $clicks;
			}
		}
		
		self::where(compact('id'))->update(['clickcount' => $stats['clicks']]);
		self::where(compact('id'))->update(['viewcount' => $stats['views']]);
	}
	
	/**
	 * @Saving
	 */
	public static function saving( $event, Target $target )
	{
		$target->modified = new \DateTime();
		$i              = 2;
		$id             = $target->id;
		while ( self::where( 'slug = ?', [ $target->slug ] )->where(
			function( $query ) use ( $id ) {
				if ( $id ) {
					$query->where( 'id <> ?', [ $id ] );
				}
			}
		)->first() ) {
			$target->slug = preg_replace( '/-\d+$/', '', $target->slug ) . '-' . $i++;
		}
	}
	
	/**
	 * @Deleting
	 */
	public static function deleting($event, Target $target)
	{
		self::getConnection()->delete('@linker_marker', ['target_id' => $target->id]);
	}

}