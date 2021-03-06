<?php

namespace App\modules\blog\controllers;

use App\components\Controller;
use App\components\View;
use App\modules\blog\models\Blog;
use Micro\db\Query;

class PostController extends Controller
{
    public function filters()
    {
        return [
            [
                'class' => '\Micro\filters\AccessFilter',
                'actions' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => false,
                        'actions' => ['create', 'update', 'delete'],
                        'users' => ['?'],
                        'message' => 'Only for authorized!'
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'users' => ['*'],
                        'message' => 'View for all'
                    ]
                ]
            ],
            [
                'class' => '\Micro\filters\CsrfFilter',
                'actions' => ['login']
            ],
            [
                'class' => '\Micro\filters\XssFilter',
                'actions' => ['index', 'login', 'logout'],
                'clean' => '*'
            ]
        ];
    }

    public function actionIndex()
    {
        $crt = new Query;
        $crt->table = Blog::tableName();
        $crt->order = 'id DESC';
//        $crt->limit = 10;
//        $crt->ofset = (!empty($_GET['page']) ? $_GET['page'] : 0) * $crt->limit;

//        $crt2 = new Query;
//        $crt2->select = 'COUNT(id)';
//        $crt2->table = Blog::tableName();
//        $crt2->single = true;
//        $num = $crt2->run();

        $v = new View;
        $v->addParameter('blogs', $crt);
//        $v->addParameter('pages', ceil($num[0] / 10));
        return $v;
    }

    public function actionView()
    {
        $crt = new Query;
        $crt->addWhere('id = :id');
        $crt->params = [
            ':id' => $_GET['id']
        ];
        $blog = Blog::finder($crt, true);

        $v = new View;
        $v->addParameter('model', $blog);
        return $v;
    }

    public function actionCreate()
    {
        $blog = new Blog;

        if (!empty($_POST['Blog'])) {
            $blog->name = $_POST['Blog']['name'];
            $blog->content = $_POST['Blog']['content'];

            if ($blog->save()) {
                $this->redirect('/blog/post/' . $blog->id);
            }
        }

        $v = new View;
        $v->addParameter('model', $blog);
        return $v;
    }

    public function actionUpdate()
    {
        $crt = new Query;
        $crt->addWhere('id = :id');
        $crt->params = [':id' => $_GET['id']];
        $blog = Blog::finder($crt, true);

        $blog->name = 'setupher';
        return $blog->save();
    }

    public function actionDelete()
    {
        $crt = new Query;
        $crt->addWhere('id = :id');
        $crt->params = [':id' => $_GET['id']];
        $blog = Blog::finder($crt, true);
        return $blog->delete();
    }
}