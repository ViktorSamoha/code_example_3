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

if ($values['arElementsId'] || $values['arSectionsId']) {
    $APPLICATION->IncludeComponent(
        "wa:map",
        ".default",
        [
            'FILTER_SECTIONS_ID' => $values['arSectionsId'],
            'FILTER_ELEMENTS_ID' => $values['arElementsId'],
            'RESTART_MAP' => 'Y',
        ],
        false
    );
}