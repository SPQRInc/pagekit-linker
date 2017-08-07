<?php

namespace Spqr\Linker\Model;

/**
 * Trait TargetTrait
 * @package Spqr\Linker\Model
 */
trait TargetTrait
{
	/**
	 * @HasMany(targetEntity="Marker", keyFrom="id", keyTo="target_id")
	 */
	public $marker;
	
	/**
	 * @return array
	 */
	public function getMarker()
	{
		if ( $this->marker ) {
			return array_values(
				array_map(
					function( $marker ) {
						return $marker;
					},
					$this->marker
				)
			);
		}
		
		return [];
	}
	
	/**
	 * @param array $markers
	 */
	public function saveMarker( array $markers )
	{
		$stored     = Marker::where( [ 'target_id' => $this->id ] )->get();
		$markerlist = [];
		
		foreach ( $markers as $marker ) {
			if ( array_key_exists( 'id', $marker ) ) {
				$m        = Marker::where( [ 'id' => $marker[ 'id' ] ] )->first();
				$m->value = $marker[ 'value' ];
				$m->type  = $marker[ 'type' ];
				$m->save();
				
				$markerlist[ $m->id ] = $m;
			} else {
				$m = Marker::create(
					[ 'target_id' => $this->id, 'type' => $marker[ 'type' ], 'value' => $marker[ 'value' ] ]
				);
				$m->save();
				
				$markerlist[ $m->id ] = $m;
			}
		}
		
		foreach ( $stored as $s ) {
			if ( !array_key_exists( $s->id, $markerlist ) ) {
				unset( $markerlist[ $s->id ] );
				$s->delete();
			}
		}
	}
}