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

if ($values['parentSectionId'] && ($values['arElementsId'] || $values['arSectionsId'])) {
    $APPLICATION->IncludeComponent(
        "wa:bron",
        "elements_list",
        [
            "IBLOCK_TYPE" => "facility",
            "IBLOCK_ID" => IB_OBJECT,
            "FIELD_CODE" => [
                0 => "NAME",
                1 => "PREVIEW_PICTURE",
                2 => "DETAIL_PAGE_URL",
            ],
            "PROPERTY_CODE" => [
                0 => "PROPERTY_LOCATION",
                1 => "PROPERTY_CAPACITY_MAXIMUM",
            ],
            "SECTION_PROPERTY_CODE" => [
                0 => "UF_CB_SVG_ICON",
                1 => "UF_CB_ICON_CLASS_COLOR",
            ],
            "LOCATIONS_IBLOCK_TYPE" => "location",
            "LOCATIONS_IBLOCK_ID" => IB_LOCATIONS,
            'PARENT_SECTION_ID' => $values['parentSectionId'],
            'GET_LOCATION_LIST' => 'Y',
            /*'FILTER_ELEMENTS_ID' => explode(',', $values['arElementsId']),
            'FILTER_SECTIONS_ID' => explode(',', $values['arSectionsId']),*/
            'FILTER_ELEMENTS_ID' => $values['arElementsId'],
            'FILTER_SECTIONS_ID' => $values['arSectionsId'],
        ],
        false
    );
}