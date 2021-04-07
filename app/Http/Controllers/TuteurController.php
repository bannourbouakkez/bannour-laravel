<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use App\Models\Date;
use App\Models\Session;
use App\Models\User;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;

class TuteurController extends Controller
{

    //auth(XXX)->user()->id


    public function DateFormYMD($date)
    {
        $date = Carbon::parse($date);
        $date = $date->format('Y-m-d');
        return $date;
    }
    public function TimeFormHM($time)
    {
        $time = Carbon::parse($time);
        $time = $time->format('H:i');
        return $time;
    }

    public function addDisponibilite(Request $request)
    {
        $success = true;
        $msg = "La periode est modifie avec succes";

        $range = $request->input('range');
        $dates = $request->input('dates');

        $start = $this->DateFormYMD($range['start']);
        $end = $this->DateFormYMD($range['end']);

        $intersection = $this->isThereAnyIntersectionWithAnOtherPeriode(0, $start, $end);
        if ($intersection->bool) {
            $success = false;
            $msg = "Le nouveau periode s'intersectionne avec un autre periode [ " . $intersection->periodesIDs . " ]";
            return response()->json(['success' => $success, 'msg' => $msg]);
        }

        $Periode = Periode::create([
            'user_id' => auth()->user()->id,
            'start' => $start,
            'end' => $end
        ]);

        if (!$Periode) {
            $success = false;
        } else {
            $PeriodeID = $Periode->PeriodeID;
        }


        foreach ($dates as $date) {
            $date = $this->DateFormYMD($date['date']);
            $Date = Date::create([
                'periode_id' => $PeriodeID,
                'date' => $date
            ]);
            if (!$Date) {
                $success = false;
            }
        }

        $periode = Periode::where('PeriodeID', '=', $PeriodeID)->with('dates')->get();

        return response()->json(['success' => $success, 'periode' => $periode[0],'msg'=>$msg]);
    }

    public function editPeriode($PeriodeID, Request $request)
    {
        $success = true;
        $msg = "La periode est modifie avec succes";

        $range = $request->input('range');
        $dates = $request->input('dates');
        $dateToDelete = $request->input('dateToDelete');

        $start = $this->DateFormYMD($range['start']);
        $end = $this->DateFormYMD($range['end']);

        //if ($this->isRangeUpdated($periode, $start, $end)) {

        $intersection = $this->isThereAnyIntersectionWithAnOtherPeriode($PeriodeID, $start, $end);
        if ($intersection->bool) {
            $success = false;
            $msg = "Le nouveau periode s'intersectionne avec un autre periode [ " . $intersection->periodesIDs . " ]";
            return response()->json(['success' => $success, 'msg' => $msg]);
        }

        $outDates = $this->OutOfNewRangeDates($PeriodeID, $start, $end);

        if ($outDates->bool) {
            $msg .= " , Dates en dehors de nouveau intervalle ont ete supprimes : [ " . $outDates->datesIDs . " ]";
            foreach ($outDates->dates as $date) {
                Date::where('DateID', '=', $date->DateID)->delete();
            }
        }



        //}

        Periode::where('PeriodeID', '=', $PeriodeID)->update([
            'start' => $start,
            'end' => $end
        ]);

        foreach ($dates as $date) {
            if (!$date['DateID']) {
                Date::create([
                    'periode_id' => $PeriodeID,
                    'date' => $date['date']
                ]);
            }
        }

        foreach ($dateToDelete as $DateID) {
            Date::where('DateID', '=', $DateID)->delete();
        }

        $nvPeriode = Periode::where('PeriodeID', '=', $PeriodeID)->with('dates')->get();

        return response()->json(['success' => $success, 'periode' => $nvPeriode[0], 'msg' => $msg]);
    }

