<?php

namespace Crud\Forms\Element;

/**
 * Class Name
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class SelectStatus 
    extends \Zend_Form_Element_Select
{
   public $options = array(1=>'Disabled (1)', 0=>'Active (0)');
}