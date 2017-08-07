<?php

namespace Spqr\Linker\Model;

/**
 * @Entity(tableClass="@linker_marker")
 */
class Marker implements \JsonSerializable
{
	use MarkerModelTrait;
	
	/** @Column(type="integer") @Id */
	public $id;
	
	/** @Column(type="integer") */
	public $target_id;
	
	/** @Column(type="string") */
	public $type;
	
	/** @Column(type="string") */
	public $value;
	
	/** @BelongsTo(targetEntity="Target", keyFrom="target_id") */
	public $target;
	
	
	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		$data = [
			'statistic' => $this->getStatistics()
		];
		
		return $this->toArray( $data );
	}
}