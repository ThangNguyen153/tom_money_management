<html>
<head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        window.jQuery || document.write("{{ asset('js/jquery-3.1.1.min.js') }}");
    </script>
</head>
<body>
<button><a href="{{ route('logout') }}">Log out</a></button>
<button><a href="{{ route('user-daily-usage') }}">Daily Usage</a></button>
<button><a href="{{ route('user-usage-statistics') }}">Statistic</a></button>
<button><a href="{{ route('user-activity-log') }}">Activity Log</a></button>
<div class="row">
    <div style="display: flex;">
        <div style="float: left;width: 40%; padding: 10px; height: 300px">
            <table border="3">
                <thead>
                    <tr>
                        <th>Daily Usage</th>
                        <th>Payment Method</th>
                        <th>Usage Type</th>
                        <th>Paid</th>
                        <th>Extra</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($daily_usages) && !empty(array($daily_usages)))
                    @foreach($daily_usages as $daily_usage)
                        <tr>
                            <td>{{ $daily_usage->id }}</td>
                            <td>{{ $daily_usage->payment_method->name }}</td>
                            <td>{{ $daily_usage->usage_type->name }}</td>
                            <td>{{ number_format($daily_usage->paid,3) }}</td>
                            <td>{{ number_format($daily_usage->extra,3) }}</td>
                            <td>{{ $daily_usage->description }}</td>
                            <td>{{ $daily_usage->created_at }}</td>
                            <td>{{ $daily_usage->updated_at }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ $daily_usages->links('pagination::semantic-ui') }}
        </div>
        <div style="float: left;width: 10%; padding: 10px; height: 300px">
            <h3>User Balance</h3>
            @if(isset($userPaymentMethods) && !empty(array($userPaymentMethods)))
                @foreach($userPaymentMethods->all() as $userPaymentMethod)
                    <p>{{ $userPaymentMethod->name }} : {{ number_format($userPaymentMethod->amount,3) }}</p>
                @endforeach
            @endif
            <h3>Total Usage Of Month: {{number_format($totalUsageOfMonth,3)}}</h3>
        </div>
        <div style="float: left;width: 40%; padding: 10px; height: 300px; max-height: 300px;">
            <h3>Usage Type List</h3>
            <div style="display: initial;">
                @if(isset($usagetypes) && !empty(array($usagetypes)))
                    @foreach($usagetypes->all() as $usagetype)
                        <p>{{ $usagetype->name }} : {{ $usagetype->slug }}</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
</body>
</html>
