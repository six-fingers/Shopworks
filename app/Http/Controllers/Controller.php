<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Show the the staff shifts asd requested.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
      $main_query = DB::table('rota_slot_staff')
        ->whereNotNull('staffid')
        ->where('staffid', '<>', 0)
        ->where('slottype', 'shift')
        ->orderBy('staffid')
        ->orderBy('daynumber');

      $users = [];
      $totals = [];

      foreach ( $main_query->get() as $key => $record ) {
        if( is_numeric($record->staffid) ) {

          $users[$record->staffid] = $users[$record->staffid] ?? [];
          if( is_numeric($record->daynumber) && $record->starttime && $record->endtime ) {
            $users[$record->staffid][$record->daynumber] = $record->starttime.' - '.$record->endtime;
            $totals[$record->daynumber] = $totals[$record->daynumber] ?? 0;
            $totals[$record->daynumber] +=
              round( abs( strtotime( $record->endtime ) - strtotime( $record->starttime ) ) / 3600,2);
          }
        }
      }

      ksort($totals);
      $firstEmployee = min(array_keys($users));
      $lastEmployee = max(array_keys($users));

      return view('show',
        [
          'users' => $users,
          'totals' => $totals,
          'firstEmployee' => $firstEmployee,
          'lastEmployee' => $lastEmployee,
        ]
      );

    }



}
