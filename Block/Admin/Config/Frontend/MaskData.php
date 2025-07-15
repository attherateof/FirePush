<?php
declare(strict_types=1);
namespace MageStack\FirePush\Block\Admin\Config\Frontend;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

class MaskData extends Field
{
    public const MASKED_VALUE = '[Encrypted Firebase Configuration Active]';

    protected function _getElementHtml(AbstractElement $element)
    {
        $value = $element->getEscapedValue();
        if ($value) {
            $element->setValue(self::MASKED_VALUE);
        }

        return parent::_getElementHtml($element);
    }
}