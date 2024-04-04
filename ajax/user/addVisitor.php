<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Request,
    Bitrix\Main\Server,
    Bitrix\Main\Loader,
    Bitrix\Highloadblock as HL,
    Bitrix\Main\Entity;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$post = $request->getPostList();
if ($post['counter']) {
    $APPLICATION->IncludeComponent(
        "wa:user.visiting.permit",
        "add.visitor",
        [
            'VISITOR_COUNTER' => $post['counter']
        ],
        false
    );
}
