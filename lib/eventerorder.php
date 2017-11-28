<?php

namespace Local\Exch1c;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Event;

class EventerOrder
{
    public function markOrderForExport(Event $event) {
        $order = $event->getParameter("ENTITY");

        $propertyCollection = $order->getPropertyCollection();

        /** @var \Bitrix\Sale\PropertyValue $obProp */
        foreach ($propertyCollection as $obProp) {
            $arProp = $obProp->getProperty();

            if(!in_array($arProp["CODE"], ["EXPORT_DO", "EXPORT_DO_UR"])) {
                continue;
            }

            if($arProp["CODE"] === "EXPORT_DO_UR" && $obProp->getValue() === 'NNN') {
                $obProp->setValue('N');
            } elseif($arProp["CODE"] === "EXPORT_DO_UR" && $obProp->getValue() !== 'Y') {
                $obProp->setValue('Y');
            }
        }

    }
}