<?php
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

namespace Espo\Modules\IPerson\Core\Formula\Functions\IpersonGroup;

use Espo\Core\Exceptions\Error;

class NameType extends \Espo\Core\Formula\Functions\Base
{
    protected function getFormat()
    {
        return "firstMiddleLast";
    }

    protected function getFormattedName($last, $initials, $first, $middle)
    {
        $format = $this->getFormat();
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
            case 'lastFirstMiddle':
                $nm = $last . ', ' . $initials . ' ' . $first . ' ' . $middle;
            case 'firstMiddleLast':
                $nm = $initials . ' ' . $first . ' ' . $middle . ' ' . $last;
            default: // firstLast
                $nm = $initials . ' ' . $first . ' ' . $last;
        }
        
        $nm = trim(str_replace('  ', ' ', $nm));

        return $nm;
    }

    public function process(\StdClass $item)
    {
        if (!property_exists($item, 'value')) {
            throw new Error();
        }

        if (!is_array($item->value)) {
            throw new Error();
        }

        if (count($item->value) != 1) {
            throw new Error("iperson\name() has only one argument");
        }

        $name = $this->evaluate($item->value[0]);

        // IPerson gives back last - initials - first - middle
        $parts = explode('@#@', $name);
	if (count($parts) != 4) {
            return join(' ', $parts);
        } else {
            return $this->getFormattedName($parts[0], $parts[1], $parts[2], $parts[3]);
        }
    }
}

