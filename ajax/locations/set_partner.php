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

$locationId = $values['location_id'];
$partnerId = $values['partner_id'];
$actionType = $values['action_type'];

if (isset($locationId) && isset($partnerId)) {
    Loader::includeModule("iblock");
    global $USER;
    $arPartners = [];
    $arLocationPartners = [];
    $res = CIBlockElement::GetList(array(), ["IBLOCK_ID" => IB_LOCATIONS, 'ID' => $locationId], false, [], ["ID", "PROPERTY_PARTNERS"]);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arLocationPartners[$arFields['ID']][] = $arFields['PROPERTY_PARTNERS_VALUE'];
    }
    clearArray($arLocationPartners);
    switch ($actionType) {
        case 'set':
            $arPartners = array_unique(array_merge($arLocationPartners[$locationId], [$partnerId]));
            updateElements($locationId, $arPartners);
            break;
        case 'delete':
            $arPartners = array_unique(array_diff($arLocationPartners[$locationId], [$partnerId]));
            updateElements($locationId, $arPartners);
            break;
    }
    unset($res);

    if (empty($arPartners)) {
        $arPartners = array(
            0 => array(
                "VALUE" => "",
                "DESCRIPTION" => ""
            )
        );
    }

    $res = CIBlockElement::SetPropertyValuesEx($locationId, IB_LOCATIONS, array('PARTNERS' => $arPartners));
    if (!$res) {
        $now = new DateTime();
        $errorMsg = 'Ошибка добавления партнера к локации id=' . $locationId . ' time= ' . $now->format('d.m.Y H:i:s');
        \Bitrix\Main\Diag\Debug::dumpToFile($errorMsg, $varName = 'set_partner.php', $fileName = 'set_partner_error_log.txt');
    }
}

function updateElements($locationId, $arPartners)
{

    if (!$arPartners) {
        $arPartners = array(
            0 => array(
                "VALUE" => "",
                "DESCRIPTION" => ""
            )
        );
    }

    Loader::includeModule("iblock");
    global $USER;
    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_PARTNERS",
    );
    $arFilter = array("IBLOCK_ID" => IB_OBJECT, '=PROPERTY_LOCATION' => $locationId);
    $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
    $arLocationElements = [];
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        if (!empty($arFields)) {
            $arLocationElements[] = $arFields;
        }
    }
    unset($res);
    foreach ($arLocationElements as $element) {
        $res = CIBlockElement::SetPropertyValuesEx($element['ID'], IB_OBJECT, array('PARTNERS' => $arPartners));
        if (!$res) {
            $now = new DateTime();
            $errorMsg = 'Ошибка добавления партнера к объекту id=' . $element['ID'] . ' time= ' . $now->format('d.m.Y H:i:s');
            \Bitrix\Main\Diag\Debug::dumpToFile($errorMsg, $varName = 'set_partner.php', $fileName = 'set_partner_error_log.txt');
        }
    }

}

function clearArray(&$array)
{
    foreach ($array as $i => $element) {
        if (is_null($element)) {
            unset($array[$i]);
        }
    }
}
