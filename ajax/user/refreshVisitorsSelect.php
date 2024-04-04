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

if ($post['user_record_id']) {
    if (Loader::includeModule("iblock")) {
        $arUserGroup = [];
        $arVisitors = [];
        $arSelect = array("ID", "PROPERTY_USER_GROUP",);
        $arFilter = array("IBLOCK_ID" => IB_USERS, "ID" => $post['user_record_id']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arUserGroup[] = $arFields['PROPERTY_USER_GROUP_VALUE'];
        }
        if (!empty($arUserGroup)) {
            unset($arSelect, $arFilter, $res, $ob, $arFields);
            $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME");
            $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ID" => $arUserGroup);
            $res = CIBlockElement::GetList(array(), $arFilter, false, [], $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arVisitors[] = [
                    'ID' => $arFields['ID'],
                    'LAST_NAME' => $arFields['PROPERTY_U_LAST_NAME_VALUE'],
                    'NAME' => $arFields['PROPERTY_U_NAME_VALUE'],
                    'SECOND_NAME' => $arFields['PROPERTY_U_SECOND_NAME_VALUE'],
                ];
            }
            if (!empty($arVisitors)) {
                foreach ($arVisitors as $k => $arVisitor) {
                    ?>
                    <div class="custom-select_item">
                        <div class="checkbox checkbox-w-btn">
                            <input type="checkbox" id="checkbox_<?= $k ?>"
                                   value="<?= $arVisitor['ID'] ?>"
                                   name="VISITORS[]">
                            <label for="checkbox_<?= $k ?>">
                                <div class="checkbox_text"><?= $arVisitor['LAST_NAME'] . ' ' . $arVisitor['NAME'] . ' ' . $arVisitor['SECOND_NAME'] ?></div>
                            </label>
                        </div>
                    </div>
                    <?
                }
            }
        }

    }
}