<?php $view->script( 'settings', 'spqr/linker:app/bundle/settings.js', [ 'vue' ] ); ?>

<div id="settings" class="uk-form uk-form-horizontal" v-cloak>
	<div class="uk-grid pk-grid-large" data-uk-grid-margin>
		<div class="pk-width-sidebar">
			<div class="uk-panel">
				<ul class="uk-nav uk-nav-side pk-nav-large" data-uk-tab="{ connect: '#tab-content' }">
					<li><a><i class="pk-icon-large-settings uk-margin-right"></i> {{ 'General' | trans }}</a></li>
					<li><a><i class="uk-icon-bar-chart uk-margin-right"></i> {{ 'Statistics' | trans }}</a></li>
					<li><a><i class="uk-icon-puzzle-piece uk-margin-right"></i> {{ 'Exclusions' | trans }}</a></li>
				</ul>
			</div>
		</div>
		<div class="pk-width-content">
			<ul id="tab-content" class="uk-switcher uk-margin">
				<li>
					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>
							<h2 class="uk-margin-remove">{{ 'General' | trans }}</h2>
						</div>
						<div data-uk-margin>
							<button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}
							</button>
						</div>
					</div>
					<div class="uk-form-row">
						<span class="uk-form-label">{{ 'Default Target' | trans }}</span>
						<div class="uk-form-controls uk-form-controls-text">
							<p class="uk-form-controls-condensed">
								<label>
									<input type="radio" v-model="config.target" value="_blank">
									{{ 'New Tab' | trans }}
								</label>
							</p>
							<p class="uk-form-controls-condensed">
								<label>
									<input type="radio" v-model="config.target" value="_self">
									{{ 'Same Tab' | trans }}
								</label>
							</p>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-nofollow" class="uk-form-label">{{ 'Default Nofollow Value' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-nofollow" type="checkbox" v-model="config.nofollow">
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-redirect" class="uk-form-label">{{ 'Default Redirection Type' | trans
							}}</label>
						<div class="uk-form-controls">
							<select id="form-redirect" class="uk-form-width-large" v-model="config.redirect">
								<option value="301">{{ '301' | trans }}</option>
								<option value="307">{{ '307' | trans }}</option>
							</select>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-href_class" class="uk-form-label">{{ 'Default Href Class' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<p class="uk-form-controls-condensed">
								<input id="form-href_class" type="text" class="uk-form-width-large" v-model="config
								.href_class">
							</p>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-limit" class="uk-form-label">{{ 'Limit Matches per String-Marker' | trans
							}}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<p class="uk-form-controls-condensed">
								<input id="form-limit" type="number" class="uk-form-width-large"
								       v-model="config.limit" min="0" number>
							</p>
						</div>
					</div>
				</li>
				<li>
					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>
							<h2 class="uk-margin-remove">{{ 'Statistics' | trans }}</h2>
						</div>
						<div data-uk-margin>
							<button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}
							</button>
						</div>
					</div>
					<div class="uk-form-row">
						<label for="form-collectstatistics" class="uk-form-label">{{ 'Collect Statistics' | trans
							}}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-collectstatistics" type="checkbox" v-model="config.statistics.collect_statistics">
						</div>
					</div>
					<div v-if="config.statistics.collect_statistics" class="uk-form-row">
						<label for="form-collectviews" class="uk-form-label">{{ 'Collect Views' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-collectviews" type="checkbox" v-model="config.statistics.collect_views">
						</div>
					</div>
					<div v-if="config.statistics.collect_statistics" class="uk-form-row">
						<label for="form-collectclicks" class="uk-form-label">{{ 'Collect Clicks' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-collectclicks" type="checkbox" v-model="config.statistics.collect_clicks">
						</div>
					</div>
					<div v-if="config.statistics.collect_statistics" class="uk-form-row">
						<label for="form-collectips" class="uk-form-label">{{ 'Collect IPs' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-collectips" type="checkbox" v-model="config.statistics.collect_ips">
						</div>
					</div>
					<div v-if="config.statistics.collect_statistics" class="uk-form-row">
						<label for="form-collectreferrer" class="uk-form-label">{{ 'Collect Referrer' | trans }}</label>
						<div class="uk-form-controls uk-form-controls-text">
							<input id="form-collectreferrer" type="checkbox" v-model="config.statistics
							.collect_referrer">
						</div>
					</div>
				</li>
				<li>
					<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
						<div data-uk-margin>
							<h2 class="uk-margin-remove">{{ 'Exclusions' | trans }}</h2>
						</div>
						<div data-uk-margin>
							<button class="uk-button uk-button-primary" @click.prevent="save">{{ 'Save' | trans }}
							</button>
						</div>
					</div>
					<form class="uk-form uk-form-stacked" v-validator="formExclusions" @submit.prevent="add | valid">
						<div class="uk-form-row">
							<div class="uk-grid" data-uk-margin>
								<div class="uk-width-large-1-2">
									<input class="uk-input-large"
									       type="text"
									       placeholder="{{ 'Tag' | trans }}"
									       name="exclusion"
									       v-model="newExclusion"
									       v-validate:required>
									<p class="uk-form-help-block uk-text-danger" v-show="formExclusions.exclusion.invalid">
										{{ 'Invalid value.' | trans }}</p>
								</div>
								<div class="uk-width-large-1-2">
									<div class="uk-form-controls">
										<span class="uk-align-right">
											<button class="uk-button" @click.prevent="add | valid">
												{{ 'Add' | trans }}
											</button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</form>
					<hr />
					<div class="uk-alert"
					     v-if="!config.exclusions.length">{{ 'You can add your first exclusion using the input field above. Go ahead!' | trans }}
					</div>
					<ul class="uk-list uk-list-line" v-if="config.exclusions.length">
						<li v-for="exclusion in config.exclusions">
							<input class="uk-input-large"
							       type="text"
							       placeholder="{{ 'Tag' | trans }}"
							       v-model="exclusion">
							<span class="uk-align-right">
								<button @click="remove(exclusion)" class="uk-button uk-button-danger">
									<i class="uk-icon-remove"></i>
								</button>
							</span>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</div>