    public function isThereAnyIntersectionWithAnOtherPeriode($PeriodeID, $start, $end)
    {

        $start = $this->DateFormYMD($start);
        $end = $this->DateFormYMD($end);

        $periodes = Periode::where('user_id', '=', auth()->user()->id)
            ->where('periodes.PeriodeID', '<>', $PeriodeID)
            ->Where(function ($query) use ($start, $end) {
                return $query
                    ->Where(function ($query) use ($start, $end) {
                        return $query->where('periodes.start', '>=', $start)
                            ->where('periodes.start', '<=', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {
                        return $query->where('periodes.end', '>=', $start)
                            ->where('periodes.end', '<=', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {

                        return $query->where('periodes.start', '>=', $start)
                            ->where('periodes.end', '<=', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {
                        return $query->where('periodes.start', '<=', $start)
                            ->where('periodes.end', '>=', $end);
                    });
            })
            ->select('periodes.PeriodeID')
            ->get();

        $IDs = "";
        $count = count($periodes);
        if ($count) {
            $IDs = "";
            foreach ($periodes as $periode) {
                $IDs .= " #" . $periode->PeriodeID . " ";
            }
        }


        $result = new \stdClass();
        $result->bool = $count;
        $result->periodesIDs = $IDs;
        return $result;
    }


    public function isThereAnyIntersectionWithAnOtherSession($SessionID, $start, $end)
    {

        //$start = $this->DateFormYMD($start);
        //$end = $this->DateFormYMD($end);

        $start = new DateTime($start->format('Y-m-d H:i'));
        $end = new DateTime($end->format('Y-m-d H:i'));


        $sessions = Session::where('sessions.SessionID', '<>', $SessionID)
            ->join('dates', function ($join) {
                $join->on('sessions.date_id', '=', 'dates.DateID');
            })
            ->join('periodes', function ($join) {
                $join->on('dates.periode_id', '=', 'periodes.PeriodeID');
            })
            ->where('periodes.user_id', '=', auth()->user()->id)
            ->Where(function ($query) use ($start, $end) {
                return $query
                    ->Where(function ($query) use ($start, $end) {
                        return $query->where('sessions.timeStart', '>', $start)
                            ->where('sessions.timeStart', '<', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {
                        return $query->where('sessions.timeEnd', '>', $start)
                            ->where('sessions.timeEnd', '<', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {

                        return $query->where('sessions.timeStart', '>', $start)
                            ->where('sessions.timeEnd', '<', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {
                        return $query->where('sessions.timeStart', '<', $start)
                            ->where('sessions.timeEnd', '>', $end);
                    });
            })
            ->select('sessions.SessionID')
            ->get();

        $IDs = "";
        $count = count($sessions);
        if ($count) {
            $IDs = "";
            foreach ($sessions as $session) {
                $IDs .= " #" . $session->SessionID . " ";
            }
        }


        $result = new \stdClass();
        $result->bool = $count;
        $result->sessionsIDs = $IDs;
        return $result;
    }



    public function OutOfNewRangeDates($PeriodeID, $start, $end)
    {

        $start = $this->DateFormYMD($start);
        $end = $this->DateFormYMD($end);

        $dates = Date::
            join('periodes', function ($join) {
                $join->on('dates.periode_id', '=', 'periodes.PeriodeID');
            })
            ->where('periodes.user_id', '=', auth()->user()->id)
            ->where('dates.periode_id', '=', $PeriodeID)
            ->Where(function ($query) use ($start, $end) {
                return $query
                    ->whereDate('dates.date', '<', $start)
                    ->orWhereDate('dates.date', '>', $end);
            })
            //->Where('dates.date', '<', $start)
            //->orWhere('dates.date', '>', $end)
            ->select('dates.DateID')
            ->get();


        $datesIDs = "";
        $count = count($dates);
        if ($count) {
            foreach ($dates as $date) {
                $datesIDs .= " #" . $date->DateID . " ";
            }
        }

        $result = new \stdClass();
        $result->bool = $count;
        $result->dates = $dates;
        $result->datesIDs = $datesIDs;
        return $result;
    }




    public function deletePeriode($PeriodeID)
    {
        $success = true;
        $supression = Periode::where('PeriodeID', '=', $PeriodeID)->delete();
        if (!$supression) {
            $success = false;
        }
        return response()->json(['success' => $success]);
    }


    public function getPeriodes()
    {
        $UserID = auth()->user()->id;
        $periodes = User::find($UserID)->periodes;
        return response()->json(['periodes' => $periodes]);
    }

    public function getPeriodesWithDates()
    {
        $periodes = Periode::where('user_id','=',auth()->user()->id)->with('dates')->get();
        return response()->json($periodes->toArray());
    }

    public function getDatesByPeriodeID($PeriodeID)
    {
        $dates = Periode::find($PeriodeID)->dates;
        return response()->json(['dates' => $dates]);
    }

    public function getSessionsByDateID($DateID)
    {
        $sessions = Date::find($DateID)->sessions;
        return response()->json(['sessions' => $sessions]);
    }

    public function getDateByID($DateID)
    {
        $date = Date::where('DateID', '=', $DateID)->first();
        $sessions = Date::find($DateID)->sessions;
        return response()->json(['DateID' => $DateID, 'date' => $date, 'datesessions' => $sessions]);
    }

    public function addSession(Request $request)
    {
        $success = true;
        $DateID = $request->input('DateID');
        $form = $request->input('form');


        $timeStart = new DateTime($form['timeStart']);
        $timeEnd = new DateTime($form['timeEnd']);

        $intersection = $this->isThereAnyIntersectionWithAnOtherSession(0, $timeStart, $timeEnd);
        if ($intersection->bool) {
            $success = false;
            $msg = "Le nouveau session s'intersectionne avec un autre session [ " . $intersection->sessionsIDs . " ]";
            return response()->json(['success' => $success, 'msg' => $msg]);
        }
        
        $session = Session::create([
            'date_id' => $DateID,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd
        ]);
        $session = Session::where('SessionID', '=', $session->SessionID)->first();

        if (!$session) {
            $success = false;
        }

        return response()->json(['success' => $success, 'DateID' => $DateID, 'session' => $session]);
    }

    public function editSession(Request $request)
    {

        $success = true;
        $msg = "La session est modifie avec succes";

        $SessionID = $request->input('SessionID');
        $form = $request->input('form');

        $timeStart = new DateTime($form['timeStart']);
        $timeEnd = new DateTime($form['timeEnd']);

        $session = Session::where('SessionID', '=', $SessionID)->first();

        $intersection = $this->isThereAnyIntersectionWithAnOtherSession($SessionID, $timeStart, $timeEnd);
        if ($intersection->bool) {
            $success = false;
            $msg = "Le nouveau session s'intersectionne avec un autre session [ " . $intersection->sessionsIDs . " ]";
            return response()->json(['success' => $success, 'msg' => $msg]);
        }

        $session = Session::where('SessionID', '=', $SessionID)->update(['timeStart' => $timeStart, 'timeEnd' => $timeEnd]);
        $session = Session::where('SessionID', '=', $SessionID)->first();

        return response()->json(['success' => $success, 'SessionID' => $SessionID, 'session' => $session, 'msg' => $msg]);
    }


    public function deleteSession($SessionID)
    {
        $success = true;
        $delete = Session::where('SessionID', '=', $SessionID)->delete();
        if (!$delete) {
            $success = false;
        }
        return response()->json(['success' => $success]);
    }


    public function getSessionByIDTest($SessionID)
    {
        $session = Session::where('SessionID', '=', $SessionID)->first();
        return response()->json(['session' => $session]);
    }

    public function getPeriodeWithDates($PeriodeID)
    {
        $periode = Periode::where('PeriodeID', '=', $PeriodeID)->first();
        $dates = Periode::find($PeriodeID)->dates;
        return response()->json(['start' => $periode->start, 'end' => $periode->end, 'dates' => $dates]);
    }

    public function getTuteurMesSessionsReserve()
    {
        $sessions=Session::join('dates', function ($join) {
            $join->on('sessions.date_id', '=', 'dates.DateID');
        })
        ->join('periodes', function ($join) {
            $join->on('dates.periode_id', '=', 'periodes.PeriodeID');
        })
        ->where('periodes.user_id','=',auth()->user()->id)
        ->where('sessions.isReserved','=',true)
        ->join('clientsessions', function ($join) {
            $join->on('clientsessions.session_id', '=', 'sessions.SessionID');
        })
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'clientsessions.user_id');
        })
         ->select('sessions.*','users.name')
        ->get();

        return response()->json(['sessions' =>$sessions]);

    }


    

}



 //$format = "Y-m-d h:i:s";
 //$date=date_format(date_create($date), $format);
