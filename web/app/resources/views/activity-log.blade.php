<table border="3">
    <thead>
    <tr>
        <th>Activity Name</th>
        <th>Causer</th>
        <th>Subject</th>
        <th>Action</th>
        <th>IP</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Updated At</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($activities) && !empty(array($activities)))
        @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->log_name }}</td>
                <td>{{ $activity->causer->email }}</td>
                @if($activity->log_name === 'User Balance')
                    <td>{{ $activity->subject->name }}</td>
                @endif
                <td>{{ $activity->getExtraProperty('action') }}</td>
                <td>{{ $activity->getExtraProperty('ip') }}</td>
                <td>{{ $activity->description }}</td>
                <td>{{ $activity->created_at }}</td>
                <td>{{ $activity->updated_at }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
{{ $activities->links('pagination::semantic-ui') }}
