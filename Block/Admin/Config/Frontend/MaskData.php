<?php
declare(strict_types=1);
namespace MageStack\FirePush\Block\Admin\Config\Frontend;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class MaskData extends Field
{
    protected function _getElementHtml(AbstractElement $element)
    {
        $value = $element->getEscapedValue();

        // If there's a value, show masked text instead of the encrypted content
        if ($value) {
            $element->setValue('[******** Firebase Config File Uploaded ********]');
            // $element->setReadonly(true);
        }

        return parent::_getElementHtml($element);
    }
}