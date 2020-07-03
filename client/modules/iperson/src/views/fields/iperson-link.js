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
 ************************************************************************/

define('iperson:views/fields/iperson-link', 'views/fields/link', function (Dep) {

    return Dep.extend({

        re_initials: /^([^.]+)[ ](([^.]+[.])+[ ])?(.*)\s*$/,

        nameValue: '',

        data: function () {
	        var nameValue = this.model.has(this.nameName) ? this.model.get(this.nameName) : this.model.get(this.idName);
	        if (nameValue === null) {
	            nameValue = this.model.get(this.idName);
	        }
	        if (this.isReadMode() && !nameValue && this.model.get(this.idName)) {
	            nameValue = this.translate(this.foreignScope, 'scopeNames');
	        }
	
	        var iconHtml = null;
	        if (this.mode === 'detail') {
	            iconHtml = this.getHelper().getScopeColorIconHtml(this.foreignScope);
	        }
	
	        var re_initials = this.re_initials;
                var self = this;
	        nameValue = nameValue.replace(re_initials, function(match, lastname, initials, $3, firstname, offset, orig) {
                       if (lastname === undefined) { lastname = ''; } else { lastname = lastname.trim(); }
                       if (initials === undefined) { initials = ''; } else { initials = initials.trim(); }
                       if (firstname === undefined) { firstname = ''; } else { firstname = firstname.trim(); }
                       if (initials == '') {
                          return firstname + ' ' + lastname;
                       } else {
                          return initials + ' (' + firstname + ') ' + lastname;
                       }
	        });

                var obj = _.extend({
	            idName: this.idName,
	            nameName: this.nameName,
	            idValue: this.model.get(this.idName),
	            nameValue: nameValue,
	            foreignScope: this.foreignScope,
	            valueIsSet: this.model.has(this.idName),
	            iconHtml: iconHtml
	        }, Dep.prototype.data.call(this));

                obj.nameValue = nameValue;
                obj.value = nameValue;

                return obj;
        },

    });
});
