module.exports = [
	{
		entry: {
			"settings": "./app/views/admin/settings.js",
			"target-index": "./app/views/admin/target-index",
			"target-edit": "./app/views/admin/target-edit",
			"target-marker": "./app/components/target-marker.vue",
			"statistic-index": "./app/views/admin/statistic-index",
			"statistic-edit": "./app/views/admin/statistic-edit"
		},
		output: {
			filename: "./app/bundle/[name].js"
		},
		module: {
			loaders: [
				{test: /\.vue$/, loader: "vue"},
				{test: /\.js$/, exclude: /node_modules/, loader: "babel-loader"}
			]
		}
	}
];