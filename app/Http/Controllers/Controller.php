<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Shift;

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
      // Retrieve Filtered Data From The Database
      $shifts = $this->retrieveData();
      // Build Shift By User Object
      $users = $this->buildUserByShift( $shifts->get() );
      // Build Totals By Day Object
      $totals = $this->buildTotalsPerDay( $shifts->get() );
      // Retrieve First and Last Employee
      $firstEmployee = min(array_keys($users));
      $lastEmployee = max(array_keys($users));
      // Build Minutes Working Alone By Day Object
      $minutesWorkingAlonePerDay = $this->buildWorkingAlonePerDay( $shifts->get(), $totals );

      return view('show',
        [
          'users' => $users,
          'totals' => $totals,
          'firstEmployee' => $firstEmployee,
          'lastEmployee' => $lastEmployee,
          'minutesWorkingAlonePerDay' => $minutesWorkingAlonePerDay,
        ]
      );
    }

    /**
     * Retrieving Minute of the day
     *
     * @return Int
     */
    public function getMinuteOfTheDay( String $time )
    {
      return (strtotime($time)-1518912000)/60;
    }

    /**
     * Retrieve Filtered Data From The Database
     *
     * @return Builder
     */
    public function retrieveData()
    {
      return Shift::whereNotNull('staffid')
        ->where('staffid', '<>', 0)
        ->where('slottype', 'shift')
        ->orderBy('staffid')
        ->orderBy('daynumber');
    }

    /**
     * Build Shift By User Object
     *
     * @return Array
     */
    public function buildUserByShift( Collection $shifts )
    {
      $users = [];
      foreach ( $shifts as $key => $shift ) {
        if( is_numeric($shift->getStaffId()) ) {
          $users[$shift->getStaffId()] = $users[$shift->getStaffId()] ?? [];
          if( is_numeric($shift->getDayNumber()) && $shift->getStartTime() && $shift->getEndTime() ) {
            $users[$shift->getStaffId()][$shift->getDayNumber()] = $shift->getStartTime().' - '.$shift->getEndTime();
          }
        }
      }
      return $users;
    }

    /**
     * Build Totals Per Day Object
     *
     * @return Array
     */
    public function buildTotalsPerDay( Collection $shifts )
    {
      $totals = [];
      foreach ( $shifts as $key => $shift ) {
        if( is_numeric($shift->getDayNumber()) && $shift->getStartTime() && $shift->getEndTime() ) {
          $totals[$shift->getDayNumber()] = $totals[$shift->getDayNumber()] ?? 0;
          $totals[$shift->getDayNumber()] +=
            abs( $this->getMinuteOfTheDay($shift->getEndTime()) - $this->getMinuteOfTheDay($shift->getStartTime()) );//converted in  minutes
        }
      }
      ksort($totals);
      return $totals;
    }

    /**
     * Build Minutes Working Alone Per Day Object
     *
     * @return Array
     */
    public function buildWorkingAlonePerDay( Collection $shifts, Array $totals )
    {
      $minutesWorkingAlonePerDay = [];
      foreach ($totals as $day => $total) {
        $minutesWorkingAlonePerDay[$day] = $minutesWorkingAlonePerDay[$day] ?? 0;
        $minutesInTheDay = [];

        foreach ($shifts as $shiftKey => $shift)
        {
          if($shift->getDayNumber() == $day) {
            $startMinute = $this->getMinuteOfTheDay( $shift->getStartTime() );
            $endMinute = $this->getMinuteOfTheDay( $shift->getEndTime() );

            if($startMinute <= $endMinute) {
              for ($i=$startMinute; $i < $endMinute ; $i++) {
                $minutesInTheDay[$i] = $minutesInTheDay[$i] ?? 0;
                $minutesInTheDay[$i] += 1;
              }
            } else {
              for ($i=$startMinute; $i <= 1439 ; $i++) {
                $minutesInTheDay[$i] = $minutesInTheDay[$i] ?? 0;
                $minutesInTheDay[$i] += 1;
              }
              for ($i=0; $i <= $endMinute ; $i++) {
                $minutesInTheDay[$i] = $minutesInTheDay[$i] ?? 0;
                $minutesInTheDay[$i] += 1;
              }
            }
          }
        }

        foreach ($minutesInTheDay as $key2 => $value2) {
          if($value2 == 1) {
            $minutesWorkingAlonePerDay[$day]++;
          }
        }
      }
      return $minutesWorkingAlonePerDay;
    }

}
