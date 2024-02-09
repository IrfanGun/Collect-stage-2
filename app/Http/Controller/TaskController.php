<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
// use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller {
	private TaskService $taskService;
	public function __construct() {
		$this->taskService = new TaskService();
	}

	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	public function createTask(Request $request)
	{
		$request->validate([
			'title'=>'required|string|min:3',
			'description'=>'required|string'
		]);

		$data = [
			'title'=>$request->post('title'),
			'description'=>$request->post('description')
		];

		$dataSaved = [
			'title'=>$data['title'],
			'description'=>$data['description'],
			'assigned'=>null,
			'subtasks'=> [],
			'created_at'=>time()
		];

		$id = $this->taskService->addTask($dataSaved);
		$task = $this->taskService->getById($id);

		return response()->json($task);
	}


	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required|string',
			'title'=>'string',
			'description'=>'string',
			'assigned'=>'string',
			'subtasks'=>'array',
		]);

		$taskId = $request->post('task_id');
		$formData = $request->only('title', 'description', 'assigned', 'subtasks');
		$task = $this->taskService->getById($taskId);

		$this->taskService->updateTask($task, $formData);

		$task = $this->taskService->getById($taskId);

		return response()->json([
			"message" => "Task dengan id $taskId berhasil diupdate"
		]);
	}


	// TODO: deleteTask() - complete
	public function deleteTask(Request $request)
	{
		
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->task_id;
		
		$existTask = $this->taskService->getById($taskId);
		if(!$existTask)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}
		$mongoTasks = $this->taskService->deleteTask($taskId);

		return response()->json([
			"message" => "Task berhasil dihapus"
		]);
	}

	// TODO: assignTask() - completed
	public function assignTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required',
			'assigned'=>'required'
		]);

		$taskId = $request->get('task_id');
		$assigned = $request->post('assigned');
		$mongoTasks = $this->taskService->getById($taskId);
	
		if(!$mongoTasks)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$geTask = $this->taskService->updateAssigned($mongoTasks,$assigned);
		return response()->json([
			"message" => "berhasil membuat penugasan baru"
		]);
	}

	// TODO: unassignTask() - completed
	public function unassignTask(Request $request)
	{
		$request->validate([
			'task_id'=>'required'
		]);

		$taskId = $request->post('task_id');
		$mongoTasks = $this->taskService->getById($taskId);

		if(!$mongoTasks)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		// $mongoTasks['assigned'] = null;
		$updateAssigned = $this->taskService->unassignedTask($mongoTasks);

		return response()->json([
			"message" => "berhasil menghapus penugasan $taskId"
		]);
	}

	// TODO: createSubtask() - completed
	public function createSubtask(Request $request)
	{
		$mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required',
			'title'=>'required|string',
			'description'=>'required|string'
		]);

		$taskId = $request->post('task_id');
		$title = $request->post('title');
		$description = $request->post('description');
		$getSubId = $this->taskService->getIdSubtask();
		$mongoTasks = $this->taskService->getById($taskId);
		

		if(!$mongoTasks)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$subtasks = isset($mongoTasks['subtasks']) ? $mongoTasks['subtasks'] : [];

		$subtasks[] = [
			'_id'=> $getSubId,
			'title'=>$title,
			'description'=>$description
		];

		$saveTask = $this->taskService->inputSubtask($mongoTasks, $subtasks);


		return response()->json([
			"message" => "berhasil membuat sub task baru dengan id $taskId"
		]);
	}

	// TODO deleteSubTask() - completed
	public function deleteSubtask(Request $request)
	{
		// $mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'=>'required',
			'subtask_id'=>'required'
		]);

		$taskId = $request->post('task_id');
		$subtaskId = $request->post('subtask_id');
		$mongoTasks = $this->taskService->getById($taskId);

		if(!$mongoTasks)
		{
			return response()->json([
				"message"=> "Task ".$taskId." tidak ada"
			], 401);
		}

		$subtasks = isset($mongoTasks['subtasks']) ? $mongoTasks['subtasks'] : [];

		// Pencarian dan penghapusan subtask
		$subtasks = array_filter($subtasks, function($subtask) use($subtaskId) {
			if($subtask['_id'] == $subtaskId)
			{
				return false;
			} else {
				return true;
			}
		});
		$subtasks = array_values($subtasks);

		$saveTask = $this->taskService->deleteSubtask($mongoTasks, $subtasks);
		$task = $this->taskService->getById($taskId);

		return response()->json([
			"message" => "berhasil menghasil sub task dengan id $taskId"
		]);
	}

}