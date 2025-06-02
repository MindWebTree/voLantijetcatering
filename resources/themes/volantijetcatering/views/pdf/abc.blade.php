@php
use Carbon\Carbon;
@endphp

<html>

<head>
    <style>
        /** Define the margins of your page **/
        /** Define the margins of your page **/
        @page {
            margin: 220px 40px;
        }

        header {
            position: fixed;
            top: -220px;
            left: 0px;
            right: 0px;
            /** Extra personal styles **/

            color: black;
            text-align: center;
            line-height: 35px;
        }

        .w-100 {
            width: 100%;
        }

        main {
            margin-top: 0;
        }

        header p {
            margin: 0;
            font-size: 13px;
            line-height: 18px;
            font-family: sans-serif;
            text-align: right;
        }

        header h2,
        header h1 {
            font-family: sans-serif;
            text-align: right;
            font-weight: 400;
            margin: 10px 0;
        }

        /* Added CSS for the table layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        header h2 {
            color: #aa1d25;
        }

        .header-table td {
            border: 1px solid white;
            padding: 5px;


        }
  

        header img {
            max-width: 100px;
        }

        header hr {
            border: 1px solid #aa1d25;
            width: 60%;
            margin-right: 0;
        }

        footer {
            position: fixed;
            bottom: -200px;
            left: 0px;
            right: 0px;
            margin: 0;
            /** Extra personal styles **/

            color: black;
            text-align: center;
            line-height: 35px;
            border-top: 2px solid #a7a7a7;
            padding-top: 20px;
        }

        footer p {
            text-align: center;
            margin: 0;
            font-size: 13px;
            line-height: 18px;
            font-family: sans-serif;
        }

        .left p,
        .left {
            text-align: left !important;
        }

        p,
        td,
        h1,
        h2,
        h3,
        th {
            font-family: sans-serif;
        }

        main p {
            font-family: sans-serif;
        }

        .left div {
            margin-top: 10px;
        }

        .center p {
            text-align: center;
        }

        .red-heading {
            color: #aa1d25;
            font-weight: 700;

        }

        .intro {
            margin: auto;
        }

        .intro td {
            width: 25%;
            max-width: 400px;
            padding: 0 10px;
            vertical-align: top;
        }

        .intro p {
            font-size: 17px;
            font-weight: 700;
            font-family: sans-serif;
        }

        .red-heading {
            font-size: 15px !important;
            /* font-weight: 600 !important; */
            /* font-family: sans-serif; */
        }

        main img {
            max-width: 100px;
    max-height: 50px;
    margin: auto;
    display: flex;
    margin: auto;
    display: flex;
    justify-content: center;
    align-items: center;
        }

        .notes {
            background-color: #e6e6e7;
            padding: 10px;
            border-left: 7px solid #c80f2e;
            margin-top: 20px;
        }

        .color-heading {
            padding: 10px;
            background-color: #fbe6e9;
            border-bottom: 2px solid #c80f2e;
            margin-top: 20px;
        }

        .red-para {
            color: #c80f2e;
            font-weight: 400;
        }

        .info-2 {
            background-color: #f6f6f7;
        }

        .info-2 td {
            border: none;
            padding: 10px;
        }

        .info-2 p {
            text-align: center;
        }

        .item {
            width: 100%;
        }

        .item td {
            text-align: center;
        }

        .item th {
            padding: 10px 10px;
        }

        .item td {
            padding: 5px;
        }

        .item-category {
            background-color: #f0f0f0;
            width: 100%;
        }

        .item-category td {
            text-align: left;
        }

        .item-name {
            font-weight: 700;
            text-align: left;
        }

        .item-desc {
            text-align: left;
        }

        .color-heading-light {
            background-color: #f0f0f0;
            padding: 10px;
            margin-top: 10px;
        }

        .cost {
            width: 100%;
            margin-top: 15px;
        }

        .cost td {
            padding: 5px;
        }

        .cost td {
            font-weight: 700;
        }

        .agent {
            width: 100%;
            margin-top: 10px;
        }

        .agent th,
        .agent td {
            text-align: left;
            padding: 5px;
        }

        mt-3 {
            margin-top: 30px;
        }

        .p-2 {
            padding: 10px;
        }

        .list li {
            padding: 5px 0;
            line-height: 23px;
            font-family: sans-serif;
        }

        .list {
            margin-top: 20px;
        }

        .page-break {
            page-break-before: always;
        }

        .pagenum:before {
            content: counter(page);
            text-align: right
        }

        .pagenum {
            text-align: right;
        }

        .button-pay {
            width: auto;
            padding: 9px 10px;
            background-color: #f84661 ;
            color: #fff;
            text-decoration: unset;
            text-transform: capitalize;
        }

        .notes-table {
            padding: 20px !important;
        }

        .notes-table td,
        .notes-table th {
            padding: 0;
        }
        .packaging-section p{
            margin: 0;
    padding: 10px;
    margin-bottom: 30px;
    text-align: left !important;
        }
        .intro td,.intro td p,.word-wrap td ,.word-wrap td p{
           word-wrap: break-word;
           overflow-wrap: anywhere;
        }
        .image-center td img{
           
            justify-content: center;
            margin: auto;
        }
        .intro td{
            max-width: 100%;
        }
        .intro td{
            text-align: center;
        }
        .intro td img{
            display: block;
                margin-left: auto;
                margin-right: auto;
                text-align: center;
        }
        .center-table td{
            min-width:150px !important;
        }
        .mt-5{
            margin: 20px 0;
        }
        .text-center{
            text-align: center
        }
        .right{
            text-align: right;
        }
    </style>
