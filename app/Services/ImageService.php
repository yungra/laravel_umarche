<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ImageService{
  public static function upload($imageFile, $folderName){
    // is_array→変数が配列かどうか検査
    if(is_array($imageFile)){
      $file = $imageFile['image'];
    } else{
      $file = $imageFile;
    }

    // uniqid()→一意なIDを取得する
    // uniqid(rand() . '_')→ランダム数値_に続いて、13文字の文字列を取得
    $fileName = uniqid(rand() . '_');
    // 拡張子を取得
    $extension = $file->extension();
    $fileNameToStore = $fileName . '.' . $extension;
    // サイズ変更
    $resizedImage = InterventionImage::make($file)->resize(1920, 1080)->encode();
    Storage::put(
      'public/' . $folderName . '/' . $fileNameToStore,
      $resizedImage
  );
    return $fileNameToStore;
  }
}
