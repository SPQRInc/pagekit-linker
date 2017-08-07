window.statistics = {

	el: '#statistics',

	data: function () {
		return _.merge({
			statistics: false,
			config: {
				filter: this.$session.get('statistics.filter', {order: 'date desc', limit: 25})
			},
			pages: 0,
			count: '',
			selected: []
		}, window.$data);
	},
	ready: function () {
		this.resource = this.$resource('api/linker/statistic{/id}');

		this.$watch('config.page', this.load, {immediate: true});
	},
	watch: {
		'config.filter': {
			handler: function (filter) {
				if (this.config.page) {
					this.config.page = 0;
				} else {
					this.load();
				}

				this.$session.set('statistics.filter', filter);
			},
			deep: true
		}
	},
	computed: {
		statusOptions: function () {
			var options = _.map(this.$data.statuses, function (status, id) {
				return {text: status, value: id};
			});

			return [{label: this.$trans('Filter by'), options: options}];
		}
	},
	methods: {
		active: function (statistic) {
			return this.selected.indexOf(statistic.id) != -1;
		},
		save: function (statistic) {
			this.resource.save({id: statistic.id}, {statistic: statistic}).then(function () {
				this.load();
				this.$notify('Statistic saved.');
			});
		},
		status: function (status) {

			var statistics = this.getSelected();

			statistics.forEach(function (statistic) {
				statistic.status = status;
			});

			this.resource.save({id: 'bulk'}, {statistics: statistics}).then(function () {
				this.load();
				this.$notify('Statistics saved.');
			});
		},
		toggleStatus: function (statistic) {
			statistic.status = statistic.status === 1 ? 2 : 1;
			this.save(statistic);
		},
		remove: function () {

			this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
				this.load();
				this.$notify('Statistics deleted.');
			});
		},
		load: function () {
			this.resource.query({filter: this.config.filter, page: this.config.page}).then(function (res) {

				var data = res.data;

				this.$set('statistics', data.statistics);
				this.$set('pages', data.pages);
				this.$set('count', data.count);
				this.$set('selected', []);
			});
		},
		getSelected: function () {
			return this.statistics.filter(function (statistic) {
				return this.selected.indexOf(statistic.id) !== -1;
			}, this);
		},
		removeStatistics: function () {
			this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
				this.load();
				this.$notify('Statistics(s) deleted.');
			});
		},
		getStatusText: function (statistic) {
			return this.statuses[statistic.status];
		}
	},
	components: {}
};
Vue.ready(window.statistics);
