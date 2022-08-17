<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Type\DateTime;

class SimpleNews extends \CBitrixComponent
{
    const NEWSIBLOCKID = 1;

    public function executeComponent()
    {
        $this->arResult['NEWS'] = $this->getNewsList();
        
        if($this->startResultCache()) {
            $this->includeComponentTemplate();
        }
    }

    private function getNewsList() : array
    {
        $arNews = [];

        $nav = new \Bitrix\Main\UI\PageNavigation("nav-more-news");
        $nav->allowAllRecords(true)
            ->setPageSize(5)
            ->initFromUri();

        $rsNews = \Bitrix\Iblock\ElementTable::query()
            ->setSelect(['ID', 'NAME', 'ACTIVE_FROM', 'PREVIEW_TEXT', 'PREVIEW_PICTURE']) // не понял что за дата, наверное дата активности
            ->where('IBLOCK_ID', self::NEWSIBLOCKID)
            ->where('ACTIVE', 'Y')
            ->setOffset($nav->getOffset())
            ->setLimit($nav->getLimit())
            ->exec();

        if (!$rsNews)
            throw new Exception(GetMessage('NO_NEWS_FOUND'));
        
        while($news = $rsNews->fetch()) {
            $arNews[$news['ID']] = [
                'NAME' => $news['NAME'],
                'DATE_CREATE' => $news['DATE_CREATE'],
                'PREVIEW_TEXT' => $news['PREVIEW_TEXT'],
                'PREVIEW_PICTURE' => $news['PREVIEW_PICTURE'],
            ];
        }

        if (!empty($arNews)) {
            return $arNews;
        } else {
            throw new Exception(GetMessage('NO_NEWS_FOUND'));
        }
    }
}