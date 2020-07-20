<?php
# vim: ts=4 sw=4 et:
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

namespace Espo\Modules\IPerson\Helpers;

use Espo\Core\Utils\Config;

use Espo\ORM\Entity;

class IPersonHelper extends \Espo\Core\ORM\Helper
{

    public function formatPersonName(Entity $entity, string $field)
    {
        $first = $entity->get('first' . ucfirst($field));
        $last = $entity->get('last' . ucfirst($field));
        $middle = $entity->get('middle' . ucfirst($field));
        $initials = $entity->get('initials' . ucfirst($field));

        $oke = true;
        $str = $this->doFormat($last, $initials, $first, $middle, $oke);

        if (!$oke) {
            return parent::formatPersonName($entity, $field);
        } else {
            return $str;
        }
    }

    private function doFormat($last, $initials, $first, $middle, &$oke)
    {
        $format = $this->config->get('personNameFormat');

        if (!$first) $first = '';
        if (!$last) $last = '';
        if (!$middle) $middle = '';
        if (!$initials) $initials = '';
        if ($initials != '' && $first != '') {
            $first = '(' . $first . ')';
        }
		
        switch ($format) {
            case 'lastFirst':
            	$nm = $last . ', ' . $initials . ' ' . $first;
		    break;

            case 'lastFirstMiddle':
            	$nm = $last . ', ' . $initials . ' ' . $first . ' ' . $middle;
		    break;

            case 'firstMiddleLast':
            	$nm = $initials . ' ' . $first . ' ' . $middle . ' ' . $last;
		    break;

            default: // firstLast
            	$nm = $initials . ' ' . $first . ' ' . $last;
		    break;
        }
        
        $nm = trim(str_replace('  ', ' ', $nm));
        
        if ($nm != '') {
        	return $nm;
        }

        // Nothing matched, so we call the base function
        $oke = false;

        return "";
    }

    public function formatForeignPersonName(Entity $entity, string $field)
    {
	    $sep = "@#@";
	    $value = $entity->get($field);	 /// Will be last, initials, first, middle
	    $a = explode($sep, $value);
	    $last = $a[0];
	    $initials = $a[1];
	    $first = $a[2];
	    $middle = $a[3];

        $oke = true;
	    $str = $this->doFormat($last, $initials, $first, $middle, $oke);
        if ($oke) {
            return $str;
        } else {
            return $value;
        }
    }
}
