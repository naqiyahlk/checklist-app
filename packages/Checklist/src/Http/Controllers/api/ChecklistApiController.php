<?php

namespace Checklist\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Checklist\Repositories\ChecklistRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChecklistApiController extends Controller
{
    private $checklistRepository;

    public function __construct(ChecklistRepository $checklistRepository)
    {
//        $this->middleware('auth:api');
        $this->checklistRepository = $checklistRepository;
    }

    public function show(ChecklistRepository $id, Request $request)
    {

    }

    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required',
            'created_by' => 'required'
        ];

        $validationMessage = [
            'name.required' => getConfig('messages.checklist.name_required'),
            'created_by.required' => getConfig('messages.checklist.createdby_required'),
        ];

        $validator = Validator::make($request->all(), $validationRules, $validationMessage);

        if ($validator->fails()) {
            return getApiResponse(false, getConfig('messages.global.validate'), getConfig('messages.global.validate'), [], $validator->errors(),422);
        }


        $inputData = [
            'name' => $request->input('name'),
            'created_by' => $request->input('created_by')
        ];

        li($inputData);

        try {

            DB::beginTransaction();

            $checklist = $this->checklistRepository->createChecklist($inputData); // retruns checklist obj if success, else returns false

            DB::commit();

        } catch (Exception $exception) {

            DB::rollBack();
            \Log::info($exception->getMessage());
            return getApiResponse(false, getConfig('messages.checklist.fail'), getConfig('messages.checklist.fail'), [], $exception->getMessage(),422);

        }

        return getApiResponse(true, getConfig('messages.checklist.save'), getConfig('messages.checklist.save'), $checklist, [], 201);



    }

    public function update()
    {

    }

    public function destroy()
    {

    }


}