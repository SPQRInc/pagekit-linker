<?php $view->script(
	'statistic-edit',
	'spqr/linker:app/bundle/statistic-edit.js',
	[
		'vue',
		'editor'
	]
) ?>

<form id="statistic" class="uk-form" v-validator="form" @submit.prevent="save | valid" v-cloak>
	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div data-uk-margin>
			<h2 class="uk-margin-remove" v-if="statistic.id">{{ 'View Statistic' | trans }}</h2>
		</div>
		<div data-uk-margin>
			<a class="uk-button uk-margin-small-right"
			   :href="$url.route('admin/linker/statistic')">{{ statistic.id ? 'Close' : 'Cancel' | trans }}</a>
		</div>
	</div>
	<ul class="uk-tab" v-el:tab v-show="sections.length > 1">
		<li v-for="section in sections"><a>{{ section.label | trans }}</a></li>
	</ul>
	<div class="uk-switcher uk-margin" v-el:content>
		<div v-for="section in sections">
			<component :is="section.name"
			           :statistic.sync="statistic"
			           :data.sync="data"
			           :form="form"></component>
		</div>
	</div>
</form>