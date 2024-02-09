<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService {
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: untuk mengambil semua tasks di collection task
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan task
	 */
	public function addTask(array $data)
	{
		$taskId = $this->taskRepository->create($data);
		return $taskId;
	}

	/**
	 * NOTE: UNTUK mengambil data task
	 */
	public function getById(string $taskId)
	{
		$task = $this->taskRepository->getById($taskId);
		return $task;
	}

	/**
	 * NOTE: untuk update task
	 */
	public function updateTask( $editTask, array $formData)
	{
		if(isset($formData['title']))
		{
			$editTask['title'] = $formData['title'];
		}

		if(isset($formData['description']))
		{
			$editTask['description'] = $formData['description'];
		}

		$id = $this->taskRepository->save( $editTask);
		return $id;
	}

	/***
	 * NOTE: untuk hapus task
	 */
	public function deleteTask($getId)
	{	
		$delete = $this->taskRepository->deleteTask($getId);
		return $delete;

	}



	/**
	 * NOTE: untuk assignedTask
	 */
	public function updateAssigned($taskId, $assigned)
	{
		$taskId['assigned'] = $assigned; 
		$id = $this->taskRepository->save($taskId);
		return $id;
	}

	/**
	 * NOTE: untuk unassignedTask
	 */
	public function unassignedTask($taskId) 
	{
		$taskId['assigned'] = null;
		$id = $this->taskRepository->save($taskId);
		return $id;
		
	}
	
	/**
	 * NOTE : untu mengambil data id
	 */
	public function getIdSubtask()
	{
		$id = $this->taskRepository->mongoSubtask();
		return $id;
	}

		/**
	 * Untuk menginput dan menyimpan data subtask
	 */
	public function inputSubtask($subTask, $inputValue)
	{
		$subTask['subtasks'] = $inputValue;
		$saveTask = $this->taskRepository->save($subTask);
		return $saveTask;
	}
	
	/**
	 * Untuk menghapus subTasks
	 */

	 public function deleteSubtask($mongoTask, $subTask)
	 {
		$mongoTask['subtasks'] = $subTask;
		$saveTask = $this->taskRepository->save($mongoTask);
		return $saveTask;
	 }
	
}