<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2021 Yurii Kuznietsov, Taras Machyshyn, Oleksii Avramenko
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

namespace Espo\Custom\Core\Loaders;

use \Espo\Core\{
    ORM\EntityManagerFactory,
    ORM\EntityManager as EntityManagerService,
};


use \Espo\Modules\IPerson\Helpers\IPersonHelper;

class EntityManager extends \Espo\Core\Loaders\EntityManager
{
    public function load() : EntityManagerService
    {
        $obj = parent::load();


        if (isset($this->config)) {
           $config = $this->config;
        } else {
           if (isset($this->entityManagerFactory)) {
              # Post 6.0 EspoCRM
              $cfgprop = new \ReflectionProperty('\\Espo\\Core\\ORM\\EntityManagerFactory', 'config');
              $cfgprop->setAccessible(true);
              $config = $cfgprop->getValue($this->entityManagerFactory);
           } else {
              # Assume pré 6.0 EspoCRM
              $config = $this->getContainer()->get('config');
           }
        }

        $version = $config->get('version');
        $parts = preg_split('/[.]/', $version, 3);
        $v_maj = $parts[0] + 0;
        $v_min = $parts[1] + 0;
        $v_micro = $parts[2] + 0;

        if ($v_maj >= 6 && $v_min >= 1) {
           $property = new \ReflectionProperty('\\Espo\\Core\\ORM\\EntityManager', 'helper');
           $property->setAccessible(true);

           $helper = new IPersonHelper($config);
           $property->setValue($obj, $helper);
        }

        return $obj;
    }
}
