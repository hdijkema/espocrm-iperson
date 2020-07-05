/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 * 
 * IPerson - Open source plugin module for EspoCRM - Contacts with initials
 * 2020 Hans Dijkema
 * Based on the work of PersonPlus by Omar A Gonsenheim
 ************************************************************************/

define('iperson:views/fields/iperson-name', 'views/fields/person-name', function (Dep) {

    return Dep.extend({

        type: 'ipersonName',

        detailTemplate: 'iperson:fields/iperson-name/detail',

        editTemplate: 'iperson:fields/iperson-name/edit',

        editTemplateLastFirst: 'iperson:fields/iperson-name/edit-last-first',

        editTemplateLastFirstMiddle: 'iperson:fields/iperson-name/edit-last-first-middle',

        editTemplateFirstMiddleLast: 'iperson:fields/iperson-name/edit-first-middle-last',

        data: function () {
            this.inDataCalc = true;

            var data = Dep.prototype.data.call(this);

            this.inDataCalc = false;

            if (this.model === 'edit') {
               data.initialsMaxlength = this.model.getFieldParam(this.initialsField, 'maxLength');
            }

            data.initialsValue = this.model.get(this.initialsField);

            data.valueIsSet = data.valueIsSet || this.model.has(this.initialsField);

            data.isNotEmpty = data.isNotEmpty || !!data.initialsValue;

            if (data.isNotEmpty && (this.mode === 'detail' || this.mode === 'list' || this.mode === 'listLink')) {
                data.formattedValue = this.getFormattedValue();
            }

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);
            var ucName = Espo.Utils.upperCaseFirst(this.name)
            this.initialsField = 'initials' + ucName;
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode === 'edit') {
                this.$initials = this.$el.find('[data-name="' + this.initialsField + '"]');

                this.$initials.on('change', function () {
                    this.trigger('change');
                }.bind(this));
            }
        },

        getFormattedValue: function () {
 
            if (this.inDataCalc) return '';

            var format = this.getFormat();

            var value = '';

            var salutation = this.model.get(this.salutationField);
            var first = this.model.get(this.firstField);
            var last = this.model.get(this.lastField);
            var middle = this.model.get(this.middleField);
            var initials = this.model.get(this.initialsField);

			if (salutation === null) { salutation = ''; }
			if (initials === null) { initials = ''; }
			if (first === null) { first = ''; }
			if (middle === null) { middle = ''; }
			if (last === null) { last = ''; }
			if (initials !== '') { first = '(' + first + ')'; }
			
			console.log('hi!');

            if (salutation !== '') { salutation = this.getLanguage().translateOption(salutation, 'salutationName', this.model.entityType); }
			
			var fmt = function() {
            	if (format === 'firstMiddleLast') {
            		return salutation + ' ' + initials + ' ' + first + ' ' + middle + ' ' + last;
            	} else if (format === 'lastFirst') {
            		return last + ', ' + salutation + ' ' + initials + ' ' + first;
            	} else if (format === 'lastFirstMiddle') {
            		return last + ', ' + salutation + ' ' + initials + ' ' + first + ' ' + middle;
            	} else { // firstLast 
            		return salutation + ' ' + initials + ' ' + first + ' ' + last;
            	}
			}
			
			return fmt().replace('  ', ' ').trim();
        },

        fetch: function (form) {
            var data = Dep.prototype.fetch.call(this, form);
            data[this.initialsField] = this.$initials.val().trim() || null;
            return data;
        },

    });
});
