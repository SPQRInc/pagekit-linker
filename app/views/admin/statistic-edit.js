window.statistic = {

	el: '#statistic',

	data: function () {
		return {
			data: window.$data,
			statistic: window.$data.statistic,
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

		this.resource = this.$resource('api/linker/statistic{/id}');
	},

	ready: function () {
		this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});
	},


	components: {
		settings: require('../../components/statistic-edit.vue')
	}
};

Vue.ready(window.statistic);