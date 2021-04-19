<?php

namespace App\Modules\Menu\Controllers;

use App\Modules\Menu\Repositories\MenuRepository;
use App\Modules\Skill\Repositories\SkillRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class MenuController extends Controller
{
    /**
     * @var MenuRepository
     */
    private $menuRepo;

    /**
     * MenuController constructor.
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepo = $menuRepository;
    }

    public function index(Request $request)
    {
        $results = $this->menuRepo->getAllMenus($request);
        return view('menu.index', compact('results'));
    }

    public function createView(Request $request)
    {
        return view('menu.create');
    }

    public function editView(Request $request, $id)
    {
        if (!isset($id) && !$id) {
            redirect('menus');
        }

        $result = $this->menuRepo->getMenu($id);
        if ($result) {
            return view('menu.edit', compact('result'));
        } else {
            redirect('menus');
        }
    }


    public function create(Request $request)
    {
        try {
            $validatorSkillData = Validator::make($request->all(), [
                'skill_name' => 'required',
            ]);

            if ($validatorSkillData->fails()) {
                return responseError(422, 422, $validatorSkillData->errors(), []);
            }

            $isSkillCreated = $this->menuRepo->createMenu($request);
            if ($isSkillCreated) {
                return responseSuccess(201, 201, 'Created', []);
            }
            return responseError(500, 500, 'Something went wrong!', []);
        } catch (\Exception $e) {
            Log::error('SkillController@create: [' . $e->getCode() . '] ' . $e->getMessage());
            return FALSE;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatorSkillData = Validator::make($request->all(), [
                'skill_name' => 'required'
            ]);

            if ($validatorSkillData->fails() || !$id) {
                return responseError(422, 422, $validatorSkillData->errors(), []);
            }

            $isSkillUpdated = $this->menuRepo->updateMenu($id, $request);
            if ($isSkillUpdated) {
                return responseSuccess(201, 201, 'Updated', []);
            }
            return responseError(500, 500, 'Something went wrong!', []);
        } catch (\Exception $e) {
            Log::error('SkillController@update: [' . $e->getCode() . '] ' . $e->getMessage());
            return FALSE;
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            if (!$id) {
                return responseError(422, 422, 'id not found!', []);
            }

            $isSkillDeleted = $this->menuRepo->deleteMenu($id);
            if ($isSkillDeleted) {
                return responseSuccess(200, 200, 'Deleted', []);
            }
            return responseError(500, 500, 'Something went wrong!', []);
        } catch (\Exception $e) {
            Log::error('SkillController@delete: [' . $e->getCode() . '] ' . $e->getMessage());
            return FALSE;
        }
    }
}
