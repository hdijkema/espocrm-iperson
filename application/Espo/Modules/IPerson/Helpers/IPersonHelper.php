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

namespace Espo\Modules\IPerson\Helpers;

use Espo\Core\Utils\Config;

use Espo\ORM\Entity;

class IPersonHelper extends \Espo\Core\ORM\Helper
{
    protected $metadata;

    public function __construct(Config $config, $metadata)
    {
    	parent::__construct($config, $metadata);
        $this->metadata = $metadata;
    }

    public function formatPersonName(Entity $entity, string $field)
    {

        $format = $this->config->get('personNameFormat');

		$metadata = $this->metadata;
		$entityType = $entity->getEntityType();
		$fields = $metadata->get('fields');
		$entityDefs = $metadata->get('entityDefs');
        $pattern = null;

        if (isset($entityDefs[$entityType])) {
           $entityDef = $entityDefs[$entityType];
           if (isset($entityDef['fields'])) {
              $fields = $entityDef['fields'];
              if (isset($fields[$field])) {
                 $foreignField = $fields[$field];
                 $type = $foreignField['type'];
                 if ($type) {
                    $fieldDefs = $metadata->get('fields');
                    if (isset($fieldDefs[$type])) {
                       $fieldDef = $fieldDefs[$type];
                       if (isset($fieldDef['patterns'])) {
                          $patterns = $fieldDef['patterns'];
                          if (isset($patterns[$format])) {
                             $pattern = $patterns[$format];
                          } else if (isset($patterns['default'])) {
                             $pattern = $patterns[$format];
                          }
                       } 
                    }
                 }
              }
           }
        }
        
        if ($pattern) {
           // a pattern can contain multiple patterns separated by a '|'. 
           // the pattern is preffered that has the least empty fills.
           $patts = explode('|', $pattern);

           $pattern_fields = null;
           preg_match_all('/[{][{]([^}]+)[}][}]/', $pattern, $pattern_fields);

           $patts_fields = array();
           foreach($pattern_fields[1] as $p_field) {
              if (!isset($patts_fields[$p_field])) {
                 $value = $entity->get($p_field . ucfirst($field));
                 $patts_fields[$p_field] = $value;
              }
           }

           $patts1 = array();
           foreach($patts as $patt) {
              foreach($patts_fields as $p_field => $val) {
                  $patt = str_replace("{{".$p_field."}}", "@#B@".$val."@E#@", $patt);
              }
              array_push($patts1, $patt);
           }

           $the_patt = $patts1[0];
           $m = -1;
           for($i = 0, $n = count($patts1); $i < $n; $i++) {
               $patt = $patts1[$i];
               $c = substr_count($patt, "@#B@@E#@");
               if ($m == -1 || $c < $m) { 
                   $the_patt = $patt;
                   $m = $c;
               }
           }

           $h = str_replace("@#B@", "", $the_patt);
           $h = str_replace("@E#@", "", $h);

           return $h;
        }
        
        // Nothing matched, so we call the base function
        return parent::formatPersonName($entity, $field);

    }
}
