<?php

namespace Spqr\Linker\Model;

use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity(tableClass="@linker_statistic")
 */
class Statistic implements \JsonSerializable
{
	use ModelTrait;
	
	/** @Column(type="integer") @Id */
	public $id;
	
	/** @Column(type="integer") */
	public $marker_id;
	
	/** @Column(type="string") */
	public $type;
	
	/** @Column(type="string") */
	public $ip;
	
	/** @Column(type="string") */
	public $referrer;
	
	/** @Column(type="datetime") */
	public $date;
	
	/** @BelongsTo(targetEntity="Marker", keyFrom="marker_id") */
	public $marker;
	
	
	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		
		$data = [
			'marker' => $this->marker,
			'target' => $this->marker->target
		];
		
		return $this->toArray( $data );
	}
}