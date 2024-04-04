<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$values = $request->getPostList();

Loader::includeModule("iblock");
if (isset($values['location_id'])) {

    $ar_sections = [];

    $arSelect = ["IBLOCK_SECTION_ID"];
    $arFilter = ["IBLOCK_ID" => IB_OBJECT, "PROPERTY_LOCATION" => $values['location_id']];
    $res = CIBlockElement::GetList([], $arFilter, false, [], $arSelect);
    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $ar_sections[] = $arFields['IBLOCK_SECTION_ID'];
    }
    $ar_sections = array_unique($ar_sections);

    unset($arFilter);

    $arFilter = array('IBLOCK_ID' => IB_OBJECT, "ID" => $ar_sections);
    $db_list = CIBlockSection::GetList([], $arFilter, true, ["ID", "NAME"]);
    while ($ar_result = $db_list->GetNext()) {
        $arResult['SECTIONS'][] = $ar_result;
    }
}
?>
<? foreach ($arResult['SECTIONS'] as $section): ?>
    <div class="custom-select_item"
         data-id="<?= $section['ID'] ?>"><?= $section['NAME'] ?></div>
<? endforeach; ?>