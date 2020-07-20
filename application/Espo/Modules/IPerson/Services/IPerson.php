<?php
# vim: ts=4 sw=4 et:

namespace Espo\Modules\IPerson\Services;

class IPerson extends \Espo\Core\Templates\Services\Base
{
   public function getAttributeContactNameFromEntityForExport($entity)
   {
       return $this->getEntityManager()->getHelper()->formatForeignPersonName($entity, 'contactName');
   }
}

