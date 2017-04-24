define([
    'jquery',
    'uiComponent',
    'underscore',
], function($, Component, _) {
    'use strict';

    var login = 'login';

    return Component.extend({
        defaults: {
            url: null,
            registries: [],
            selected: []
        },

        initObservable: function() {
            this._super()
                .observe(this.selected);

            return this;
        },

        defineBehaviour: function(data, event) {
            if (this.registries.length == 1) {
                event.stopPropagation();
                this.addProduct();
            }
        },

        getData: function() {
            var data = $('#product_addtocart_form').serializeArray();
            if (_.size(this.selected) > 0) {
                data.push({name: 'registries', value: _.map(this.selected, function(value) { return value })});
            }

            return data;
        },

        addProduct: function() {
            $.ajax({
                url: this.url,
                method: 'POST',
                data: this.getData(),
                dataType: 'json',
                showLoader: true,
                success: function (response) {
                    var giftr = $('[data-block="addtogiftr"]');
                    giftr.find('[data-role="dropdownDialog"]').dropdownDialog("close");
                    if (response.status == this.login) {
                        setLocation(response.message);
                    }
                }
            });
        }
    });
});
