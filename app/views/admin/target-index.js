window.targets = {

	el: '#targets',

	data: function () {
		return _.merge({
			targets: false,
			config: {
				filter: this.$session.get('targets.filter', {order: 'date desc', limit: 25})
			},
			pages: 0,
			count: '',
			selected: []
		}, window.$data);
	},
	ready: function () {
		this.resource = this.$resource('api/linker/target{/id}');

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

				this.$session.set('targets.filter', filter);
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
		active: function (target) {
			return this.selected.indexOf(target.id) != -1;
		},
		save: function (target) {
			this.resource.save({id: target.id}, {target: target}).then(function () {
				this.load();
				this.$notify('Target saved.');
			});
		},
		status: function (status) {

			var targets = this.getSelected();

			targets.forEach(function (target) {
				target.status = status;
			});

			this.resource.save({id: 'bulk'}, {targets: targets}).then(function () {
				this.load();
				this.$notify('Targets saved.');
			});
		},
		toggleStatus: function (target) {
			target.status = target.status === 1 ? 2 : 1;
			this.save(target);
		},
		remove: function () {

			this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
				this.load();
				this.$notify('Targets deleted.');
			});
		},
		copy: function () {

			if (!this.selected.length) {
				return;
			}

			this.resource.save({id: 'copy'}, {ids: this.selected}).then(function () {
				this.load();
				this.$notify('Targets copied.');
			});
		},
		load: function () {
			this.resource.query({filter: this.config.filter, page: this.config.page}).then(function (res) {

				var data = res.data;

				this.$set('targets', data.targets);
				this.$set('pages', data.pages);
				this.$set('count', data.count);
				this.$set('selected', []);
			});
		},
		getSelected: function () {
			return this.targets.filter(function (target) {
				return this.selected.indexOf(target.id) !== -1;
			}, this);
		},
		removeTargets: function () {
			this.resource.delete({id: 'bulk'}, {ids: this.selected}).then(function () {
				this.load();
				this.$notify('Targets(s) deleted.');
			});
		},
		getStatusText: function (target) {
			return this.statuses[target.status];
		}
	},
	components: {}
};
Vue.ready(window.targets);
