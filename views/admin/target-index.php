<?php $view->script( 'target-index', 'spqr/linker:app/bundle/target-index.js', 'vue' ); ?>

<div id="targets" class="uk-form" v-cloak>
	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>
			<h2 class="uk-margin-remove"
			    v-if="!selected.length">{{ '{0} %count% Targets|{1} %count% Target|]1,Inf[ %count% Targets' | transChoice count {count:count} }}</h2>
			<template v-else>
				<h2 class="uk-margin-remove">{{ '{1} %count% Target selected|]1,Inf[ %count% Targets selected' | transChoice selected.length {count:selected.length} }}</h2>
				<div class="uk-margin-left">
					<ul class="uk-subnav pk-subnav-icon">
						<li>
							<a class="pk-icon-check pk-icon-hover"
							   title="{{ Enable | trans }}"
							   data-uk-tooltip="{delay: 500}"
							   @click="status(1)"></a>
						</li>
						<li>
							<a class="pk-icon-block pk-icon-hover"
							   title="{{ Disable | trans }}"
							   data-uk-tooltip="{delay: 500}"
							   @click="status(2)"></a>
						</li>
						<li>
							<a class="pk-icon-copy pk-icon-hover"
							   title="Copy"
							   data-uk-tooltip="{delay: 500}"
							   @click="copy"></a>
						</li>
						<li>
							<a class="pk-icon-delete pk-icon-hover"
							   title="Delete"
							   data-uk-tooltip="{delay: 500}"
							   @click="remove" v-confirm="'Delete Targets?'"></a>
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
		<div data-uk-margin>
			<a class="uk-button uk-button-primary"
			   :href="$url.route('admin/linker/target/edit')">{{ 'Add Target' | trans }}</a>
		</div>
	</div>
	<div class="uk-overflow-container">
		<table class="uk-table uk-table-hover uk-table-middle">
			<thead>
			<tr>
				<th class="pk-table-width-minimum">
					<input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
				<th class="pk-table-min-width-200"
				    v-order:title="config.filter.order">{{ 'Title' | trans }}
				</th>
				<th class="pk-table-width-100 uk-text-center">
					<input-filter :title="$trans('Status')"
					              :value.sync="config.filter.status"
					              :options="statusOptions"></input-filter>
				</th>
				<th v-if="config.statistics.collect_statistics" class="pk-table-min-width-200"
				    v-order:viewcount="config.filter.order">{{ 'Views' | trans }}
				</th>
				<th v-if="config.statistics.collect_statistics" class="pk-table-min-width-200"
				    v-order:clickcount="config.filter.order">{{ 'Clicks' | trans }}
				</th>
				<th class="pk-table-width-200 pk-table-min-width-200">{{ 'URL' | trans }}</th>
			</tr>
			</thead>
			<tbody>
			<tr class="check-item" v-for="target in targets" :class="{'uk-active': active(target)}">
				<td><input type="checkbox" name="id" :value="target.id"></td>
				<td>
					<a :href="$url.route('admin/linker/target/edit', { id: target.id })">{{ target.title }}</a>
				</td>
				<td class="uk-text-center">
					<a :title="getStatusText(target)" :class="{
                                'pk-icon-circle-danger': target.status == 2,
                                'pk-icon-circle-success': target.status == 1,
                                'pk-icon-circle': target.status == 0

                            }" @click="toggleStatus(target)"></a>
				</td>
				<td v-if="config.statistics.collect_statistics">
					{{ target.viewcount ? target.viewcount : 0 }}
				</td>
				<td v-if="config.statistics.collect_statistics">
					{{ target.clickcount ? target.clickcount : 0 }}
				</td>
				<td class="pk-table-text-break">
					<a target="_blank" v-if="target.published && target.url" :href="this.$url.route(target.url.substr
					(1))">{{ decodeURI(target.url) }}</a>
					<span v-if="!target.published && target.url">{{ decodeURI(target.url) }}</span>
					<span v-if="!target.url">{{ 'Disabled' | trans }}</span>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<h3 class="uk-h1 uk-text-muted uk-text-center"
	    v-show="targets && !targets.length">{{ 'No Targets found.' | trans }}</h3>
	<v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1 || page > 0"></v-pagination>
</div>