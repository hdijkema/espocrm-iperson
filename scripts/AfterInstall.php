<?php

use \Espo\Core\Utils\System;

class AfterInstall
{
  private function info($msg)
  {
	  $GLOBALS['log']->info('IPerson Module: ' . $msg);
  }
  
  private function error($msg)
  {
  	$GLOBALS['log']->error('IPerson Module: ' . $msg);
  }
  
  public function run($container) 
  {
      $sys = new Espo\Core\Utils\System();
      
      $root_dir = $sys->getRootDir();
      
      $orm_base = $root_dir . '/application/Espo/Core/Utils/Database/Orm/Base.php';
      
      $base = file_get_contents($orm_base);
      
      if (strpos($base, 'iPersonPatchGetForeignField') === false) {
      	$func = 'protected function getForeignField';
      	$i = strpos($base, $func);
      	if ($i !== false) {
      		$pre = substr($base, 0, $i + strlen($func));
      		$post = substr($base, $i + strlen($func));

			$code = <<<'EOF'
(string $name, string $entityType)
    {
      $foreignField = $this->getMetadata()->get(['entityDefs', $entityType, 'fields', $name]);
      if (isset($foreignField['type'])) {
         if ($foreignField['type'] == 'personName') {
             return $this->iPersonPatchGetForeignField($name, $entityType);
         } else {
             $fieldType = ucfirst($foreignField['type']);
             $className = '\Espo\Custom\Core\Utils\Database\Orm\Fields\\' . $fieldType;
             if (!class_exists($className)) {
                $className = '\Espo\Core\Utils\Database\Orm\Fields\\' . $fieldType;
             }
             if (class_exists($className) && method_exists($className, 'getForeignField')) {
                $helperClass = new $className($this->getMetaData(), $this->getOrmEntityDefs(), $this->getEntityDefs(), $this->config);
                return $helperClass->getForeignField($name, $entityType);
             }
         }
      }
      return $this->iPersonPatchGetForeignField($name, $entityType);
    }
EOF;

            $code .= "\n\n    private function iPersonPatchGetForeignField";
      		$base = $pre . $code . $post;
      		
      		file_put_contents($orm_base, $base);
      		
      		$this->info("'$orm_base' succesfully patched");
      	} else {
      		$this->error("Cannot patch '$orm_base'!");
      		throw Error();
      	}
      }

      $this->info('done.');
  }
}
?>
