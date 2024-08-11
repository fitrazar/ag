<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        body {
            font-family: Helvetica;
            -webkit-font-smoothing: antialiased;
        }


        /* Table Styles */

        .table-wrapper {
            margin: 10px 70px 70px;
            box-shadow: 0px 35px 50px rgba(0, 0, 0, 0.2);
        }

        .fl-table {
            border-radius: 5px;
            font-size: 12px;
            font-weight: normal;
            border: none;
            border-collapse: collapse;
            width: 130%;
            white-space: nowrap;
            background-color: #f0f0f0;
            position: relative;
            top: 10px;
            left: -80px;
            font-size: 1rem;
        }

        .fl-table td,
        .fl-table th {
            text-align: center;
            padding: 8px;
        }

        .fl-table td {
            border-right: 1px solid #f8f8f8;
            font-size: 12px;
        }

        .fl-table thead th {
            color: #ffffff;
            background: #324960;
        }
    </style>
</head>

<body>

    <div class="table-wrapper">
        <table class="fl-table">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Kode</th>
                    <th scope="col">Guru</th>
                    <th scope="col">Mapel</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($workings as $working)
                    <tr>
                        <td scope="row">{{ $loop->iteration }}
                        </td>
                        <td>
                            {{ $working->code }}
                        </td>
                        <td>
                            {{ $working->teacher->name }}
                        </td>
                        <td>
                            {{ $working->subject->name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
