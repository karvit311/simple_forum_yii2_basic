<?php

namespace app\modules\forum\controllers;

use Yii;
use app\modules\forum\models\Registrations;
use app\modules\forum\models\Questions;
use app\modules\forum\models\RegistrationsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use dektrium\user\filters\AccessRule;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * RegistretionController implements the CRUD actions for Registretion model.
 */
class RegistrationsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','create-topic','update','view'],
                        'roles' => ['@','?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Registretion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Registrations();
        $registrations = Registrations::find()->all();
        $searchModel = new RegistrationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=10;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'registrations' => $registrations,
        ]);
    }

    /**
     * Displays a single Registretion model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Registretion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Registrations();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreateTopic($name=null)
    {
        $names = Registrations::find()->all();
        $model = new Registrations();
        if($model->load(\Yii::$app->request->post())){
            $model->user_id = Yii::$app->user->id;
            $model->created_at = date('Y-m-d H:i:s');
            $model->save();
        }
        if ($model->load(\Yii::$app->request->post()) &&  $model->save()) {
            \Yii::$app->session->setFlash('success', Yii::t('message', 'New topic was successfully created!'));
            return $this->redirect('/user/admin/index');
        } else {
            return $this->render('create-topic', [
                'model' => $model,
                'names' => $names,
            ]);
        }
    }

    /**
     * Updates an existing Registretion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Registretion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Registretion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Registretion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Registrations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}


