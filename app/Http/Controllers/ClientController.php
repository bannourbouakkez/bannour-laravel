<?php

namespace App\Http\Controllers;

use App\Models\Clientsession;
use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\Date;
use App\Models\Session;
use App\Models\User;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    


    public function getTuteursByPeriodeRange(Request $request)
    {
        $range = $request->input('range');
        $start = app('App\Http\Controllers\CommuneFunctionsController')->DateFormYMD($range['start']);
        $end = app('App\Http\Controllers\CommuneFunctionsController')->DateFormYMD($range['end']);
        $tuteurs = User::
        join('periodes', function ($join) {
            $join->on('periodes.user_id', '=', 'users.id');
        })
        ->join('dates', function ($join) {
            $join->on('dates.periode_id', '=', 'periodes.PeriodeID');
        })

        ->leftjoin('sessions', function ($join) {
            $join->on('sessions.date_id', '=', 'dates.DateID');
        })
        ->leftjoin('clientsessions', function ($join) {
            $join->on('clientsessions.session_id', '=', 'sessions.SessionID');
        })

        ->Where(function ($query)  {
            return $query
                ->where('sessions.isReserved','=',false)
                ->orWhere(function ($query) {
                    return $query
                        ->where('clientsessions.user_id', '=', auth()->user()->id)
                        ->Where('sessions.isReserved', '=',true);
                });
        })


        ->whereBetween('dates.date', [$start, $end])
        ->select('users.*')
        ->distinct('users.id')
        ->get();
        return response()->json(['tuteurs'=>$tuteurs]);
    }



    public function getSessionsByRangeAndByTuteurID(Request $request)
    {
        $TuteurID = $request->input('TuteurID');
        $tuteur = User::where('id','=',$TuteurID)->first();
        $start = app('App\Http\Controllers\CommuneFunctionsController')->DateFormYMD($request->input('start'));
        $end = app('App\Http\Controllers\CommuneFunctionsController')->DateFormYMD($request->input('end'));

        $sessions = Session::
        join('dates', function ($join) {
            $join->on('dates.DateID', '=', 'sessions.date_id');
        })
        ->join('periodes', function ($join) {
            $join->on('periodes.PeriodeID', '=', 'dates.periode_id');
        })
        ->leftjoin('clientsessions', function ($join) {
            $join->on('clientsessions.session_id', '=', 'sessions.SessionID');
        })
        ->where('periodes.user_id','=',$TuteurID)

        
        ->Where(function ($query)  {
            return $query
                ->where('sessions.isReserved','=',false)
                ->orWhere(function ($query) {
                    return $query
                        ->where('clientsessions.user_id', '=', auth()->user()->id)
                        ->Where('sessions.isReserved', '=',true);
                });
        })
        
        
       
        ->whereBetween('dates.date', [$start, $end])
        //->select('sessions.*','clientsessions.ClientSessionID')
        ->get();

        return response()->json(['sessions'=>$sessions,'tuteur'=>$tuteur]);
    }

    public function reserver($SessionID)
    {
        $success=true;
        $Clientsession=Clientsession::create([
            'session_id'=>$SessionID,
            'user_id'=>auth()->user()->id
        ]);
        if($Clientsession)
        Session::where('SessionID','=',$SessionID)->update(['isReserved'=>true]);

        if(!$Clientsession){
          $success=false;
        }
        return response()->json(['success'=>$success]);
    }

    public function annulerReservation($SessionID)
    {
        $success=true;
        $supression=Clientsession::where('session_id','=',$SessionID)
        ->where('user_id','=',auth()->user()->id)->delete();

        if(!$supression){
          $success=false;
        }else{
        Session::where('SessionID','=',$SessionID)->update(['isReserved'=>false]);
        }
        return response()->json(['success'=>$success]);
    }


    public function getMesSessionsReserve(){
        $sessions=Clientsession::where('user_id','=',auth()->user()->id)
        ->join('sessions', function ($join) {
            $join->on('sessions.SessionID', '=', 'clientsessions.session_id');
        })
        ->get();
        return response()->json(['sessions'=>$sessions]);
    }
    

    

}
