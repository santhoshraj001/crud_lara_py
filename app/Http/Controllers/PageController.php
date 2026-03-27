<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    // this fetchData function just for get json data in postman
   public function fetchData(){
        $data = DB::table('employees')->get();
        return response()->json([
            'data' => $data
        ]);
    }


    public function index()
    {
        $employees = DB::table('employees')->get();
      
        return view('index', compact('employees'));  //compact is used to send data to the view
  
        
    }

    public function store(Request $request)
    {
        
        $salary = $request->salary;          //this salary value go to the command line of next line
        $command = "python " . base_path('python_script/bonus.py') . " " . $salary; //$salary; =  sends the salary value to the Python script

        $bonus = shell_exec($command);     //shell_exec() runs system command or run a terminal command
        // dd($bonus);
        $tax = trim(shell_exec("python " . base_path('python_script/tax.py') . " " . $salary));   //another one method for execute the python script with system command
        $grade = trim(shell_exec("python " . base_path('python_script/grade.py') . " " . $salary));

       
    $imageNames = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . rand(1000,9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imageNames[] = $imageName;
        }
    }
        DB::table('employees')->insert([
            'name' => $request->name,
            'images' => json_encode($imageNames),
            'email' => $request->email,
            'salary' => $salary,
            'bonus' => $bonus,
            'tax' => $tax,
            'grade' => $grade
        ]);

        return redirect()->back();
    }
    public function delete($id)
    {
        DB::table('employees')->where('id', $id)->delete();
        return redirect()->back();
    }

    public function edit($id)
    {
         $employees = DB::table('employees')->get();
        $editdata = DB::table('employees')->where('id', $id)->first();

        return view('index', compact('employees','editdata'));
    }

   public function update(Request $request, $id)
{
    $salary = $request->salary;

    $bonus = trim(shell_exec("python " . base_path('python_script/bonus.py') . " " . $salary));
    $tax = trim(shell_exec("python " . base_path('python_script/tax.py') . " " . $salary));
    $grade = trim(shell_exec("python " . base_path('python_script/grade.py') . " " . $salary));
     
    $imageNames = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . rand(1000,9999) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imageNames[] = $imageName;
        }
    }

    DB::table('employees')->where('id', $id)->update([
        
        'name' => $request->name,
        'images' => json_encode($imageNames),
        'email' => $request->email,
        'salary' => $salary,
        'bonus' => $bonus,
        'tax' => $tax,
        'grade' => $grade
    ]);

    return redirect('/std');
}

                            // THIS FOR CALENDAR
public function storeEvent(Request $request)
{   
        $start = Carbon::parse($request->start)->format('Y-m-d H:i:s');

    DB::table('cal_event')->insert([
        
        'title' => $request->title,
        'start_datetime' => $start,
        // 'end_datetime' => $request->startt
        

    ]);

    return response()->json(['status' => true]);
}

public function getEvents()
{
    $events = DB::table('cal_event')->get();

    $data = [];

    foreach($events as $event){
        $data[] = [
            'id' => $event->id,
            'title' => $event->title,
            // 'start' => $event->event_date,
            'start' => $event->start_datetime
        ];
    }

    return response()->json($data);
}

public function updateEvent(Request $request)
{
    DB::table('cal_event')
        ->where('id', $request->id)
        ->update([
            'title' => $request->title,
            'start_datetime' => $request->start
        ]);

    return response()->json(['status' => true]);
}
public function deleteEvent(Request $request)
{
    DB::table('cal_event')
        ->where('id', $request->id)
        ->delete();

    return response()->json(['status' => true]);
}
}