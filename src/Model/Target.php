<?php

namespace Spqr\Linker\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@linker_target")
 */
class Target implements \JsonSerializable
{
	use TargetModelTrait, TargetTrait, DataModelTrait;
	
	/* Target draft. */
	const STATUS_DRAFT = 0;
	
	/* Target published. */
	const STATUS_PUBLISHED = 1;
	
	/* Target unpublished. */
	const STATUS_UNPUBLISHED = 2;
	
	/** @Column(type="integer") @Id */
	public $id;
	
	/** @Column(type="integer") */
	public $status;
	
	/** @Column(type="string") */
	public $slug;
	
	/** @Column(type="string") */
	public $title;
	
	/** @Column(type="string") */
	public $target_url = '';
	
	/** @Column(type="integer") */
	public $clickcount = 0;
	
	/** @Column(type="integer") */
	public $viewcount = 0;
	
	/** @Column(type="datetime") */
	public $date;
	
	/** @Column(type="datetime") */
	public $modified;
	
	/** @var array */
	protected static $properties = [
		'published' => 'isPublished'
	];
	
	/**
	 * @param $item
	 *
	 * @return mixed
	 */
	public static function getPrevious( $item )
	{
		return self::where(
			[ 'date > ?', 'date < ?', 'status = 1' ],
			[
				$item->date,
				new \DateTime
			]
		)->orderBy( 'date', 'ASC' )->first();
	}
	
	/**
	 * @param $item
	 *
	 * @return mixed
	 */
	public static function getNext( $item )
	{
		return self::where( [ 'date < ?', 'status = 1' ], [ $item->date ] )->orderBy( 'date', 'DESC' )->first();
	}
	
	/**
	 * @return mixed
	 */
	public function getStatusText()
	{
		$statuses = self::getStatuses();
		
		return isset( $statuses[ $this->status ] ) ? $statuses[ $this->status ] : __( 'Unknown' );
	}
	
	/**
	 * @return array
	 */
	public static function getStatuses()
	{
		return [
			self::STATUS_PUBLISHED   => __( 'Published' ),
			self::STATUS_UNPUBLISHED => __( 'Unpublished' ),
			self::STATUS_DRAFT       => __( 'Draft' )
		];
	}
	
	/**
	 * @return bool
	 */
	public function isPublished()
	{
		return $this->status === self::STATUS_PUBLISHED && $this->date < new \DateTime;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize()
	{
		$data = [
			'marker' => $this->getMarker(),
			'url'    => App::url( '@linker/id', [ 'id' => $this->id ], 'base' )
		];
		
		return $this->toArray( $data );
	}
}