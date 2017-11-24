<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Event;

class EventerOrder
{
    public function markOrderForExport(Event $event) {
        $order = $event->getParameter("ENTITY");
        $oldValues = $event->getParameter("VALUES");
        $isNew = $event->getParameter("IS_NEW");

        $propertyCollection = $order->getPropertyCollection();
        $doSave = false;

        /** @var \Bitrix\Sale\PropertyValue $obProp */
        foreach ($propertyCollection as $obProp) {
            $arProp = $obProp->getProperty();

            if(!in_array($arProp["CODE"], ["EXPORT_DO", "EXPORT_DO_UR"])) {
                continue;
            }

            if($arProp["CODE"] === "EXPORT_DO_UR" && $obProp->getValue() !== 'Y') {
                $doSave = true;
                $obProp->setValue('Y');
            }
        }

        if($doSave) {
            //$order->save();
        } else {
            //$GLOBALS['APPLICATION']->throwException('Что-то пошло не так');
        }
    }
}