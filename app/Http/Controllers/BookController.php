<?php

namespace App\Http\Controllers;

use App\Funcs\CommonFuncs;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    private $db = [];
    public $commonFuncs;
    public function __construct()
    {
        $this->commonFuncs = new CommonFuncs();
        $this->db = getenv('DB_DATABASE');
    }

    // index, create, store, show, edit, update e destroy

    public function index(Request $request, Response $response)
    {
        $response = new response();
        $response->header('content-type', 'application/json');

        //conectando com pdo pela funcao que foi criada para facilitar
        $pdo = $this->commonFuncs->conn($this->db);

        return $response->setContent(json_encode([
            'pdo' => $pdo,
            'getenv' => [
                getenv('DB_HOST'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            ]
        ]))->setStatusCode(200);
    }
}
