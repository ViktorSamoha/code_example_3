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

if ($post['id']) {
    if (Loader::includeModule("iblock")) {
        $arVisitors = [];
        $arSelect = array("ID", "PROPERTY_U_LAST_NAME", "PROPERTY_U_NAME", "PROPERTY_U_SECOND_NAME");
        $arFilter = array("IBLOCK_ID" => IB_VISITORS, "ID" => $post['id']);
        $res = CIBlockElement::GetList(array(), $arFilter, false, array(), $arSelect);
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
            ?>
            <br>
            <?
            foreach ($arVisitors as $arVisitor) {
                ?>
                <div class="form-visitor js-parent-hidden-block" id="visitor-block-<?= $arVisitor['ID'] ?>">
                    <div class="input-group">
                        <div class="input input--sm">
                            <span><?= $arVisitor['LAST_NAME'] . ' ' . $arVisitor['NAME'] . ' ' . $arVisitor['SECOND_NAME'] ?></span>
                        </div>
                        <button class="btn-edit" type="button" onclick="unsetVisitor(<?= $arVisitor['ID'] ?>);">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                        d="M5.79167 17.0833C5.47222 17.0833 5.19444 16.9652 4.95833 16.7291C4.72222 16.493 4.60417 16.2083 4.60417 15.8749V4.54159H3.75V3.60409H7.3125V3.02075H12.6875V3.60409H16.25V4.54159H15.3958V15.8749C15.3958 16.2083 15.2778 16.493 15.0417 16.7291C14.8056 16.9652 14.5278 17.0833 14.2083 17.0833H5.79167ZM7.9375 14.3749H8.89583V6.29159H7.9375V14.3749ZM11.1042 14.3749H12.0625V6.29159H11.1042V14.3749Z"
                                        fill="#EA9A00"/>
                            </svg>
                            <span>Удалить</span>
                        </button>
                    </div>
                </div>
                <?
            }
        }
    }
}

