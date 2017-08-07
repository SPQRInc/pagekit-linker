<?php

namespace Spqr\Linker\Model;

use Pagekit\Database\ORM\ModelTrait;

/**
 * Class MarkerModelTrait
 * @package Spqr\Linker\Model
 */
trait MarkerModelTrait
{
	use ModelTrait;
	
	/**
	 * @HasMany(targetEntity="Statistic", keyFrom="id", keyTo="marker_id")
	 */
	public $statistic;
	
	/**
	 * @return array
	 */
	public function getStatistics()
	{
		if ( $this->statistic ) {
			return array_values(
				array_map(
					function( $statistic ) {
						return $statistic;
					},
					$this->statistic
				)
			);
		}
		
		return [];
	}
	
	/**
	 * @Deleting
	 */
	public static function deleting($event, Marker $marker)
	{
		self::getConnection()->delete('@linker_statistic', ['marker_id' => $marker->id]);
	}

}