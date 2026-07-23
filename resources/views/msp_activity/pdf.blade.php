<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSP Activity PDF</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            width: 100%;
        }

        table {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h1>MSP Activity</h1>
    <!-- <table>
        <thead>
            <tr>
                <th rowspan="2">Month</th>
                @foreach ($activities as $key => $activity)
                <th colspan="2">{{ $activity->type }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($activities as $key => $activity)
                <th>Part.</th>
                <th>Perf.</th>
                @endforeach
            </tr>
            @foreach ($data as $k => $val)
            <tr>
                <th>{{ $k }}</th>
                @foreach ($val as $count)
                <td>{{ $count['total_participants'] }}</td>
                <td>{{ $count['total_performed'] }}</td>
                @endforeach
            </tr>

            @endforeach
        </tbody>
    </table> -->

    <table>
        <thead>
            <tr>
                <th rowspan="2">Month / Activity</th>
                <th colspan="2">April</th>
                <th colspan="2">May</th>
                <th colspan="2">June</th>
                <th colspan="2">July</th>
                <th colspan="2">August</th>
                <th colspan="2">September</th>
                <th colspan="2">October</th>
                <th colspan="2">November</th>
                <th colspan="2">December</th>
                <th colspan="2">January</th>
                <th colspan="2">February</th>
                <th colspan="2">March</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($data as $k => $val)
                <th>Part</th>
                <th>Act</th>
                @endforeach
            </tr>
            @foreach ($activities as $key => $activity)
            <tr>
                <th>{{ $activity->type }}</th>
                @foreach ($data as $k => $val)
                @foreach ($val as $count)
                @if($count['activity_name'] == $activity->type)
                <td>{{ $count['total_participants'] }}</td>
                <td>{{ $count['total_performed'] }}</td>
                @endif
                @endforeach
                @endforeach
            </tr>
            @endforeach
    </table>
</body>

</html>