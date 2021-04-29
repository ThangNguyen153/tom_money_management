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
                    @foreach($daily_usages->all() as $daily_usage)
                        <tr>
                            <td>{{ $daily_usage->id }}</td>
                            <td>{{ $daily_usage->payment_method->name }}</td>
                            <td>{{ $daily_usage->usage_type->name }}</td>
                            <td>{{ $daily_usage->paid }}</td>
                            <td>{{ $daily_usage->extra }}</td>
                            <td>{{ $daily_usage->description }}</td>
                            <td>{{ $daily_usage->created_at }}</td>
                            <td>{{ $daily_usage->updated_at }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div style="float: left;width: 10%; padding: 10px; height: 300px">
            <h3>User Balance</h3>
            @if(isset($userPaymentMethods) && !empty(array($userPaymentMethods)))
                @foreach($userPaymentMethods->all() as $userPaymentMethod)
                    <p>{{ $userPaymentMethod->name }} : {{ $userPaymentMethod->amount }}</p>
                @endforeach
            @endif
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
