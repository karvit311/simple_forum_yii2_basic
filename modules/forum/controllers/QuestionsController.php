<?php

namespace app\modules\forum\controllers;

use Yii;
use app\modules\forum\models\Questions;
use app\modules\forum\models\Registrations;
use dektrium\user\models\User;
use app\modules\forum\models\QuestionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

/**
 * QuestionsController implements the CRUD actions for Questions model.
 */
class QuestionsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
             'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index','create','view', 'delete','update'],
                        'roles' => ['@','?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Questions models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $id = $_GET['id'];
        If ((!isset($_SESSION['countid']))||($id != $_SESSION['countid']) )
        {
            $querycount = Questions::find()->where(['registration_id' => $id])->all();
            $querycount_count = Questions::find()->where(['registration_id' => $id])->count();
            if($querycount_count>0){ 
                foreach ($querycount as $key => $resultcount) {
                    $newcount = $resultcount->count + 1;
                }
                $update = Questions::find()->where(['registration_id' => $id])->one();
                $update->count = $newcount;
                $update->update();
            }

        }
        $searchModel = new QuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=10;
        $questions = Questions::find()->where(['registration_id' => $id])->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'questions' => $questions,
        ]);
    }

    /**
     * Displays a single Questions model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $idRegistretions = Registrations::find()->leftJoin('questions','questions.registration_id=registrations.id')->where([
            'questions.id' => $id])->all();
        foreach ($idRegistretions as $key => $idRegistretion) {}
        return $this->render('view', [
            'model' => $this->findModel($id),
            'idRegistretion' => $idRegistretion,
        ]);
    }

    /**
     * Creates a new Questions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id,$name = null)
    {
        $id = $_GET['id'];
        $idRegistretions = Registrations::find()->leftJoin('questions','questions.registration_id=registrations.id')->where([
            'questions.id' => $id])->all();
        foreach ($idRegistretions as $key => $idRegistretion) {}
            $model = new Questions();
            if ($model->load(Yii::$app->request->post())){
                $model->amount = '1';
                $model->user_id = Yii::$app->user->id;
                $model->registration_id = $id;
                $model->save();
            }
        if($model->load(Yii::$app->request->post()) && $model->save()) {
            (\Yii::$app->getSession()->setFlash('success', 'The question has been created!'));
            return $this->redirect(['index?id=' . $model->registration_id ]);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing Questions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionForbidden()
    {
        return $this->render('forbidden');
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $idRegistretions = Registrations::find()->leftJoin('questions','questions.registration_id=registrations.id')->where([
            'questions.id' => $id])->all();

        foreach ($idRegistretions as $key => $idRegistretion) {}

        if (!\Yii::$app->user->can('updateOwnNews', ['model' => $model])) 
        {
            return $this->render('forbidden');
        } 
        if ($model->load(Yii::$app->request->post()) && $model->save()) 
        {
            return $this->redirect(['index', 'id' => $idRegistretion['id']]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'idRegistretion' => $idRegistretion,
            ]);
        }
    }

    /**
     * Deletes an existing Questions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $idRegistretions = Questions::find()->leftJoin('registrations','registrations.id=questions.registration_id')->all();

        foreach ($idRegistretions as $key => $idRegistretion) {}

        return $this->redirect(['index?id='. $idRegistretion['registration_id']]);
    }

    /**
     * Finds the Questions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Questions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Questions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
