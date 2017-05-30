<?php namespace App\Http\Controllers;

use App\Teacher;
use App\Course;
use Illuminate\Http\Request;

class TeacherCourseController extends Controller
{
	public function index($teacher_id)
	{
		$teacher = Teacher::find($teacher_id);

		if ($teacher)
		{
			$students = $teacher->courses;

			return $this->createSuccessResponse($students, 200);
		}

		return $this->createErrorMessage("Does not exists a teacher with the given id", 404);
	}

	public function store(Request $request, $teacher_id)
	{
		$teacher = Teacher::find($teacher_id);

		if ($teacher)
		{
			$this->_validateRequest($request);

			$course = Course::create(
				[
					'title'			=> $request->get('title'),
					'description'	=> $request->get('description'),
					'value'			=> $request->get('value'), 
					'teacher_id'	=> $teacher->id
				]
			);

			return $this->createSuccessResponse("The course with id {$course->id} has been created and associated with the teacher with id {$teacher->id}", 201);
		}

		return $this->createErrorMessage("The teacher with id {$teacher_id} does not exists", 404);
	}

	public function update(Request $request, $teacher_id, $course_id)
	{
		$teacher = Teacher::find($teacher_id);

		if ($teacher)
		{
			$course = Course::find($course_id);

			if ($course)
			{
				$this->_validateRequest($request);
				$course->title = $request->get('title');
				$course->description = $request->get('description');
				$course->value = $request->get('value');
				$course->teacher_id = $teacher_id;

				$course->save();

				return $this->createSuccessResponse("The course with id {$course->id} was updated", 200);
			}

			return $this->createErrorMessage("Does not exists a course with the given id {$course_id}", 404);
		}

		return $this->createErrorMessage("Does not exists a teacher with the given id {$teacher_id}", 404);
	}

	public function destroy($teacher_id, $course_id)
	{
		$teacher = Teacher::find($teacher_id);

		if ($teacher)
		{
			$course = Course::find($course_id);

			if ($course)
			{
				if ($teacher->courses()->find($course_id))
				{
					$course->students()->detach();

					$course->delete();

					return $this->createSuccessResponse("The course with id {$course_id} was removed", 200);
				}

				return $this->createErrorMessage("The course with id {$course->id} is not associated with the teacher with id {$teach_id}", 404);
			}

			return $this->createErrorMessage("Does not exists a course with the given id {$course_id}", 404);
		}

		return $this->createErrorMessage("Does not exists a teacher with the given id {$teacher_id}", 404);
	}

	private function _validateRequest($request)
	{
		$rules = [
			'title'			=> 'required',
			'description'	=> 'required',
			'value'			=> 'required|numeric',
		];

		$this->validate($request, $rules);
	}
}