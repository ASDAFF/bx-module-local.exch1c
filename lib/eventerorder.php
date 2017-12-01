<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Event;

class EventerOrder
{
    public function markOrderForExport(Event $event) {
        $order = $event->getParameter("ENTITY");

        $propertyCollection = $order->getPropertyCollection();

        $arModConf = include __DIR__ . '/../mod_conf.php';
        $allConsts = get_defined_constants();

        $orderStatusStart = '';
        $constName = str_replace('.', '_', $arModConf['name']).'_ORDER_STATUS_START';
        if(isset($allConsts[$constName])) {
            $orderStatusStart = $allConsts[$constName];
        }

        $orderStatusEdit = '';
        $constName = str_replace('.', '_', $arModConf['name']).'_ORDER_STATUS_EDIT';
        if(isset($allConsts[$constName])) {
            $orderStatusEdit = $allConsts[$constName];
        }


        /** @var \Bitrix\Sale\PropertyValue $obProp */
        foreach ($propertyCollection as $obProp) {
            $arProp = $obProp->getProperty();

            switch($arProp["CODE"]) {
                case 'EXPORT_DO':
                case 'EXPORT_DO_UR':
                    if($obProp->getValue() === 'NNN') {
                        $obProp->setValue('N');
                    } elseif($obProp->getValue() !== 'Y') {
                        $obProp->setValue('Y');
                    }
                break;

                case 'EXT_STATUS':
                case 'EXT_STATUS_UR':
                    if($obProp->getValue() === 'N') {
                        $obProp->setValue($orderStatusStart);
                    }
//                    else {
//                        $obProp->setValue($orderStatusEdit);
//                    }
                break;

                default:
                    continue;
            }
        }
    }
}