window.target = {

	el: '#target',

	data: function () {
		return {
			data: window.$data,
			target: window.$data.target,
			sections: []
		}
	},

	created: function () {

		var sections = [];

		_.forIn(this.$options.components, function (component, name) {

			var options = component.options || {};

			if (options.section) {
				sections.push(_.extend({name: name, priority: 0}, options.section));
			}

		});

		this.$set('sections', _.sortBy(sections, 'priority'));

		this.resource = this.$resource('api/linker/target{/id}');
	},

	ready: function () {
		this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});
	},

	methods: {

		save: function () {
			var data = {target: this.target, id: this.target.id};

			this.$broadcast('save', data);

			this.resource.save({id: this.target.id}, data).then(function (res) {

				var data = res.data;

				if (!this.target.id) {
					window.history.replaceState({}, '', this.$url.route('admin/linker/target/edit', {id: data.target.id}))
				}

				this.$set('target', data.target);

				this.$notify('Target saved.');

			}, function (res) {
				this.$notify(res.data, 'danger');
			});
		}

	},

	components: {
		settings: require('../../components/target-edit.vue')
	}
};

Vue.ready(window.target);