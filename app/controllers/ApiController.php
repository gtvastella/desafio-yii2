<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use app\models\Customer;
use app\models\CustomerSearch;
use app\models\BookSearch;
use app\models\Book;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\ImgUpload;
use app\components\TokenAuthenticator;
use yii\web\BadRequestHttpException;


class ApiController extends Controller
{
 
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['POST'],  
                    'create-customer' => ['POST'],
                    'list-customers' => ['GET'],
                    'create-book' => ['POST'],
                    'list-books' => ['GET'],
                ],
            ],
            'authenticator' => [
                'class' => TokenAuthenticator::class,
                'except' => ['login'],
            ],
        ];
    }

     /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }
   
    /**
     * Authenticates the user and returns a bearer token. If the login or password is incorrect, an exception is thrown.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionLogin()
    {
        $request = Yii::$app->request;
        
        $login = $request->post('login');
        $password = $request->post('password');
        if (!$login || !$password) {
            throw new BadRequestHttpException('Login e senha são obrigatórios');
        }

        $user = User::findOne(['login' => $login]);
        if (!$user || !Yii::$app->getSecurity()->validatePassword($password, $user->password)) {
            throw new BadRequestHttpException('Login ou senha inválidos');
        }

        $token = $user->createBearerToken();
        if (!$token) {
            throw new BadRequestHttpException('Erro ao criar token');
        }

        return [
            'success' => true,
            'token' => $token,
            'message' => "Login efetuado com sucesso",
        ];
    }

    /**
     * Creates a new customer, validating the required fields.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionCreateCustomer()
    {
        $request = Yii::$app->request;
        
        $name = $request->post('name');
        $cpf = $request->post('cpf');
        $address = $request->post('address');
        $sex = $request->post('sex');

        if (!$name || !$cpf || !$address || !$sex) {
            throw new BadRequestHttpException('Nome, CPF, endereço e sexo são obrigatórios');
        }

        if (!in_array($sex, ['M', 'F'])) {
            throw new BadRequestHttpException('Sexo inválido: deve ser M ou F');
        }

        $addressFields = [
            'cep', 'publicArea', 'city', 'state', 'complement'
        ];
        $optionalFields = ['number', 'complement'];

        foreach ($addressFields as $field) {
            if (!isset($address[$field]) && !in_array($field, $optionalFields)) {
                throw new BadRequestHttpException("O campo de endereço $field é obrigatório");
            }
        }

        $customer = new Customer();
        $customer->name = $name;
        $customer->cpf = $cpf;
        $customer->address = $address['publicArea'];
        $customer->cep = $address['cep'];
        $customer->number = $address['number'] ?? null;
        $customer->city = $address['city'];
        $customer->state = $address['state'];
        $customer->complement = $address['complement'] ?? null;
        $customer->sex = $sex == 'M' ? Customer::MALE_SEX : Customer::FEMALE_SEX;
        
        if (!$customer->save()) {
            throw new BadRequestHttpException('Erro ao cadastrar cliente: ' . implode(', ', $customer->getFirstErrors()));
        }

        return [
            'success' => true,
            'message' => 'Cliente cadastrado com sucesso',
            'customer' => $customer->id
        ];
        
    }
    
    /**
     * Lists customers according to the query parameters and sorting.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionListCustomers()
    {
        $request = Yii::$app->request;

        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $cpf = $request->get('cpf');
        $name = $request->get('name');
        $orderBy = $request->get('sort', 'name');
        $allowOrderBy = ['name', '-name', 'cpf', '-cpf', 'city', '-city'];
        if (!in_array($orderBy, $allowOrderBy)) {
            throw new BadRequestHttpException('Só é possível ordenar por ' . implode(', ', $allowOrderBy));
        }

        $search = new CustomerSearch();
        $params = ["CustomerSearch" => [
            'cpf' => $cpf,
            'name' => $name,
        ]];
        $dataProvider = $search->search($params, $limit, $offset, $orderBy);

        return [
            'success' => true,
            'message' => 'Foram encontrados ' . $dataProvider->getCount() . ' clientes',
            'data' => $dataProvider->getModels(),
        ];
    }

    /**
     * Creates a new book, validating the required fields. If the title or author is not informed, the ISBN is used to populate the fields.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionCreateBook()
    {
        $request = Yii::$app->request;
        
        $isbn = $request->post('isbn');
        $title = $request->post('title');
        $author = $request->post('author');
        $price = $request->post('price');
        $stock = $request->post('stock');

        if (!$isbn || !$stock || !$price) {
            throw new BadRequestHttpException('ISBN, estoque e preço são obrigatórios');
        }

        $book = new Book();
        $book->isbn = $isbn;
        $book->stock = $stock;
        $book->price = $price;

        if (!$title || !$author) {
            $fieldsToPopulate = [];
            if (!$title) {
                $fieldsToPopulate[] = 'title';
            }
            if (!$author) {
                $fieldsToPopulate[] = 'author';
            }

            if (!$book->populateDataByISBN($fieldsToPopulate)) {
                throw new BadRequestHttpException('Erro ao buscar dados do ISBN. Preencha todos os campos para cadastrar manualmente');
            }
        }
        
        $book->title = $book->title ?? $title;
        $book->author = $book->author ?? $author;
        if (!$book->save()) {
            throw new BadRequestHttpException('Erro ao cadastrar livro: ' . implode(', ', $book->getFirstErrors()));
        }

        return [
            'success' => true,
            'message' => 'Livro cadastrado com sucesso',
            'book' => $book->id
        ];
    }


    /**
     * Lists books according to the query parameters and sorting.
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionListBooks()
    {
        $request = Yii::$app->request;

        $limit = $request->get('limit', 100);
        $offset = $request->get('offset', 0);
        $title = $request->get('title');
        $author = $request->get('author');
        $isbn = $request->get('isbn');
        $orderBy = $request->get('sort', 'title');
        $allowOrderBy = ['title', '-title', 'price', '-price'];
        if (!in_array($orderBy, $allowOrderBy)) {
            throw new BadRequestHttpException('Só é possível ordenar por ' . implode(', ', $allowOrderBy));
        }

        $search = new BookSearch();
        $params = ["BookSearch" => [
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
        ]];
        $dataProvider = $search->search($params, $limit, $offset, $orderBy);

        return [
            'success' => true,
            'message' => 'Foram encontrados ' . $dataProvider->getCount() . ' livros',
            'data' => $dataProvider->getModels(),
        ];
    }


    /**
     * Attempts to upload an image for a model. The model ID must be provided, and the image must be uploaded. 
     * If the image is valid, it is saved to S3, and the model's image path is updated.
     * @throws BadRequestHttpException
     * @return array
     */
    private function uploadModelImg($model, $imgUpload)
    {
        $imgUpload = new ImgUpload();
        $imgUpload->image = UploadedFile::getInstanceByName('img');

        if (!$imgUpload->image) {
            throw new BadRequestHttpException('Imagem é obrigatória');
        }
        
        if (!$imgUpload->validate()) {
            throw new BadRequestHttpException('Imagem inválida: ' . implode(', ', $imgUpload->getFirstErrors()));
        }

        $path = $imgUpload->upload();
        if (!$path) {
            throw new BadRequestHttpException('Erro ao salvar imagem em S3');
        }

        $model->s3_image_path = $path;
        if (!$model->save()) {
            throw new BadRequestHttpException('Erro ao salvar imagem');
        }

        return $path;
    }
    /**
     * Attempts to upload an image for a book. The book ID must be provided, and the image must be uploaded. 
     * If the image is valid, it is saved to S3, and the book's image path is updated.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionUploadBookImg()
    {   
        $request = Yii::$app->request;
        $bookId = $request->post('bookId');

        if (!$bookId) {
            throw new BadRequestHttpException('ID do livro e imagem são obrigatórios');
        }

        $book = Book::findOne($bookId);
        if (!$book) {
            throw new BadRequestHttpException('Livro não encontrado');
        }

        $imgUpload = new ImgUpload();
        $imgUpload->image = UploadedFile::getInstanceByName('img');

        if (!$imgUpload->image) {
            throw new BadRequestHttpException('Imagem é obrigatória');
        }
        
        $this->uploadModelImg($book, $imgUpload);

        return [
            'success' => true,
            'message' => 'Imagem salva com sucesso',
            'path' => $book->s3_image_path,
        ];
    }

    /**
     * Attempts to upload an image for a customer. The customer ID must be provided, and the image must be uploaded. 
     * If the image is valid, it is saved to S3, and the book's image path is updated.
     * @throws BadRequestHttpException
     * @return array
     */
    public function actionUploadCustomerImg()
    {   
        $request = Yii::$app->request;
        $customerId = $request->post('customerId');

        if (!$customerId) {
            throw new BadRequestHttpException('ID do cliente e imagem são obrigatórios');
        }

        $customer = Customer::findOne($customerId);
        if (!$customer) {
            throw new BadRequestHttpException('Cliente não encontrado');
        }

        $imgUpload = new ImgUpload();
        $imgUpload->image = UploadedFile::getInstanceByName('img');

        if (!$imgUpload->image) {
            throw new BadRequestHttpException('Imagem é obrigatória');
        }
        
        $this->uploadModelImg($customer, $imgUpload);

        return [
            'success' => true,
            'message' => 'Imagem salva com sucesso',
            'path' => $customer->s3_image_path,
        ];
    }
}