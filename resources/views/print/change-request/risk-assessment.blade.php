<!DOCTYPE html>
<html>

    <head>
        <style>
            body {
                font-size: 10pt;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td,
            th {
                border: 1px solid #000;
                padding: 5px;
            }

            /* TABLE */
            .print-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                line-height: 1.3;
            }

            .print-table td,
            .print-table th {
                border: 1px solid #000;
                padding: 6px;
                vertical-align: top;
            }

            .no-border,
            .no-border td,
            .no-border th {
                border: none !important;
                padding: 6px !important;
            }
        </style>
    </head>

    <body>
        <div class="print-page">
            @include('print.change-request.partials.risk-assessment-content')
        </div>
    </body>

</html>
