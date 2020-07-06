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
 * 
 * IPerson - Open source plugin module for EspoCRM - Contacts with initials
 * 2020 Hans Dijkema
 * Based on the work of PersonPlus by Omar A Gonsenheim
 ************************************************************************/

namespace Espo\Custom\Core\Utils\Database\Orm\Fields;

use Espo\Core\Utils\Util;

class IpersonName extends \Espo\Core\Utils\Database\Orm\Fields\PersonName
{
    private function getFields($format, $fieldName)
    {
        /*switch ($format) {
            case 'foreign':
                $subList = ['last' . ucfirst($fieldName), 'ipersonSep', 'initials' . ucfirst($fieldName), 'ipersonSep', 'first' . ucfirst($fieldName), 'ipersonSep', 'middle' . ucfirst($fieldName) ];
                break;
            case 'lastFirst':
                $subList = ['last' . ucfirst($fieldName), ' ', 'initials' . ucfirst($fieldName), ' ', 'first' . ucfirst($fieldName)];
                break;
            case 'lastFirstMiddle':
                $subList = [
                    'last' . ucfirst($fieldName), ' ', 'initials' . ucfirst($fieldName), ' ', 'first' . ucfirst($fieldName), ' ', 'middle' . ucfirst($fieldName)
                ];
                break;
            case 'firstMiddleLast':
                $subList = [
                    'initials' . ucfirst($fieldName), ' ', 'first' . ucfirst($fieldName), ' ', 'middle' . ucfirst($fieldName), ' ', 'last' . ucfirst($fieldName)
                ];
                break;
            default:
                $subList = ['initials' . ucfirst($fieldName), ' ', 'first' . ucfirst($fieldName), ' ', 'last' . ucfirst($fieldName)];
        }*/
        $subList = ['last' . ucfirst($fieldName), 'ipersonSep', 
                    'initials' . ucfirst($fieldName), 'ipersonSep', 
                    'first' . ucfirst($fieldName), 'ipersonSep', 
                    'middle' . ucfirst($fieldName) 
                   ];
                #$subList = [
                    #'initials' . ucfirst($fieldName), ' ', 'first' . ucfirst($fieldName), ' ', 'middle' . ucfirst($fieldName), ' ', 'last' . ucfirst($fieldName)
                #];
        
        return $subList;
    }
	
    protected function load($fieldName, $entityName)
    {
        $format = $this->config->get('personNameFormat');

	$subList = $this->getFields($format, $fieldName);

        $tableName = Util::toUnderScore($entityName);

        // Always order by lastname 
        $orderBy1Field = 'last' . ucfirst($fieldName);
        $orderBy2Field = 'initials' . ucfirst($fieldName);

        $uname = ucfirst($fieldName);

        $fullList = [];
        $fieldList = [];

        $parts = [];

        foreach ($subList as $subFieldName) {
            $fieldNameTrimmed = trim($subFieldName);
            if (!empty($fieldNameTrimmed)) {
                $columnName = $tableName . '.' . Util::toUnderScore($fieldNameTrimmed);

                $fullList[] = $fieldList[] = $columnName;
                $parts[] = $columnName." {operator} {value}";
            } else {
                $fullList[] = "'" . $subFieldName . "'";
            }
        }

        $firstColumn = $tableName . '.' . Util::toUnderScore('first' . $uname);
        $lastColumn = $tableName . '.' . Util::toUnderScore('last' . $uname);
        $middleColumn = $tableName . '.' . Util::toUnderScore('middle' . $uname);
	$initialsColumn = $tableName . '.'. Util::toUnderScore('initials' . $uname);

        $whereString = "".implode(" OR ", $parts);

        if ($format === 'firstMiddleLast') {
            $whereString .=
                " OR CONCAT({$firstColumn}, ' ', {$middleColumn}, ' ', {$lastColumn}) {operator} {value}" .
                " OR CONCAT({$firstColumn}, ' ', {$lastColumn}) {operator} {value}" .
                " OR CONCAT({$lastColumn}, ' ', {$firstColumn}) {operator} {value}";
        } else if ($format === 'lastFirstMiddle') {
            $whereString .=
                " OR CONCAT({$lastColumn}, ' ', {$firstColumn}, ' ', {$middleColumn}) {operator} {value}" .
                " OR CONCAT({$firstColumn}, ' ', {$lastColumn}) {operator} {value}" .
                " OR CONCAT({$lastColumn}, ' ', {$firstColumn}) {operator} {value}";
        } else {
            $whereString .= " OR CONCAT({$firstColumn}, ' ', {$lastColumn}) {operator} {value}";
            $whereString .= " OR CONCAT({$lastColumn}, ' ', {$firstColumn}) {operator} {value}";
        }

        $selectString = $this->getSelect($fullList);

        if ($format === 'firstMiddleLast' || $format === 'lastFirstMiddle') {
            $selectString = "REPLACE({$selectString}, '  ', ' ')";
        }

        $obj = [
            $entityName => [
                'fields' => [
                    $fieldName => [
                        'type' => 'varchar',
                        'select' => $selectString,
                        'where' => [
                            'LIKE' => str_replace('{operator}', 'LIKE', $whereString),
                            '=' => str_replace('{operator}', '=', $whereString),
                        ],
                        'orderBy' => "{$tableName}." . Util::toUnderScore($orderBy1Field) ." {direction}, {$tableName}." . Util::toUnderScore($orderBy2Field)
                    ]
                ]
            ]
        ];

        return $obj;
    }
    
    public function getForeignField(string $fieldName, string $entityType)
    {
        return $this->getFields('foreign', $fieldName);
    }

    protected function getSelect($fullList) 
    {
       return parent::getSelect($fullList);
    }
}
