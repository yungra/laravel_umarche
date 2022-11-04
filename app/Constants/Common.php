<?php

namespace App\Constants;

// 定数クラス

class Common
{
  // 商品追加、削除用
  const PRODUCT_ADD = '1';
  const PRODUCT_REDUCE = '2';

  // 'add', 'reduce'で商品追加、削除が出来るよう定数を用意
  const PRODUCT_LIST = [
    // $this→自身のオブジェクト
    // self::→自クラス
    'add' => self::PRODUCT_ADD,
    'reduce' => self::PRODUCT_REDUCE,
  ];

  // 商品の並び順変更用の定数
  const ORDER_RECOMMEND = '0';
  const ORDER_HIGHER = '1';
  const ORDER_LOWER = '2';
  const ORDER_LATER = '3';
  const ORDER_OLDER = '4';

  // 配列に入れてしまう
  const SORT_ORDER = [
    'recommend' => self::ORDER_RECOMMEND,
    'higherPrice' => self::ORDER_HIGHER,
    'lowerPrice' => self::ORDER_LOWER,
    'later' => self::ORDER_LATER,
    'older' => self::ORDER_OLDER,
  ];
}
