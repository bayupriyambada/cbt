<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamGroup;
use App\Models\ExamStatus;
use App\Models\ExamSession;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExamSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get exam_sessions
        $exam_sessions = ExamSession::when(request()->q, function ($exam_sessions) {
            $exam_sessions = $exam_sessions->where('title', 'like', '%' . request()->q . '%');
        })->with('exam.classroom', 'exam.lesson', 'exam_groups')->latest()->paginate(5);

        //append query string to pagination links
        $exam_sessions->appends(['q' => request()->q]);

        //render with inertia
        return inertia('Admin/ExamSessions/Index', [
            'exam_sessions' => $exam_sessions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //get exams
        $exams = Exam::all();

        //render with inertia
        return inertia('Admin/ExamSessions/Create', [
            'exams' => $exams,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate request
        $request->validate([
            'title'         => 'required',
            'exam_id'       => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'token'         => 'required|string|max:6', // Tambahkan validasi token

        ]);

        //create exam_session
        ExamSession::create([
            'title'         => $request->title,
            'exam_id'       => $request->exam_id,
            'start_time'    => $request->start_time,
            'end_time'      => $request->end_time,
            'token'         => $request->token,
        ]);

        //redirect
        return redirect()->route('admin.exam_sessions.index');
    }

    public function generateToken($id)
    {

        $examSession = ExamSession::find($id);

        if (!$examSession) {
            return response()->json(['message' => 'Exam session not found'], 404);
        }

        $newToken = Str::random(6);

        // Update only the exam session with the new token
        $examSession->token = $newToken;
        $examSession->save();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $exam_session
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get exam_session
        $exam_session = ExamSession::with('exam.classroom', 'exam.lesson')->findOrFail($id);

        //get relation exam_groups with pagination
        $exam_session->setRelation('exam_groups', $exam_session->exam_groups()->with('student.classroom')->paginate(5));

        //render with inertia
        return inertia('Admin/ExamSessions/Show', [
            'exam_session'  => $exam_session,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //get exam_session
        $exam_session = ExamSession::findOrFail($id);

        //get exams
        $exams = Exam::all();

        //render with inertia
        return inertia('Admin/ExamSessions/Edit', [
            'exam_session'  => $exam_session,
            'exams'         => $exams,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamSession $exam_session)
    {
        //validate request
        $request->validate([
            'title'         => 'required',
            'exam_id'       => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required',
        ]);

        //update exam_session
        $exam_session->update([
            'title'         => $request->title,
            'exam_id'       => $request->exam_id,
            'start_time'    => date('Y-m-d H:i:s', strtotime($request->start_time)),
            'end_time'      => date('Y-m-d H:i:s', strtotime($request->end_time)),
        ]);

        //redirect
        return redirect()->route('admin.exam_sessions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get exam_session
        $exam_session = ExamSession::findOrFail($id);

        //delete exam_session
        $exam_session->delete();

        //redirect
        return redirect()->route('admin.exam_sessions.index');
    }

    /**
     * createEnrolle
     *
     * @param  mixed $exam_session
     * @return void
     */
    public function createEnrolle(ExamSession $exam_session)
    {
        //get exams
        $exam = $exam_session->exam;

        //get students already enrolled
        $students_enrolled = ExamGroup::where('exam_id', $exam->id)->where('exam_session_id', $exam_session->id)->pluck('student_id')->all();

        //get students
        $students = Student::with('classroom')->where('classroom_id', $exam->classroom_id)->whereNotIn('id', $students_enrolled)->get();

        //render with inertia
        return inertia('Admin/ExamGroups/Create', [
            'exam'          => $exam,
            'exam_session'  => $exam_session,
            'students'      => $students,
        ]);
    }

    /**
     * storeEnrolle
     *
     * @param  mixed $exam_session
     * @return void
     */
    public function storeEnrolle(Request $request, ExamSession $exam_session)
    {
        //validate request
        $request->validate([
            'student_id'    => 'required',
        ]);

        //create exam_group
        foreach ($request->student_id as $student_id) {

            //select student
            $student = Student::findOrFail($student_id);

            //create exam_group
            $exam = ExamGroup::create([
                'exam_id'         => $request->exam_id,
                'exam_session_id' => $exam_session->id,
                'student_id'      => $student->id,
            ]);

            ExamStatus::create([
                'student_id' => $student->id,
                'exam_session_id' => $exam_session->id,
                'token' => $exam->exam_session->token,
            ]);
        }

        //redirect
        return redirect()->route('admin.exam_sessions.show', $exam_session->id);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'status' => 'required|in:pending,completed,failed',
        ]);

        // Update atau buat status ujian untuk siswa
        $examStatus = ExamStatus::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'exam_session_id' => $request->exam_session_id,
            ],
            ['status' => $request->status]
        );

        return response()->json(['message' => 'Status berhasil diperbarui.', 'status' => $examStatus]);
    }

    public function destroyEnrolle(ExamSession $exam_session, ExamGroup $exam_group)
    {
        $exam_group->delete();
        // exam status with id delete
        $examStatus = ExamStatus::where('student_id', $exam_group->student_id)->where('exam_session_id', $exam_session->id)->first();

        if ($examStatus) {
            $examStatus->delete();
        }
        return redirect()->route('admin.exam_sessions.show', $exam_session->id);
    }
}
