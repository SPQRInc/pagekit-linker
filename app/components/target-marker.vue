<template>
    <form class="uk-form uk-form-stacked" v-validator="formMarker" @submit.prevent = "add | valid">
        <div class="uk-form-row">
            <div class="uk-grid" data-uk-margin>
                <div class="uk-width-large-1-3">
                    <div class="uk-form-controls">
                        <select class="uk-form-width-small" v-model="newMarker.type">
                            <option value="url">{{ 'URL' | trans }}</option>
                            <option value="string">{{ 'String' | trans }}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-width-large-1-3">
                    <input class="uk-input-large"
                           type="text"
                           placeholder="{{ 'Value' | trans }}"
                           name="value"
                           v-model="newMarker.value"
                           v-validate:required>
                    <p class="uk-form-help-block uk-text-danger" v-show="formMarker.value.invalid">
                        {{ 'Invalid value.' | trans }}</p>
                </div>
                <div class="uk-width-large-1-3">
                    <div class="uk-form-controls">
                        <span class="uk-align-right">
                            <button class="uk-button" @click.prevent = "add | valid">
                                {{ 'Add' | trans }}
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <hr/>
    <div class="uk-alert"
         v-if="!data.target.marker.length">{{ 'You can add your first marker using the input field above. Go ahead!' | trans }}
    </div>
    <div class="uk-form-row" v-if="data.target.marker.length" v-for="marker in data.target.marker">
        <div v-if="marker.type != 'default'" class="uk-grid" data-uk-margin>
            <div class="uk-width-large-1-3">
                <div class="uk-form-controls">
                    <select id="form-type{{$index}}" class="uk-form-width-small" v-model="marker.type" :disabled = "marker.type == 'default'">
                        <option value="url">{{ 'URL' | trans }}</option>
                        <option value="string">{{ 'String' | trans }}</option>
                        <option v-if="marker.type == 'default'" value="default" disabled>{{ 'Default Marker' | trans }}</option>
                    </select>
                </div>
            </div>
            <div class="uk-width-large-1-3">
                <input id="form-value{{$index}}" class="uk-input-large"
                       type="text"
                       placeholder="{{ 'Marker' | trans }}"
                       v-model="marker.value" :disabled = "marker.type == 'default'">
            </div>
            <div class="uk-width-large-1-3">
                <span class="uk-align-right">
                    <button class="uk-button uk-button-danger" :disabled = "marker.type == 'default'" @click.prevent = "remove(marker)">
                        <i class="uk-icon-remove"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

</template>

<script>

module.exports = {

    section: {
        label: 'Marker',
        priority: 100
    },

    props: ['data'],

    data: function () {
        return {
			data: this.data,
			newMarker: {
				'type': 'url',
				'value': ''
            }
        }
    },

    methods: {
        add: function add(e) {

            e.preventDefault();
            if (!this.newMarker || !this.newMarker.type || !this.newMarker.value) return;
			this.data.target.marker.push({
				type: this.newMarker.type,
				value: this.newMarker.value
			});

			this.newMarker = {
				type: 'url',
				value: ''
			};

        },
        remove: function (mark) {
            this.data.target.marker.$remove(mark);
        }
    }
};

window.target.components['target-marker'] = module.exports;

</script>