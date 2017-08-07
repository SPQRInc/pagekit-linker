<?php

namespace Spqr\Linker\Event;

use Spqr\Linker\Model\Marker;
use Spqr\Linker\Model\Target;
use Spqr\Linker\Model\Statistic;

use Pagekit\Event\EventSubscriberInterface;

class StatisticListener implements EventSubscriberInterface
{
	public function onStatisticsChange($event, Statistic $statistic)
	{
		$marker = Marker::where(['id' => $statistic->marker_id])->first();
		$target_id = $marker->target_id;
		Target::updateStatistic($target_id);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function subscribe()
	{
		return [
			'model.statistic.saved' => 'onStatisticsChange',
			'model.statistic.deleted' => 'onStatisticsChange'
		];
	}
}