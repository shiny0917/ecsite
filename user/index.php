<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $loginStatus = true;
} else {
    $loginStatus = false;
}
require_once('./PDO_localhost.php')
?>
<!DOCTYPE html>
<html lang ="ja">
    <head>
        <meta charset ="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/css/swiper.min.css">
        <title>SHOP - INDEX</title>
        <style>
            body {width:960px;margin:0 auto;}
            header {background:pink;}
            a {text-decoration:none;}
            ul {display:flex;margin:0;padding:0;}
            .header_left {justify-content:flex-start;}
            .header_right {justify-content:flex-end;}
            li {list-style-type:none;font-size:0.8em;padding:0 5px;}
            .swiper-container {height:376px;}
            .item {flex-direction:column;}
            .header_navi {display:flex;width:960px;padding:0;background:lightgrey;}
            .header_navi li {display:block;width:240px;height:40px;text-align:center;margin-top:20px;}
            table {width:640px;margin:20px auto;}
            th {padding-left:50px;min-width:150px;}
            td {padding-left:20px;min-width:200px;}
            #pagenation {text-align:center;margin-bottom:30px;}
            footer {width:100%;background:pink;text-align:center;};
            small {height:50px;}
        </style>
    </head>
    <body>
        <header>
            <ul class ="header_left">
                <li>ご利用ガイド</li>
                <li>支払い方法</li>
                <li>送料・配送</li>
                <li>FAQ</li>
                <li>お問い合わせ</li>
            </ul>
            <ul class ="header_right">
                <?php if ($loginStatus) : ?>
                    <li><a href ="./user_logout.php">ログアウト</a></li>
                    <li><a href ="./user_change.php">登録情報変更</a></li>
                    <li><a href ="./user_pass_change.php">パスワード変更</a></li>
                    <li><a href ="./user_order_history.php">注文履歴</a></li>
                <?php else : ?>
                    <li><a href ="./user_regist.php">新規登録</a></li>
                    <li><a href ="./user_login.php">ログイン</a></li>
                <?php endif; ?>
                <li><a href ="./user_cart_look.php">カートを見る</a></li>
            </ul>
        </header>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><a href ="http://localhost/ec/user/index.php"><img src="../site_image/slide_logo_pink.png" alt="Swiper01"></a></div>
                <div class="swiper-slide"><a href ="./index.php"><img src="../site_image/slide_logo_blue.png" alt="Swiper02"></a></div>
                <div class="swiper-slide"><a href ="./index.php"><img src="../site_image/slide_logo_green.png" alt="Swiper03"></a></div>
                <div class="swiper-slide"><a href ="./index.php"><img src="../site_image/slide_logo_yellow.png" alt="Swiper04"></a></div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev swiper-button-white"></div>
            <div class="swiper-button-next swiper-button-white"></div>
        </div>
        <nav>
            <ul class ="header_navi">
                <li><a href="./index.php?item_category=1" alt="カテゴリー１">食品</a></li>
                <li><a href="./index.php?item_category=2" alt="カテゴリー２">雑貨</a></li>
                <li><a href="./index.php?item_category=3" alt="カテゴリー３">車</a></li>
                <li><a href="./index.php?item_category=4" alt="カテゴリー４">星</a></li>
            </ul>
        </nav>
        <main>
        <?php
                // レコード数を取得して最大ページ数を求める
                const RECORDS_PER_PAGE = 3;
                $sql = "SELECT COUNT(*) AS rows FROM item_list";
                if (
                    isset($_GET['item_category']) &&
                    $_GET['item_category'] > 0 &&
                    $_GET['item_category'] < 5
                ) {
                    $sql .= " WHERE item_category = :item_category";
                    $item_category = $_GET['item_category'];
                }
                $stmt = $pdo -> prepare($sql);
                if (isset($item_category)) {
                    $stmt -> bindParam(":item_category", $item_category);
                }
                $stmt -> execute();
                $result = $stmt -> fetch(PDO::FETCH_ASSOC);
                $count_record = $result['rows'];
                $page_count = $count_record/RECORDS_PER_PAGE;
                $page_max = ceil($page_count);

                // GETの値でpage指定
                if (
                    isset($_GET["page"]) &&
                    $_GET["page"] > 0 &&
                    $_GET["page"] <= $page_max
                  ) {
                    $current_page = (int)$_GET["page"];
                  } else {
                    $current_page = 1;
                  }
                $limit = RECORDS_PER_PAGE;
                $offset = ($current_page-1) * RECORDS_PER_PAGE;
                if (isset($item_category)) {
                    $sql = "SELECT * FROM item_list WHERE item_category = :item_category LIMIT :limit OFFSET :offset";
                } else {
                    $sql = "SELECT * FROM item_list LIMIT :limit OFFSET :offset";
                }

                $stmt = $pdo -> prepare($sql);
                if (isset($item_category)) {
                    $stmt -> bindParam(':item_category', $item_category);
                }
                $stmt -> bindParam(':limit', $limit);
                $stmt -> bindParam(':offset', $offset);
                $result = $stmt -> execute();
            ?>
            <?php if ($result !== false): ?>
                <table>
                    <?php while ($disp = $stmt -> fetch(PDO::FETCH_ASSOC)): ?>
                        <?php // if ($disp['item_status'] === 1): ?>
                            <div class ="item">
                                <tr>
                                    <th><?php if ($disp['item_image'] !== null) {
                                            echo "<a href='item.php?item_id=".$disp['item_id']."'><img src='".$disp['item_image']."' alt='商品画像' width='100'></a>";
                                        } else {
                                            echo "<img src='../img/noimage.png' alt='noimage' width='100'>";
                                        }
                                    ?></th>
                                    <td><?php echo "<a href='item.php?item_id=".$disp['item_id']."'>" .htmlspecialchars($disp['item_name'],ENT_QUOTES,'UTF-8')."</a>"; ?></td>
                                    <td><?php echo htmlspecialchars($disp['item_info'],ENT_QUOTES,'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($disp['item_price'],ENT_QUOTES,'UTF-8') ?>円</td>
                                </tr>
                            </div>
                        <?php // endif; ?>
                    <?php endwhile; ?>
                </table>
            <div id ="pagenation">
                <?php echo "登録件数" . $count_record . "件　"; ?>
                <?php
                    if (isset($item_category)) {
                        if($current_page > 1){ // リンクの判定
                            echo "<a href='?item_category=".$item_category."&page=" .($current_page - 1). "'>前へ</a>". '　';
                        } else {
                            echo '前へ'. '　';
                        }
                        for($i = 1; $i <= $page_max && $i <= 5; $i++){
                            if ($i == $current_page) {
                                echo $current_page . '　';
                            } else {
                                echo "<a href='?item_category=".$item_category."&page=" .$i. "'>".$i. "</a>". '　';
                            }
                        }
                        if($current_page < $page_max){ // リンクの判定
                            echo "<a href='?item_category=".$item_category."&page=" .($current_page + 1). "'>次へ</a>". '　';
                        } else {
                            echo '次へ';
                        }
                    } else {
                        if ($current_page > 1) { // リンクの判定
                            echo "<a href='?page=" .($current_page - 1). "'>前へ</a>". '　';
                        } else {
                            echo '前へ'. '　';
                        }
                        for ($i = 1; $i <= $page_max && $i <= 5; $i++) {
                            if ($i == $current_page) {
                                echo $current_page . '　';
                            } else {
                                echo "<a href='?page=" .$i. "'>".$i. "</a>". '　';
                            }
                        }
                        if ($current_page < $page_max) { // リンクの判定
                            echo "<a href='?page=" .($current_page + 1). "'>次へ</a>". '　';
                        } else {
                            echo '次へ';
                        }
                    }
                ?>
                <?php else: exit('データベース読み込みエラー'); ?>
                <?php endif; ?>
            </div>
            <?php
                $stmt = null;
                $pdo = null;
            ?>
        </main>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>
        <script>
            var mySwiper = new Swiper ('.swiper-container', {
            effect: "fade",
            loop: true,
            autoplay: 3000,
            speed: 2000,
            nextButton: '.swiper-button-next',
            prevButton: '.swiper-button-prev',
            pagination: '.swiper-pagination',
            })
        </script>
        <footer>
            <p><a href ="../manage/shop_login.html">管理ログイン</a></p>
            <small><a href ="./index.php">&copy 2019</a></small>
        </footer>
    </body>
</html>