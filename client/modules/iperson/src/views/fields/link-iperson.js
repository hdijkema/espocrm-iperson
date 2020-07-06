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

define('iperson:views/fields/link-iperson', [], function () {
    return {
        getFormattedName: function(format, last_initials_first_middle) {
            if (last_initials_first_middle === null) {
                return '';
            } else if (last_initials_first_middle === undefined) {
                return '';
            } else {
                var parts = last_initials_first_middle.split('@#@');
                if (parts.length != 4) { return last_initials_first_middle; }
                return this.internalFormattedName(format, parts[1], parts[2], parts[3], parts[0]);
            }
        },
    	
    	internalFormattedName: function(format, initials, firstname, middle, last) {
            
            if (initials !== '') { 
            	if (firstname !== '') { firstname = '(' + firstname + ')'; }
            }
            
            var fmt_fml = function() {
            	return initials + ' ' + firstname + ' ' + middle + ' ' + last;
            };
            
            var fmt_lfm = function() {
            	return last + ', ' + initials + ' ' + firstname + ' ' + middle;
            };
            
            if (format == 'lastFirstMiddle' || format == 'lastFirst') {
            	return fmt_lfm().replace('  ', ' ').trim();
            } else {
            	return fmt_fml().replace('  ', ' ').trim();
            }
    	},

    };
});
