@php
use Carbon\Carbon;
@endphp

<html>

<head>
    <style>
        /** Define the margins of your page **/
        /** Define the margins of your page **/
        @page {
            margin: 150px 40px;
        }

        header {
            position: fixed;
            top: -120;
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
            bottom: -130px;
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

        .gray-background{
            background-color:#e0e0e0;
            border:2px solid #6f6f6f;
        }
        .bottom_border{
            border-bottom: 1px solid #000;
            width: max-content;
        }
        .m-0{
            margin:0;
        }
        .mb-10{
            margin-bottom: 10px !important;
          
        }
        .min-width200{
            min-width: 200px;
        }
        .left-display{
            margin-left: 0!important;
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
// dd($order);
$date = Carbon::createFromFormat('Y-m-d', $order->delivery_date)->format('l, m/d/Y');


@endphp

    <!-- Define header and footer blocks before your content -->
    <header>
        <div class="pagenum"></div>                                                                                                                                                                                                                                                                                 

        <table class="header-table">
            <tr>
                <td class="left">
                   <h3>WORK TICKET</h3>
                </td>
                <td colspan="2" class="left">
                  
                  <p><b>#{{$order->id}}-{{$packaging[0]->slip_sequence}}</b></p>
                </td>
             

                <td colspan="2"  >
                  {{-- <img src="http://127.0.0.1:8000/admin/paymentprofile/customers/orders/view/643" alt="barcode image"> --}}
                  {!! $barcode !!}
                  <P class="left">{{$barcode_no}}</P>
                </td>
            </tr>

        </table>
        <table>
            <tr>
                <td class="min-width200"><b>Order</b></td>
                <td>{{$order->id}}</td>
            </tr>
        </table>
    </header>

    <footer>
        <p class="left">{{ now()->format('m/d/Y g:i A') }}</p>
       
       
       
    </footer>
    <!-- Wrap the content of your PDF inside a main tag -->
    <main>



        <table>
            <tr>
                <td>
                    <table>
                        <tr><td  class="min-width200"><b>Location</b></td></tr>
                        <tr><td  class="min-width200"><b>Date</b></td></tr>
                        <tr><td  class="min-width200"><b>Time</b></td></tr>
                        <tr><td  class="min-width200"><b>Tail</b></td></tr>
                        <tr><td  class="min-width200"><b>Handler Agent Name</b></td></tr>
                        <tr><td  class="min-width200"><b>Handler Agent Mobile No.</b></td></tr>
                        <tr><td  class="min-width200"><b>Handler Agent PPR Permit</b></td></tr>
                        
                    </table>
                </td>
                <td>
                    <table class="gray-background">
                      
                        <tr><td>{{$addresses[0]->airport_name}}</td></tr>
                        <tr><td>{{$date}}</td> </tr>
                        <tr><td>{{$order->delivery_time}}</td></tr>
                        <tr><td>{{$order->fbo_tail_number}}</td></tr>
                        <tr><td>{{ isset($order->Name) ? $order->Name : 'N/A' }}</td></tr>
                        <tr><td>{{ isset($order->Mobile) ? $order->Mobile : 'N/A' }}</td></tr>
                        <tr><td>{{ isset($order->PPR_Permit) ? $order->PPR_Permit : 'N/A' }}</td></tr>
                        
                    </table> 
                    </td>
            </tr>
           

        </table>

       
       

            <table cellspacing="10">
                <tr>
                    <th ><p class="bottom_border">Quantity</p></th>
                    <th > <p class="bottom_border left">Item Description</p></th>
                </tr>
                @foreach ($dataArray as $item_detail)
            
                <tr class="mb-10" colspan="2">
                   {{-- @dd($dataArray); --}}
                    <td>{{$item_detail['qty']}}</td>
                    <td>
                       
                        <p class="m-0"><b>Item Name:</b> {{$item_detail['product_name']}}</p>
                        <p class="m-0"><b>Item Description:</b> {{$item_detail['product_description']}} </p>
                      
                        @if(isset($item_detail['additional']))
                        @php
                        $dataArray = json_decode($item_detail['additional'], true);
                        if(isset($dataArray['special_instruction'])){
                            $specialInstruction = $dataArray['special_instruction'];
                            @endphp
                                    <p class="m-0"><b>Special Instruction:</b> {{ $specialInstruction }} </p>
                            @php
                                }
                            @endphp
                    
                             
                      
                      
                      
                     
                          @endif
                    </td>
                </tr>
                @endforeach
                
            </table>

      
    </main>
</body>

</html>
