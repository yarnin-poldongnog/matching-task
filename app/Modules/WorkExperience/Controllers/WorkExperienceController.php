<?php

namespace App\Modules\WorkExperience\Controllers;

use App\Modules\Project\Repositories\ProjectRepository;
use App\Modules\Skill\Repositories\SkillRepository;
use App\Modules\Task\Repositories\TaskRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class WorkExperienceController extends Controller
{
    /**
     * @var SkillRepository
     */
    private $skillRepo;

    /**
     * SkillController constructor.
     * @param SkillRepository $skillRepository
     */
    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepo = $skillRepository;
    }

    public function index(Request $request)
    {
        return view('work-experience.index');
    }

    public function createView(Request $request)
    {
        return view('work-experience.create');
    }

    public function editView(Request $request, $id)
    {
        return view('work-experience.edit');
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

            $isSkillCreated = $this->skillRepo->createSkill($request);
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

            $isSkillUpdated = $this->skillRepo->updateSkill($id, $request);
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

            $isSkillDeleted = $this->skillRepo->deleteSkill($id);
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
