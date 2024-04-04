<? require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

if (isset($values['OBJECT_ID'])
    && isset($values['ARRIVAL_DATE'])
    && isset($values['DEPARTURE_DATE'])
    && isset($values['PEOPLE_COUNT'])
    && isset($values['DAILY_TRAFFIC'])) {
    if (Loader::includeModule("highloadblock")) {
        $hlblock = HL\HighloadBlockTable::getById(HL_ROUTE_BOOKING_ID)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        $hlbElements = [];
        $arBookedDay = [];
        $return = true;
        $data = $entity_data_class::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "DESC"),
            "filter" => array("UF_OBJECT_ID" => $values['OBJECT_ID'])
        ));
        while ($arData = $data->Fetch()) {
            $hlbElements[] = [
                'ID' => $arData['ID'],
                'OBJECT_ID' => $arData['UF_OBJECT_ID'],
                'ARRIVAL_DATE' => $arData['UF_ARRIVAL_DATE']->format('d.m.Y'),
                'DEPARTURE_DATE' => $arData['UF_DEPARTURE_DATE']->format('d.m.Y'),
                'PEOPLE_COUNT' => $arData['UF_PEOPLE_COUNT'],
            ];
        }
        if (!empty($hlbElements)) {
            foreach ($hlbElements as $element) {
                $period = getDatePeriod($element['ARRIVAL_DATE'], $element['DEPARTURE_DATE']);
                if ($period) {
                    foreach ($period as $date) {
                        if (isset($arBookedDay[$date]) && !empty($arBookedDay[$date])) {
                            $arBookedDay[$date] += $element['PEOPLE_COUNT'];
                        } else {
                            $arBookedDay[$date] = $element['PEOPLE_COUNT'];
                        }
                    }
                }
            }
            if (!empty($arBookedDay)) {
                $bookingPeriod = getDatePeriod($values['ARRIVAL_DATE'], $values['DEPARTURE_DATE']);
                if ($bookingPeriod) {
                    foreach ($bookingPeriod as $day) {
                        if (isset($arDateLoad[$day])) {
                            if (($values['DAILY_TRAFFIC'] - $arDateLoad[$day]) < $values['PEOPLE_COUNT']) {
                                $return = false;
                            }
                        }
                    }
                    echo $return;
                } else {
                    echo false;
                }
            } else {
                echo false;
            }
        } else {
            echo false;
        }
    } else {
        echo false;
    }
} else {
    echo false;
}