<?php

namespace backend\controllers;

use Yii;
use backend\models\Products;
use backend\models\ProductsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\db\ActiveRecord;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
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
                    'amazon' => ['GET'],
                    'save-product-info' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex()
    {
        $identity = Yii::$app->user->identity;

        if (isset($identity) && !empty($identity)) {

            $searchModel = new ProductsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);
        }
        else
        {
            return $this->goHome();
        }
    }

    /**
     * Displays a single Products model.
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
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Products();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Products model.
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
     * Deletes an existing Products model.
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
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAmazon(){
        $identity = Yii::$app->user->identity;

        if (isset($identity) && !empty($identity)) {

            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $getData = YII::$app->request->get();

            $asin = $getData['asinProduct'];

            if (!empty($asin)){
                $info = Yii::$app->components->getItemInfo($asin, 'AKIAIN4I5FALO4URFNSA', 'rlysJT49Xo1QjTCWIjh2wvmUh5+1ECpA1/ovMtMZ');
            }
            else
            {
                $info['error'] = 'Please, enter ASIN!';
            }

            return $info;
        }
    }

    public function actionSaveProductInfo(){
        $identity = Yii::$app->user->identity;

        if (isset($identity) && !empty($identity)) {

            $postData = YII::$app->request->post();

            $model = new Products();

            $model->asin = $postData['asinProductInput'];
            $model->title = $postData['titleProductInput'];
            $model->price = $postData['priceProductInput'];
            $model->currency_code = $postData['currencyCodeProductInput'];
            $model->picture = $postData['pictureProductInput'];
            $model->EAN = $postData['eanProductInput'];
            $model->Brand = $postData['brandProductInput'];

            $model->save();

            $searchModel = new ProductsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]);

        }
    }
}


