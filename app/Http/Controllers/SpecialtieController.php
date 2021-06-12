<?php

namespace App\Http\Controllers;

use App\Repository\Repositories\SpecialtieRepository;
use App\Repository\RepositoryFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SpecialtieController extends Controller
{
    private $specialtieRepository;

    public function __construct()
    {
        $this->specialtieRepository = App::make(RepositoryFactory::class,['class' => SpecialtieRepository::class]);
    }

    public function store(Request $request)
    {

    }

    public function search(Request $request)
    {

    }

    public function update($id,Request $request)
    {

    }

    public function delete($id)
    {

    }
    
}
