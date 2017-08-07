<template>
    <div class="uk-grid pk-grid-large pk-width-sidebar-large uk-form-stacked" data-uk-grid-margin>
        <div class="pk-width-content">
            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Target' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p>{{ statistic.target.title }}</p>
                </div>
            </div>
            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Marker Value' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p>	{{ statistic.marker.value ? statistic.marker.value :  'Not defined' | trans  }} </p>
                </div>
            </div>
            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Marker Type' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p> {{ statistic.type == 'click' ? 'Click' : 'View' | trans  }} </p>
                </div>
            </div>
        </div>
        <div class="pk-width-sidebar">
            <div class="uk-panel">
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'IP' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p> {{ statistic.ip ? statistic.ip :  'Not collected' | trans  }} </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Referrer' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p> {{ statistic.referrer ? statistic.referrer :  'Not collected' | trans  }} </p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Date' | trans }}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p>{{ statistic.date | date }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

module.exports = {

	props: ['statistic', 'data', 'form'],

	section: {
		label: 'Statistic'
	}

};

</script>