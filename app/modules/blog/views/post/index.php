<?php
use Micro\web\Language;
use Micro\wrappers\Html;

/** @var array $blogs */
/** @var integer $pages */
/** @var Language $lang */

$currPage = 0;
if (!empty($_GET['page'])) {
    $currPage = $_GET['page'];
}

$this->widget('App\modules\blog\widgets\TopblogsWidget');
echo Html::href('Создать', '/blog/post/create');
?>

<?php
    echo $this->widget('\Micro\widgets\ListViewWidget', [
        'data'=>$blogs,
        'page'=> !empty($_GET['page']) ? $_GET['page'] : 0,
        'pathView'=>__DIR__ . '/_view.php',
        'paginationConfig' => [
            'url'=>'/blog/post/index/'
        ]
    ]);
?>

<p><?= /** @noinspection PhpUndefinedFieldInspection */ $lang->hello; ?></p>