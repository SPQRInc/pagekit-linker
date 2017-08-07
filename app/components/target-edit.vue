<template>
    <div class="uk-grid pk-grid-large pk-width-sidebar-large uk-form-stacked" data-uk-grid-margin>
        <div class="pk-width-content">
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title" :placeholder="'Enter Title' | trans"
                       v-model="target.title" v-validate:required>
                <p class="uk-form-help-block uk-text-danger"
                   v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="url" :placeholder="'Enter Target URL' | trans"
                       v-model="target.target_url" v-validate:required>
                <p class="uk-form-help-block uk-text-danger"
                   v-show="form.url.invalid">{{ 'Target URL cannot be blank.' | trans }}</p>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <div class="uk-panel">
                <div class="uk-form-row">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" class="uk-width-1-1" type="text" v-model="target.slug">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="target.status">
                            <option v-for="(id, status) in data.statuses" :value="id">{{status}}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Target' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" v-model="target.data.target" value="_blank">
                                {{ 'New Tab' | trans }}
                            </label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label>
                                <input type="radio" v-model="target.data.target" value="_self">
                                {{ 'Same Tab' | trans }}
                            </label>
                        </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-nofollow" class="uk-form-label">{{ 'Nofollow' | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <input id="form-nofollow" type="checkbox" v-model="target.data.nofollow">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-mask" class="uk-form-label">{{ 'Mask Link' | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <input id="form-mask" type="checkbox" v-model="target.data.mask">
                    </div>
                </div>
                <div class="uk-alert"
                     v-if="!target.data.mask">{{ 'Without masking the collection of click statistics is not possible!' | trans }}
                </div>
                <div v-if="target.data.mask" class="uk-form-row">
                    <label for="form-redirect" class="uk-form-label">{{ 'Redirection Type' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-redirect" class="uk-form-width-large" v-model="target.data.redirect">
                            <option value="301">{{ '301' | trans }}</option>
                            <option value="307">{{ '307' | trans }}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-href_class" class="uk-form-label">{{ 'Href Class' | trans }}</label>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <input id="form-href_class" type="text" class="uk-form-width-large" v-model="target.data.href_class">
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

module.exports = {

	props: ['target', 'data', 'form'],

	section: {
		label: 'Target'
	}

};

</script>