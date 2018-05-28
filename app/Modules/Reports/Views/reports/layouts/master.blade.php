<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>
        @page {
            margin: 25px;
        }

        body {
            color: #001028;
            background: #FFFFFF;
            font-family: DejaVu Sans, Helvetica, sans-serif;
            font-size: 12px;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        h1, h2, h3, h4, h5 {
            color: #5D6975;
            text-align: center;
        }

        h1 {
            font-size: 2.8em;
            line-height: 1.4em;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        th {
            padding: 5px 10px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 10px;
        }

        table.alternate tr:nth-child(even) td {
            background: #F5F5F5;
        }

        th.amount, td.amount {
            text-align: right;
        }

        .total {
            text-align: right;
            color: #5D6975;
            font-weight: bold;
        }

    </style>
</head>
<body>

@yield('content')

</body>
</html>