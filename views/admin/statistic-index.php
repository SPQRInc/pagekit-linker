<?php $view->script( 'statistic-index', 'spqr/linker:app/bundle/statistic-index.js', 'vue' ); ?>

<div id="statistics" class="uk-form" v-cloak>
	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>
			<h2 class="uk-margin-remove"
			    v-if="!selected.length">{{ '{0} %count% Statistics|{1} %count% Statistic|]1,Inf[ %count% Statistics' | transChoice count {count:count} }}</h2>
			<template v-else>
				<h2 class="uk-margin-remove">{{ '{1} %count% Statistic selected|]1,Inf[ %count% Statistics selected' | transChoice selected.length {count:selected.length} }}</h2>
				<div class="uk-margin-left">
					<ul class="uk-subnav pk-subnav-icon">
						<li>
							<a class="pk-icon-delete pk-icon-hover"
							   title="Delete"
							   data-uk-tooltip="{delay: 500}"
							   @click="remove" v-confirm="'Delete Statistics?'"></a>
						</li>
					</ul>
				</div>
			</template>
			<div class="pk-search">
				<div class="uk-search">
					<input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
				</div>
			</div>
		</div>
	</div>
	<div class="uk-overflow-container">
		<table class="uk-table uk-table-hover uk-table-middle">
			<thead>
			<tr>
				<th class="pk-table-width-minimum">
					<input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
				<th class="pk-table-min-width-200">{{ 'Target' | trans }}
				</th>
				<th class="pk-table-min-width-200">{{ 'Marker' | trans }}
				</th>
				<th class="pk-table-min-width-200"
				    v-order:type="config.filter.order">{{ 'Type' | trans }}
				</th>
				<th class="pk-table-min-width-200"
				    v-order:date="config.filter.order">{{ 'Date' | trans }}
				</th>
			</tr>
			</thead>
			<tbody>
			<tr class="check-item" v-for="statistic in statistics" :class="{'uk-active': active(statistic)}">
				<td><input type="checkbox" name="id" :value="statistic.id"></td>
				<td>
					<a :href="$url.route('admin/linker/statistic/edit', { id: statistic.id })">{{ statistic.target.title
						}}</a>
				</td>
				<td>
					{{ statistic.marker.value ? statistic.marker.value :  'Not defined' | trans  }}
				</td>
				<td>
					{{ statistic.type == 'click' ? 'Click' : 'View' | trans  }}
				</td>
				<td>
					{{ statistic.date | date }}
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<h3 class="uk-h1 uk-text-muted uk-text-center"
	    v-show="statistics && !statistics.length">{{ 'No Statistics found.' | trans }}</h3>
	<v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1 || page > 0"></v-pagination>
</div>