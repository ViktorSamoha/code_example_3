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

if ($values['ELEMENT_ID']) {
    if (checkRoute($values['ELEMENT_ID'])) {
        $APPLICATION->IncludeComponent(
            "wa:booking",
            "route.booking.form",
            [
                'ELEMENT_ID' => $values['ELEMENT_ID'],
            ],
            false
        );
    } else {
        $APPLICATION->IncludeComponent(
            "wa:booking",
            "object.booking.form",
            [
                'ELEMENT_ID' => $values['ELEMENT_ID'],
            ],
            false
        );
    }
}