</head>

<body>
    @php 
//     $carbonDate = Carbon::parse($order->created_at );
//     $delivery_dates = Carbon::parse($order->delivery_date );


// // Separate date and time
// $Order_date = $carbonDate->isoFormat('DD MMMM YYYY'); // "2024-03-15"
// $delivery_dates_format=$delivery_dates->isoFormat('DD MMMM YYYY');
// $delivery_date=$delivery_dates->toDateString();
// $delivery_time=$delivery_dates->format('H:i');



@endphp

    <!-- Define header and footer blocks before your content -->
    <header>
        <div class="pagenum"></div>

        <table class="header-table">
            <tr>
                <td class="left">
                   <h3>WORK TICKET</h3>
                </td>
                <td colspan="2">
                  <p><b>Slip Number</b></p>
                </td>
                <td colspan="2">
                  <img src="" alt="barcode image">
                </td>
            </tr>

        </table>
        <table>
            <tr>
                <td>Order</td>
                <td>395284</td>
            </tr>
        </table>
    </header>

    <footer>
        <p>date</p>
       
       
       
    </footer>
    <!-- Wrap the content of your PDF inside a main tag -->
    <main>



        <table>
            <tr>
                <td>Company</td>
                <td>Flexjet</td>
            </tr>
            <tr>
                <td>Location</td>
                <td>SDL-ATLANTIC AVIATON (formerly Ross)</td>
            </tr>
            <tr>
                <td>
                    <table>
                        <tr><td>Location</td></tr>
                        <tr><td>Date</td></tr>
                        <tr><td>Time</td></tr>
                        <tr><td>Tail</td></tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>SDL-ATLANTIC AVIATON (formerly Ross)</tr>
                        <tr>Sunday, 03/03/2024 </tr>
                        <tr>7:30 AM</tr>
                        <tr>3712515</tr>
                    </table> 
                    </td>
            </tr>
           

        </table>

        <h4 class="border-heading">SPECIAL INSTRUCTIONS:</h4>
        <p>ALL CATERING BOXES MUST BE LABELED WITH FLEX JET AND FLIGHT
            ID #3712515
            </p>
            <p>REVISION-5- UPDATED ORDER 
            </p>

            <table>
                <tr>
                    <th>Quantity</th>
                    <th>Item Description</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>
                        <p>item Name: </p>
                        <p>item Description: </p>
                    </td>
                </tr>
            </table>

      
    </main>
</body>

</html>
