## udemy Laravel講座

##ダウンロード方法

git clone
git clone https://github.com/yungra/laravel_umarche.git

git clone ブランチを指定してダウンロードする場合
git clone -b ブランチ名 https://github.com/yungra/laravel_umarche.git

もしくはzipファイルでダウンロードしてください

## インストール方法

cd laravel_umarche
composer install
npm install
npm run dev

.env.example をコピーして .envファイルを作成

.envファイルの中の下記をご利用の環境に合わせて変更してください。

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_umarche
DB_USERNAME=umarche
DB_PASSWORD=password123

XAMPP/MAMPまたは他の開発環境でDBを起動した後に

php artisan migrate:fresh --seed

と実行してください。(データベーステーブルとダミーテーブルが追加されればOK)

最後に
php artisan key:generate
と入力してキーを生成後、

pho artisan serve
で簡易サーバーを立ち上げ、表示確認してください。


## インストール後の実施事項

画像のダミーデータは
public/imagesフォルダ内に
sample1.jpg〜sample6.jpgとして
保存しています。

php artisan storage:linkで
storageフォルダにリンク後、

storage/app/public/productsフォルダ内に
保存すると表示されます。
(productsフォルダがない場合は作成してください。)

ショップの画像も表示する場合は、
storage/app/public/shopsフォルダを作成し
画像を保存してください。

## section07の補足

決済のテストとしてstripeを利用しています。
必要な場合は .envにstripeの情報を追記してください。

## section08の補足

メールのテストとしてmailtrapを利用しています。
必要な場合は .envにmailtrapの情報を追記してください。

メール処理には時間がかかるので、
キューを利用しています。

必要な場合は php artisan queue:workで
ワーカーを立ち上げて動作確認するようにしてください。