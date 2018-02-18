<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Rota Shift</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  </head>

  <body>
    <div class="card border-info m-3">
      <div class="card-header bg-info text-white">
        Staff Rota Shift
      </div>

      <div class="card-body">
        <table class="table table-striped border text-center">
          @foreach ( $users as $staffId => $shifts )
            <!-- Print Header Row -->
            @if ( $firstEmployee == $staffId )
              <thead>
                <tr>
                  <th class="bg-secondary text-white"></th>
                  @foreach ( $totals as $dayNumber => $totalPerDay )
                    <th class="bg-secondary text-white"> {{ $dayNumber }} </th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
            @endif

                <tr>
                  <td class="bg-secondary text-white"> {{ $staffId }} </td>
                  @foreach ( $totals as $dayNumber => $totalPerDay )
                    @if ( array_key_exists( $dayNumber, $shifts ) )
                      <td> {{ $shifts[$dayNumber] }} </td>
                    @else
                      <td></td>
                    @endif
                  @endforeach
                </tr>

                <!-- Print Totals Row -->
                @if ( $lastEmployee == $staffId )
                <tr>
                  <td class="bg-secondary text-white">Totals</td>
                  @foreach ( $totals as $dayNumber => $totalPerDay )
                    <td class="bg-secondary text-white">
                      {{ floor($totalPerDay/60) }} hrs and
                      {{ $totalPerDay-(floor($totalPerDay/60)*60) }} mins
                    </td>
                  @endforeach
                </tr>
                <tr>
                  <td class="bg-secondary text-white">Working Alone</td>
                  @foreach ( $minutesWorkingAlonePerDay as $day => $minutes )
                    <td class="bg-secondary text-white">
                      {{ $minutes }}
                    </td>
                  @endforeach
                </tr>
              </tbody>
            @endif
          @endforeach
        </table>
      </div>

      <div class="card-footer bg-info">
      </div>
    </div>
  </body>
</html>
